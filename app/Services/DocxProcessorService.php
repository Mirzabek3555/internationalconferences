<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * DOCX → HTML konverter (formulalar saqlanadi)
 * 
 * Pipeline:
 * 1. DOCX ichidan OMML formulalarni topish va MathML/LaTeX ga aylantirish
 * 2. DOCX ni modifikatsiya qilish — formulalar o'rniga placeholder matn qo'yish
 * 3. Mammoth.js orqali modifikatsiya qilingan DOCX → HTML
 * 4. Placeholder larni formula HTML bilan almashtirish
 * 5. Kimyoviy formulalar → subscript HTML
 */
class DocxProcessorService
{
    private string $scriptsPath;
    private string $nodePath;

    public function __construct()
    {
        $this->scriptsPath = base_path('scripts');
        $this->nodePath = $this->findNodePath();
    }

    /**
     * Asosiy metod: DOCX faylni HTML ga aylantirish
     * Formulalar, jadvallar, rasmlar (linked + embedded) saqlanadi
     */
    public function processDocx(string $docxPath): array
    {
        if (!file_exists($docxPath)) {
            throw new \Exception('DOCX fayl topilmadi: ' . $docxPath);
        }

        // 0. Linked rasmlarni embed qilingan DOCX ga aylantirish
        $embeddedDocxPath = $this->embedLinkedImages($docxPath);
        $workingDocxPath = $embeddedDocxPath ?: $docxPath;

        // 1. OMML formulalarni DOCX ichidan olish va placeholder qo'yish
        $formulaData = $this->extractFormulasAndCreateModifiedDocx($workingDocxPath);
        $formulas = $formulaData['formulas'];
        $modifiedDocxPath = $formulaData['modified_docx_path'];

        // 2. Mammoth orqali DOCX → HTML (modifikatsiya qilingan DOCX ishlatiladi)
        $docxForMammoth = $modifiedDocxPath ?: $workingDocxPath;
        $html = $this->convertDocxToHtml($docxForMammoth);

        // 3. Placeholder larni formula HTML bilan almashtirish
        if (!empty($formulas)) {
            $html = $this->replacePlaceholdersWithFormulas($html, $formulas);
        }

        // 4. Kimyoviy formulalarni to'g'rilash (H2O → H₂O)
        $html = $this->processChemicalFormulas($html);

        // 5. HTML ni tozalash va optimallashtirish
        $html = $this->cleanHtml($html);

        // 6. HTML ichidagi broken linked rasmlarni base64 bilan almashtirish
        $html = $this->fixLinkedImagesInHtml($html);

        // Vaqtincha fayllarni o'chirish
        if ($modifiedDocxPath && file_exists($modifiedDocxPath)) {
            @unlink($modifiedDocxPath);
        }
        if ($embeddedDocxPath && file_exists($embeddedDocxPath)) {
            @unlink($embeddedDocxPath);
        }

        return [
            'html' => $html,
            'has_formulas' => !empty($formulas),
            'formula_count' => count($formulas),
        ];
    }

    /**
     * DOCX ichidagi linked (tashqi fayl yo'li bilan bog'langan) rasmlarni
     * embed qilib yangi DOCX fayl yaratish
     *
     * Word faylida rasm "Insert → Picture → Link" bilan qo'shilganda,
     * rasm DOCX ichiga saqlanmaydi — faqat fayl yo'li saqlanadi.
     * Bu metod o'sha rasmlarni o'qib, DOCX ichiga joylashtiradi.
     */
    private function embedLinkedImages(string $docxPath): ?string
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                return null;
            }

            // word/_rels/document.xml.rels — barcha bog'lanmalarni ko'rish
            $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
            $documentXml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($relsXml === false || $documentXml === false) {
                return null;
            }

            // Linked rasmlarni topish (Target o'zi fayl yo'li bo'lgan munosabatlar)
            $dom = new \DOMDocument();
            @$dom->loadXML($relsXml);
            $relationships = $dom->getElementsByTagName('Relationship');

            $linkedImages = [];
            foreach ($relationships as $rel) {
                $type = $rel->getAttribute('Type');
                $target = $rel->getAttribute('Target');
                $targetMode = $rel->getAttribute('TargetMode');
                $relId = $rel->getAttribute('Id');

                // Linked rasm: TargetMode="External" va image type
                if ($targetMode === 'External' && strpos($type, 'image') !== false) {
                    // File:/// protokolini olib tashlash
                    $filePath = preg_replace('/^file:\/\/\//i', '', $target);
                    $filePath = urldecode($filePath);
                    // Windows yo'li normalizatsiya
                    $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

                    if (file_exists($filePath)) {
                        $linkedImages[$relId] = [
                            'path' => $filePath,
                            'target' => $target,
                        ];
                    } else {
                        Log::warning('Linked rasm topilmadi: ' . $filePath);
                    }
                }
            }

            if (empty($linkedImages)) {
                // Linked rasm yo'q — hech narsa o'zgartirmasak ham bo'ladi
                return null;
            }

            Log::info('Linked rasmlar topildi', ['count' => count($linkedImages)]);

            // Yangi DOCX yaratish — asl DOCX dan nusxa
            $tempDir = storage_path('app/private/temp_word');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $newDocxPath = $tempDir . '/' . uniqid('embedded_') . '.docx';
            copy($docxPath, $newDocxPath);

            $zipNew = new \ZipArchive();
            if ($zipNew->open($newDocxPath) !== true) {
                return null;
            }

            // Yangilangan rels XML yaratish
            $newRelsXml = $relsXml;

            foreach ($linkedImages as $relId => $imgInfo) {
                $imgPath = $imgInfo['path'];
                $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
                if (empty($ext)) $ext = 'png';

                // Rasm mazmunini o'qish
                $imgData = file_get_contents($imgPath);
                if ($imgData === false) continue;

                // DOCX ichiga yangi ot bilan saqlash
                $mediaName = 'word/media/linked_' . $relId . '.' . $ext;
                $zipNew->addFromString($mediaName, $imgData);

                // Rels XML da External → Internal o'zgartirish
                // TargetMode="External" ni olib tashlash va Target ni media ichidagi yo'lga o'zgartirish
                $internalTarget = 'media/linked_' . $relId . '.' . $ext;

                // Regex bilan ushbu relId uchun Relationship elementini yangilash
                $newRelsXml = preg_replace(
                    '/(<Relationship[^>]*Id="' . preg_quote($relId, '/') . '"[^>]*)TargetMode="External"([^>]*)Target="[^"]*"([^>]*\/?>)/i',
                    '$1$2Target="' . $internalTarget . '"$3',
                    $newRelsXml
                );
                // Agar birinchi pattern ishlamasa, ikkinchi tartib bilan ham sinab ko'ramiz
                $newRelsXml = preg_replace(
                    '/(<Relationship[^>]*Id="' . preg_quote($relId, '/') . '"[^>]*)Target="[^"]*"([^>]*)TargetMode="External"([^>]*\/?>)/i',
                    '$1Target="' . $internalTarget . '"$2$3',
                    $newRelsXml
                );
            }

            $zipNew->addFromString('word/_rels/document.xml.rels', $newRelsXml);
            $zipNew->close();

            Log::info('Linked rasmlar embed qilindi', ['new_docx' => $newDocxPath, 'count' => count($linkedImages)]);
            return $newDocxPath;

        } catch (\Exception $e) {
            Log::warning('embedLinkedImages xatosi: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * HTML ichidagi broken linked rasmlarni (fayl yo'li ko'rsatilgan src) base64 ga o'girish
     * Bu fallback: agar embedLinkedImages ishlamasa, bu metod rasmlarni to'g'rilaydi.
     */
    private function fixLinkedImagesInHtml(string $html): string
    {
        // <img src="C:\Users\...\ file.jpg"> yoki <img src="file:///C:/..."> ni topish
        // is — case-insensitive + dotall (newline ni ham qamrab olsin)
        return preg_replace_callback(
            '/<img\s([^>]*?)src="([^"]+)"([^>]*?)\/?>|<img\s([^>]*?)src=\'([^\']+)\'([^>]*?)\/?>|<img([^>]*?)src="([^"]+)"([^>]*?)>/is',
            function ($matches) {
                // Qaysi guruhlar to'ldirilganini aniqlash
                if (!empty($matches[2])) {
                    $before = $matches[1] ?? '';
                    $src    = $matches[2];
                    $after  = $matches[3] ?? '';
                } elseif (!empty($matches[5])) {
                    $before = $matches[4] ?? '';
                    $src    = $matches[5];
                    $after  = $matches[6] ?? '';
                } elseif (!empty($matches[8])) {
                    $before = $matches[7] ?? '';
                    $src    = $matches[8];
                    $after  = $matches[9] ?? '';
                } else {
                    return $matches[0];
                }

                // Agar src allaqachon base64 yoki http bo'lsa — o'zgartirmaslik
                if (strpos($src, 'data:') === 0 || strpos($src, 'http') === 0) {
                    return $matches[0];
                }

                // file:/// protokolini olib tashlash
                $filePath = preg_replace('/^file:\/\/\//i', '', $src);
                $filePath = urldecode($filePath);
                // Windows yo'li normalizatsiya
                $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

                if (!file_exists($filePath)) {
                    // Rasm topilmadi — placeholder
                    Log::warning('HTML da broken rasm: ' . $filePath);
                    return '<img ' . $before . 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" style="border:1px dashed #ccc;min-width:60px;min-height:40px;" />';
                }

                // Rasmni o'qib base64 ga aylantirish
                $imgData = @file_get_contents($filePath);
                if ($imgData === false) {
                    return $matches[0];
                }

                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $mimeMap = [
                    'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
                    'png'  => 'image/png',  'gif'  => 'image/gif',
                    'bmp'  => 'image/bmp',  'webp' => 'image/webp',
                    'svg'  => 'image/svg+xml',
                    'tiff' => 'image/tiff', 'tif'  => 'image/tiff',
                ];
                $mime   = $mimeMap[$ext] ?? 'image/jpeg';
                $base64 = base64_encode($imgData);

                Log::info('Linked rasm base64 ga aylantildi: ' . basename($filePath));
                return '<img ' . $before . 'src="data:' . $mime . ';base64,' . $base64 . '" ' . $after . '>';
            },
            $html
        );
    }

    /**
     * DOCX dan formulalarni chiqarib olish va placeholder li yangi DOCX yaratish
     * 
     * Mammoth <m:oMath> elementlarni tanimaydi — ularni o'tkazib yuboradi.
     * Shuning uchun biz DOCX XML ni o'zgartiramiz:
     * - <m:oMath> / <m:oMathPara> → {{FORMULA_0}}, {{FORMULA_1}}, ...
     * Mammoth bu placeholder matnni oddiy paragraf sifatida chiqaradi.
     * Keyin biz placeholder larni formula HTML bilan almashtiramiz.
     */
    public function extractFormulasAndCreateModifiedDocx(string $docxPath): array
    {
        $formulas = [];
        $modifiedDocxPath = null;

        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                Log::warning('DOCX faylni ZIP sifatida ochib bo\'lmadi: ' . $docxPath);
                return ['formulas' => [], 'modified_docx_path' => null];
            }

            $documentXml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($documentXml === false) {
                return ['formulas' => [], 'modified_docx_path' => null];
            }

            // OMML formulalarni topish va placeholder bilan almashtirish
            $formulaIdx = 0;
            $modifiedXml = $documentXml;

            // MAMMOTH FIX: Convert floating/wrapped images (<wp:anchor>) to inline (<wp:inline>)
            // Mammoth by default ignores <wp:anchor>. Renaming them to <wp:inline> allows Mammoth to extract them.
            $modifiedXml = str_replace(['<wp:anchor', '</wp:anchor>'], ['<wp:inline', '</wp:inline>'], $modifiedXml);

            // 1. Avval <m:oMathPara> elementlarni topish (display formulalar)
            $modifiedXml = preg_replace_callback(
                '/<m:oMathPara[^>]*>.*?<\/m:oMathPara>/is',
                function ($matches) use (&$formulas, &$formulaIdx) {
                    $ommlBlock = $matches[0];
                    $formulaHtml = $this->convertOmmlToHtml($ommlBlock);
                    
                    $placeholder = '{{FORMULA_' . $formulaIdx . '}}';
                    $formulas[$formulaIdx] = [
                        'placeholder' => $placeholder,
                        'html' => $formulaHtml,
                        'type' => 'display',
                    ];
                    $formulaIdx++;

                    // Placeholder ni w:r/w:t ichiga o'rash — mammoth uni matn sifatida chiqaradi
                    return '<w:r><w:t>' . $placeholder . '</w:t></w:r>';
                },
                $modifiedXml
            );

            // 2. Keyin <m:oMath> elementlarni topish (inline formulalar)
            $modifiedXml = preg_replace_callback(
                '/<m:oMath[^>]*>.*?<\/m:oMath>/is',
                function ($matches) use (&$formulas, &$formulaIdx) {
                    $ommlBlock = $matches[0];
                    $formulaHtml = $this->convertOmmlToHtml($ommlBlock);
                    
                    $placeholder = '{{FORMULA_' . $formulaIdx . '}}';
                    $formulas[$formulaIdx] = [
                        'placeholder' => $placeholder,
                        'html' => $formulaHtml,
                        'type' => 'inline',
                    ];
                    $formulaIdx++;

                    return '<w:r><w:t>' . $placeholder . '</w:t></w:r>';
                },
                $modifiedXml
            );

            if ($modifiedXml !== $documentXml) {
                // Modifikatsiya qilingan DOCX yaratish
                $tempDir = storage_path('app/private/temp_word');
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $modifiedDocxPath = $tempDir . '/' . uniqid('modified_') . '.docx';

                // Asl DOCX ni nusxalash
                copy($docxPath, $modifiedDocxPath);

                // Yangi XML ni yozish
                $zipMod = new \ZipArchive();
                if ($zipMod->open($modifiedDocxPath) === true) {
                    $zipMod->addFromString('word/document.xml', $modifiedXml);
                    $zipMod->close();
                    Log::info('DOCX modified (formulas or floating images)', [
                        'formulas_count' => count($formulas),
                        'modified_path' => $modifiedDocxPath,
                    ]);
                } else {
                    Log::warning('Modified DOCX ni ochib bo\'lmadi');
                    $modifiedDocxPath = null;
                }
            }
        } catch (\Exception $e) {
            Log::warning('OMML formulalarni olishda xatolik: ' . $e->getMessage());
        }

        return [
            'formulas' => $formulas,
            'modified_docx_path' => $modifiedDocxPath,
        ];
    }

    /**
     * OMML blokni HTML ga aylantirish
     * XSLT mavjud bo'lsa MathML, aks holda oddiy matn
     */
    private function convertOmmlToHtml(string $omml): string
    {
        // Avval XSLT orqali MathML ga aylantirish
        $xslPath = resource_path('xslt/OMML2MathML.xsl');

        if (file_exists($xslPath) && extension_loaded('xsl')) {
            try {
                $xmlString = '<?xml version="1.0" encoding="UTF-8"?>'
                    . '<root xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"'
                    . ' xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
                    . $omml
                    . '</root>';

                $xml = new \DOMDocument();
                $xml->loadXML($xmlString, LIBXML_NOERROR | LIBXML_NOWARNING);

                $xsl = new \DOMDocument();
                $xsl->load($xslPath);

                $processor = new \XSLTProcessor();
                $processor->importStyleSheet($xsl);

                $result = $processor->transformToXMl($xml);
                if ($result) {
                    if (preg_match('/<math[^>]*>.*<\/math>/is', $result, $m)) {
                        return '<span class="math-formula">' . $m[0] . '</span>';
                    }
                    return '<span class="math-formula">' . $result . '</span>';
                }
            } catch (\Exception $e) {
                Log::warning('XSLT konvertatsiyada xatolik: ' . $e->getMessage());
            }
        }

        // Fallback: OMML dan oddiy matnni chiqarib olish
        return $this->extractMathTextFromOmml($omml);
    }

    /**
     * OMML ichidan matnni oddiy ko'rinishda olish (fallback)
     * Formulani italik va qavs ichida ko'rsatadi
     */
    private function extractMathTextFromOmml(string $omml): string
    {
        $text = '';

        // m:t taglar ichidagi matnni olish
        preg_match_all('/<m:t[^>]*>([^<]*)<\/m:t>/i', $omml, $matches);
        if (!empty($matches[1])) {
            $text = implode('', $matches[1]);
        }

        if (empty($text)) {
            $text = strip_tags($omml);
            $text = preg_replace('/\s+/', ' ', trim($text));
        }

        if (empty($text)) {
            return '<em>[formula]</em>';
        }

        // Raqamlarni subscript/superscript bilan formatlash
        // Daraja: ^2, ^3, etc.
        $text = preg_replace('/\^(\d+)/', '<sup>$1</sup>', $text);
        // Indeks: _2, _3, etc.
        $text = preg_replace('/_(\d+)/', '<sub>$1</sub>', $text);

        return '<span class="math-formula" style="font-style: italic; font-family: \'Times New Roman\', serif;">' . htmlspecialchars($text, ENT_NOQUOTES) . '</span>';
    }

    /**
     * HTML ichidagi placeholder larni formula HTML bilan almashtirish
     */
    private function replacePlaceholdersWithFormulas(string $html, array $formulas): string
    {
        foreach ($formulas as $idx => $formula) {
            $placeholder = '{{FORMULA_' . $idx . '}}';
            $formulaHtml = $formula['html'];
            
            // Display formulalar uchun blok element
            if ($formula['type'] === 'display') {
                $formulaHtml = '<div class="formula-display" style="text-align: center; margin: 8pt 0; font-style: italic;">' . $formulaHtml . '</div>';
            }

            $html = str_replace($placeholder, $formulaHtml, $html);
            // HTML encoded versiya ham (mammoth ba'zan encode qiladi)
            $html = str_replace(htmlspecialchars($placeholder), $formulaHtml, $html);
        }

        return $html;
    }

    /**
     * Mammoth.js orqali DOCX → HTML
     */
    public function convertDocxToHtml(string $docxPath): string
    {
        $scriptPath = $this->scriptsPath . DIRECTORY_SEPARATOR . 'convert-docx.cjs';

        if (!file_exists($scriptPath)) {
            throw new \Exception('convert-docx.js skripti topilmadi');
        }

        $command = sprintf(
            '%s %s %s 2>&1',
            escapeshellarg($this->nodePath),
            escapeshellarg($scriptPath),
            escapeshellarg($docxPath)
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $result = implode("\n", $output);

        if ($returnCode !== 0) {
            Log::error('Mammoth konvertatsiyada xatolik', ['output' => $result]);
            throw new \Exception('DOCX → HTML konvertatsiya xatosi: ' . $result);
        }

        $json = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Mammoth javobini parse qilib bo\'lmadi: ' . $result);
        }

        if (isset($json['error'])) {
            throw new \Exception('Mammoth xatosi: ' . $json['error']);
        }

        // Ogohlantirishlarni log qilish
        if (!empty($json['messages'])) {
            foreach ($json['messages'] as $msg) {
                Log::info('Mammoth: ' . ($msg['message'] ?? ''), ['type' => $msg['type'] ?? 'info']);
            }
        }

        return $json['html'] ?? '';
    }

    /**
     * Kimyoviy formulalarni to'g'ri formatlash
     * H2O → H₂O, CO2 → CO₂, C6H12O6 → C₆H₁₂O₆
     */
    public function processChemicalFormulas(string $html): string
    {
        // HTML ni taglar va matnlarga ajratamiz, faqat matn qismiga regex qollaymiz
        // Bu <img src="data:image/..."> kabi attributlar ichidagi base64 kodlarini buzib qoyishini oldini oladi
        $parts = preg_split('/(<[^>]*>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        
        $result = '';
        foreach ($parts as $part) {
            if (str_starts_with($part, '<')) {
                $result .= $part;
            } else {
                // Faqat matn qismida H2O, CO2, P72 kabi kimyoviy element+raqam qolipini qidiramiz
                $result .= preg_replace_callback(
                    '/\b([A-Z][a-z]?)(\d+)(?=[A-Z\s,\.\)\(;:<\/]|$)/u',
                    function ($matches) {
                        return $matches[1] . '<sub>' . $matches[2] . '</sub>';
                    },
                    $part
                );
            }
        }

        return $result;
    }

    /**
     * HTML ni tozalash va optimallashtirish
     */
    private function cleanHtml(string $html): string
    {
        // Bo'sh paragraflarni olib tashlash
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        // Ortiqcha bo'shliqlarni tozalash
        $html = preg_replace('/\s{3,}/', '  ', $html);

        // Word-specific class nomlarini olib tashlash
        $html = preg_replace('/\s+class="MsoNormal"/', '', $html);
        $html = preg_replace('/\s+class="Mso[^"]*"/', '', $html);

        // Style attributlarni tozalash (mammoth qoldirishi mumkin)
        $html = preg_replace('/\s+style="[^"]*mso-[^"]*"/', '', $html);

        return trim($html);
    }

    /**
     * Node.js yo'lini topish
     */
    private function findNodePath(): string
    {
        $nodePath = env('NODE_PATH', '');
        if (!empty($nodePath) && file_exists($nodePath)) {
            return $nodePath;
        }

        $possiblePaths = [
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $output = [];
        exec('where node 2>&1', $output);
        if (!empty($output[0]) && file_exists(trim($output[0]))) {
            return trim($output[0]);
        }

        return 'node';
    }

    /**
     * KaTeX CSS yo'lini olish (PDF uchun inline qilish kerak)
     */
    public function getKatexCssPath(): string
    {
        $katexCss = base_path('node_modules/katex/dist/katex.min.css');
        if (file_exists($katexCss)) {
            return $katexCss;
        }
        return '';
    }

    /**
     * KaTeX CSS kontentini olish (inline uchun)
     */
    public function getKatexCssContent(): string
    {
        $cssPath = $this->getKatexCssPath();
        if (!empty($cssPath) && file_exists($cssPath)) {
            return file_get_contents($cssPath);
        }
        return '';
    }
}
