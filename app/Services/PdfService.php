<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Certificate;
use App\Models\Conference;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class PdfService
{
    /**
     * Premium sertifikat yaratish — TCPDF
     *
     * Dizayn (2-namuna rasmga o'xshash):
     *  • CHAP ~40%  — davlat manzara rasmi (to'liq), pastda QR + sayt linki
     *  • Diagonal   — accent rangli polygon (parallelogram)
     *  • O'NG ~60%  — oq fon: CERTIFICATE sarlavhasi (accent rangda), muallif, sarlavha, imzo
     *
     * A4 Landscape: 297 × 210 mm
     */
    /**
     * Premium sertifikat yaratish (Geometrik/Ribbon dizayn) — GD + FreeType (JPG)
     *
     * Dizayn (Sample asosida):
     *  • O'ng tomoni qiyshiq (slanted) oq panel.
     *  • Katta "Ribbon" (Lenta/O'q shaklida) sarlavha bloki (Davlatning Secondary rangi).
     *  • Qalin tashqi ramka (Davlatning Primary rangi).
     *  • Chap panelda davlat manzara rasmi to'liq COVER ko'rinishida.
     *  • Serif font (Times New Roman) bilan yozilgan ism-sharif.
     */
    public function generateCertificate(Article $article, Certificate $certificate): string
    {
        $article->load(['conference.country']);
        $country = $article->conference->country;
        $conference = $article->conference;

        // Davlat ranglarini oldindan aniqlash (banner va chegara uchun)
        $countryColorInfo = $this->getCountryColors($country->code ?? 'GB');
        [$pr, $pg, $pb] = $this->rgb($countryColorInfo['primary']);
        [$sr, $sg, $sb] = $this->rgb($countryColorInfo['secondary']);

        // O'lchamlar (A4 Landscape)
        $W = 2480;
        $H = 1754;

        $canvas = imagecreatetruecolor($W, $H);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // Ranglar (Yangi dizayn asosida)
        $cWhite = imagecolorallocate($canvas, 255, 255, 255);
        $cDarkRed = imagecolorallocate($canvas, 204, 0, 0);     // Qizil matnlar / CERTIFICATE (IFICAT)
        $cGreen = imagecolorallocate($canvas, 0, 153, 51);    // Yashil matnlar / ism / CERTIFICATE (CERT va E)
        $cDarkGreen = imagecolorallocate($canvas, 0, 102, 34);    // Alternativ qora-yashil
        $cBlack = imagecolorallocate($canvas, 0, 0, 0);
        $cGrey = imagecolorallocate($canvas, 100, 100, 100);
        $cBorder = imagecolorallocate($canvas, 180, 180, 180);

        // Fontlar
        $fd = 'C:/Windows/Fonts/';
        $fontR = file_exists($fd . 'arial.ttf') ? $fd . 'arial.ttf' : '';
        $fontB = file_exists($fd . 'arialbd.ttf') ? $fd . 'arialbd.ttf' : '';
        $fontI = file_exists($fd . 'ariali.ttf') ? $fd . 'ariali.ttf' : '';
        $fontBI = file_exists($fd . 'arialbi.ttf') ? $fd . 'arialbi.ttf' : '';
        $fontImpact = file_exists($fd . 'impact.ttf') ? $fd . 'impact.ttf' : $fontB;
        $fontGeorgiaB = file_exists($fd . 'georgiab.ttf') ? $fd . 'georgiab.ttf' : (file_exists($fd . 'timesbd.ttf') ? $fd . 'timesbd.ttf' : $fontB);

        // ════════════════════════════════════════════════════════════
        // 1. ASOSIY FON (Yangi tayyor JPG shabloni yuklash)
        // ════════════════════════════════════════════════════════════
        $templatePath = public_path('images/certificates/assets/template.jpg');
        if (file_exists($templatePath)) {
            $template = @imagecreatefromstring(file_get_contents($templatePath));
            if ($template) {
                imagecopyresampled($canvas, $template, 0, 0, 0, 0, $W, $H, imagesx($template), imagesy($template));
                imagedestroy($template);
            } else {
                imagefilledrectangle($canvas, 0, 0, $W, $H, $cWhite);
            }
        } else {
            imagefilledrectangle($canvas, 0, 0, $W, $H, $cWhite);
        }

        // Yangi shablonda rang qat'iy, shuning uchun "green" rangni o'zgartirishni to'xtatamiz.
        // $this->replaceGreenWithColor($canvas, $pr, $pg, $pb, $W, $H);

        // ════════════════════════════════════════════════════════════
        // 2. O'NG TOMONGA DAVLAT RASMINI CHIZISH (Polygon Mask orqali)
        // ════════════════════════════════════════════════════════════

        $bgImg = $this->getBackgroundImage($country->code ?? 'GB');
        if ($bgImg && file_exists($bgImg) && function_exists('imagecreatefromstring')) {
            $src = @imagecreatefromstring(file_get_contents($bgImg));
            if ($src) {
                // Yangi template asosida o'ng to'rtburchak + chap chevron edge
                // Nuqtalar: P1=(TopLeft), P2=(TopRight), P3=(BottomRight), P4=(BottomLeft), P5=(MiddleTip)
                
                // 1) O'ng yarmini ushlab turuvchi 3 taraflama dizayn ramkasini (Frame) chizish
                $framePoly = [1298, 0, 2480, 0, 2480, 1754, 1308, 1754, 1785, 872];
                $cStrokeCountry = imagecolorallocate($canvas, $pr, $pg, $pb);
                imagefilledpolygon($canvas, $framePoly, 5, $cStrokeCountry);

                // 2) Asosiy rasmni asosan Tepa, O'ng, Past tarafdan 30px kichraytirib chizamiz.
                // Uning chap tomoni xuddi avvalgidek kulrang lenta (grey chevron) chizig'iga ustma-ust tushadi.
                // Tepadan Y=30 ga pastlaganda, lineerlik bo'yicha X=1315 tushadi. Pastda Y=1724 da X=1324 tushadi.
                $innerPointsRaw = [1315, 30, 2450, 30, 2450, 1724, 1324, 1724, 1785, 872];
                $this->drawMaskedImage($canvas, $src, $innerPointsRaw);
                imagedestroy($src);
                
                // 2) Premium muhrni (seal) o'ng yuqori burchakka chizish
                $sealPath = public_path('images/certificates/assets/premium-seal-clean.png');
                if (file_exists($sealPath)) {
                    $seal = @imagecreatefrompng($sealPath);
                    if ($seal) {
                        $sealSize = 320; // Chiroyli ko'rinish uchun o'lcham
                        $sealX = 2450 - $sealSize - 10; // O'ng burchakdagi freymga taqab qo'yish
                        $sealY = 40; // Tepa burchakka taqab qo'yish
                        imagecopyresampled($canvas, $seal, $sealX, $sealY, 0, 0, $sealSize, $sealSize, imagesx($seal), imagesy($seal));
                        imagedestroy($seal);
                    }
                }
            }
        }

        // ════════════════════════════════════════════════════════════
        // 3. MATNLAR — Template ustiga to'g'ridan-to'g'ri yozish
        // ════════════════════════════════════════════════════════════

        // Navy ko'k rang — rasmdagi dizayn uchun
        $cWhite2   = imagecolorallocate($canvas, 255, 255, 255);
        $cGreen    = imagecolorallocate($canvas, 4, 95, 57);
        $cNavy     = imagecolorallocate($canvas, 20, 40, 90);
        $cGrey2    = imagecolorallocate($canvas, 90, 90, 120);

        // 4. MATNLAR tayyorgarligi — Mualliflarni alohida ajratib olib, tsikl bilan chizamiz
        $authorsList = [];
        $mainAuthor = mb_convert_case(trim($article->author_name ?? 'Author Name'), MB_CASE_TITLE, 'UTF-8');
        if (!empty($mainAuthor)) {
            $authorsList[] = $mainAuthor;
        }

        if (!empty($article->co_authors)) {
            $coAuthorsRaw = explode("\n", trim($article->co_authors));
            foreach ($coAuthorsRaw as $ca) {
                // Verguldan oldingi ismini ajratib olamiz (tashkilot shart emas)
                $nameParts = explode(',', $ca);
                $caName = mb_convert_case(trim($nameParts[0]), MB_CASE_TITLE, 'UTF-8');
                if (!empty($caName)) {
                    $authorsList[] = $caName;
                }
            }
        }

        // Agar hech qanday muallif bo'lmasa
        if (empty($authorsList)) {
            $authorsList[] = 'Author Name';
        }

        // ════════════════════════════════════════════════════════════
        // MULTI-PAGE PDF O'RNIGA ZIP YARATISH (Har bir muallifga alohida JPG sertifikat)
        // ════════════════════════════════════════════════════════════
        $generatedJpgs = [];
        $directory = Storage::disk('public')->path('certificates');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach ($authorsList as $authorName) {
            // Har bir sahifa uchun asosiy fonni nusxalaymiz
            $pageCanvas = imagecreatetruecolor($W, $H);
            imagecopy($pageCanvas, $canvas, 0, 0, 0, 0, $W, $H);

            $cy = 660;
            $cx = 80;
            $maxTextW = 1200;

            // 4.1 Muallif ismi — katta, navy, Georgia Bold, centerlangan
            $afs = 60;
            if (mb_strlen($authorName) > 35) $afs = 52;
            if (mb_strlen($authorName) > 45) $afs = 44;
            if ($fontGeorgiaB) {
                $cy = $this->gdTextMultiline($pageCanvas, $afs, $fontGeorgiaB, $cNavy, $authorName, $cx, $cy, $maxTextW, 'center', $afs + 10) + 30;
            } else {
                $cy += 60;
            }

        // 4.2 "FOR PARTICIPATION AND PUBLICATION OF THE PAPER ENTITLED" — kichik, kulrang
        if ($fontR) {
            $this->gdText($pageCanvas, 26, $fontR, $cGrey2, 'For participation and publication of the paper entitled', $cx, $cy, $maxTextW, 'center');
            $cy += 60;
        }

        // 4.3 Maqola sarlavhasi — Bold Italic, navy, centerlangan
        $rawTitle  = $article->title ?? '';
        $titleText = mb_strlen($rawTitle) > 160 ? mb_substr($rawTitle, 0, 157) . '...' : $rawTitle;
        $titleFont = $fontBI ?: ($fontB ?: $fontR);
        if ($titleFont) {
            $cy = $this->gdTextMultiline($pageCanvas, 40, $titleFont, $cNavy, $titleText, $cx, $cy, $maxTextW, 'center', 50) + 30;
        }

        $countryNameEn = $country->name_en ?? ($country->name ?? 'Country');
        $confTitle     = $conference->title ?? 'International Scientific Conference';

        // 4.3.5 "presented at the" va Konferensiya nomi katta harflarda
        if ($fontR && $fontB) {
            $this->gdText($pageCanvas, 26, $fontR, $cNavy, 'presented at the', $cx, $cy, $maxTextW, 'center');
            $cy += 60;
            
            // Konferensiya nomini kattaroq (masalan, o'lcham 36) Bold qilib o'rtada yozamiz
            $cy = $this->gdTextMultiline($pageCanvas, 36, $fontB, $cNavy, $confTitle, $cx, $cy, $maxTextW, 'center', 46) + 30;
        }
        if ($fontR && $fontB) {
            $parts = [
                ['text' => 'In an International Conference on ', 'font' => $fontR],
                ['text' => $confTitle . '.', 'font' => $fontB],
                ['text' => ' Published online with ', 'font' => $fontR],
                ['text' => 'International scientific conferences of practice', 'font' => $fontB],
                ['text' => ' publications, Hosted online from ' . $countryNameEn, 'font' => $fontR],
            ];
            // Har bir qatorni hisoblaymiz — oxirgi qator uchun $cy ni yangilaymiz
            $lineCount = 0;
            $testParts = $parts;
            $words = [];
            foreach ($testParts as $p) {
                if (empty($p['text']) || !file_exists($p['font'])) continue;
                $wList = explode(' ', $p['text']);
                foreach ($wList as $i => $w) {
                    $txt = $w . ($i < count($wList) - 1 ? ' ' : '');
                    if ($txt !== '') $words[] = ['text' => $txt, 'font' => $p['font']];
                }
            }
            $curW = 0;
            $lc = 1;
            foreach ($words as $w) {
                $trimmed = rtrim($w['text']);
                if ($trimmed === '') { $curW += 34/3; continue; }
                $b = imagettfbbox(34, 0, $w['font'], $trimmed);
                $tw = abs($b[4] - $b[6]) + (substr($w['text'], -1) === ' ' ? 34/3 : 0);
                if ($curW + $tw > $maxTextW && $curW > 0) { $lc++; $curW = $tw; }
                else $curW += $tw;
            }
            $confDescEndY = $cy + ($lc * 48) + 20;

            $this->gdTextMixedMultiline($pageCanvas, 34, $parts, $cNavy, $cx, $cy, $maxTextW, 'justify', 48);

            // Maqola sanasi
            if ($fontB) {
                $confDate = $article->conference && $article->conference->conference_date 
                    ? \Carbon\Carbon::parse($article->conference->conference_date)->format('d.m.Y')
                    : ($article->published_at ? $article->published_at->format('d.m.Y') : ($article->created_at ? $article->created_at->format('d.m.Y') : date('d.m.Y')));
                $this->gdText($pageCanvas, 32, $fontB, $cNavy, 'Date: ' . $confDate, $cx, $confDescEndY, $maxTextW, 'center');
            }
        }



        // ════════════════════════════════════════════════════════════
        // 5. CHIEF EDITOR NOMI (Davlatga mos)
        // ════════════════════════════════════════════════════════════

        $editorNames = [
            'UZ' => 'Prof. Sherzod Yusupov',
            'GB' => 'Prof. Jonathan Hartley',
            'DE' => 'Prof. Klaus Hoffmann',
            'RU' => 'Prof. Alexander Petrov',
            'FR' => 'Prof. Jean-Michel Beaumont',
            'TR' => 'Prof. Mehmet Yilmaz',
            'JP' => 'Prof. Hiroshi Tanaka',
            'CN' => 'Prof. Zhang Wei',
            'US' => 'Prof. Robert Williams',
            'KZ' => 'Prof. Nursultan Akhmetov',
            'KR' => 'Prof. Kim Junho',
            'IN' => 'Prof. Priya Ramesh',
            'IT' => 'Prof. Giovanni Esposito',
            'ES' => 'Prof. Carlos Fernandez',
            'PL' => 'Prof. Marek Kowalski',
            'BR' => 'Prof. Carlos Oliveira',
            'CA' => 'Prof. Michael Patterson',
            'TM' => 'Prof. Berdymurat Atayev',
            'AZ' => 'Prof. Elchin Mammadov',
            'TJ' => 'Prof. Rustam Nazarov',
            'KG' => 'Prof. Bakyt Mamytbekov',
            'DK' => 'Prof. Anders Christensen',
            'SE' => 'Prof. Erik Lindqvist',
            'NO' => 'Prof. Lars Andersen',
            'FI' => 'Prof. Mikko Korhonen',
            'NL' => 'Prof. Jan van der Berg',
            'BE' => 'Prof. Pierre Dubois',
            'CH' => 'Prof. Thomas Müller',
            'AT' => 'Prof. Wolfgang Bauer',
            'PT' => 'Prof. João Ferreira',
            'GR' => 'Prof. Nikos Papadopoulos',
            'SA' => 'Prof. Abdullah Al-Rashid',
            'AE' => 'Prof. Mohammed Al-Mansoori',
        ];

        $alpha3map = [
            'UZB' => 'UZ', 'GBR' => 'GB', 'USA' => 'US', 'DEU' => 'DE',
            'FRA' => 'FR', 'ITA' => 'IT', 'ESP' => 'ES', 'RUS' => 'RU',
            'JPN' => 'JP', 'CHN' => 'CN', 'KOR' => 'KR', 'TUR' => 'TR',
            'POL' => 'PL', 'KAZ' => 'KZ', 'IND' => 'IN', 'BRA' => 'BR',
            'CAN' => 'CA', 'TKM' => 'TM', 'AZE' => 'AZ', 'TJK' => 'TJ', 'KGZ' => 'KG',
            'DNK' => 'DK', 'SWE' => 'SE', 'NOR' => 'NO', 'FIN' => 'FI',
            'NLD' => 'NL', 'BEL' => 'BE', 'CHE' => 'CH', 'AUT' => 'AT',
            'PRT' => 'PT', 'GRC' => 'GR', 'SAU' => 'SA', 'ARE' => 'AE',
        ];

        $countryRawCode = strtoupper($country->code ?? 'GB');
        $editorCode = strlen($countryRawCode) === 3
            ? ($alpha3map[$countryRawCode] ?? strtoupper(substr($countryRawCode, 0, 2)))
            : $countryRawCode;
        $editorName = $editorNames[$editorCode] ?? 'Prof. Yogendra Mishra';

        if ($fontB) {
            $this->gdText($pageCanvas, 30, $fontB, $cBlack, $editorName, 80, 1690, 600, 'left');
        }

        // ════════════════════════════════════════════════════════════
        // 6. QR KOD (O'ng pastki burchak)
        // ════════════════════════════════════════════════════════════

        $qrS = 240;
        $qrX = $W - $qrS - 60;
        $qrY = $H - $qrS - 60;

        try {
            $qrData = urlencode("https://internationalscientificconferences.org/");
            $qrPng = @file_get_contents("https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $qrData);
            if ($qrPng) {
                $qi = @imagecreatefromstring($qrPng);
                if ($qi) {
                    imagecopyresampled($pageCanvas, $qi, $qrX, $qrY, 0, 0, $qrS, $qrS, imagesx($qi), imagesy($qi));
                    imagedestroy($qi);
                }
            }
        } catch (\Throwable $e) {
        }

        if (count($authorsList) === 1) {
            // Yagona muallif bo'lsa JPG qilib saqlaymiz
            $fileNameAuthor = preg_replace('/[\/\\:\*\?"<>\|]/', '', $authorsList[0]);
            $fileNameAuthor = mb_strimwidth($fileNameAuthor, 0, 100, "...");
            if (empty(trim($fileNameAuthor))) {
                $fileNameAuthor = 'Sertifikat_' . $certificate->certificate_number;
            }
            $filename = trim($fileNameAuthor) . ' - sertifikat.jpg';
            $path = 'certificates/' . $filename;
            
            imagejpeg($pageCanvas, Storage::disk('public')->path($path), 100);
            
            imagedestroy($pageCanvas);
            imagedestroy($canvas);
            
            return $path;
        }

        // Agar bir nechta bo'lsa, har birini alohida saqlab olamiz
        $fileNameAuthor = preg_replace('/[\/\\:\*\?"<>\|]/', '', $authorName);
        $fileNameAuthor = mb_strimwidth($fileNameAuthor, 0, 100, "...");
        if (empty(trim($fileNameAuthor))) {
            $fileNameAuthor = 'Sertifikat_' . $certificate->certificate_number . '_' . uniqid();
        }
        $filename = trim($fileNameAuthor) . ' - sertifikat.jpg';
        $path = 'certificates/' . uniqid() . '_' . $filename;
        
        imagejpeg($pageCanvas, Storage::disk('public')->path($path), 100);
        imagedestroy($pageCanvas);

        $generatedJpgs[] = [
            'full_path' => Storage::disk('public')->path($path),
            'local_name' => $filename
        ];
    } // End of foreach ($authorsList)
    
    imagedestroy($canvas);

    // 6. SAQLASH (Ko'p muallif bo'lsa, ZIP sifatida saqlash)
    $fileNameAll = preg_replace('/[\/\\:\*\?"<>\|]/', '', implode(', ', $authorsList));
    $fileNameAll = mb_strimwidth($fileNameAll, 0, 100, "...");
    if (empty(trim($fileNameAll))) {
        $fileNameAll = 'Sertifikatlar_' . $certificate->certificate_number;
    }
    $zipFilename = trim($fileNameAll) . ' - sertifikatlar.zip';
    $zipPath = 'certificates/' . $zipFilename;
    $zipFullPath = Storage::disk('public')->path($zipPath);
    
    $zip = new \ZipArchive();
    if ($zip->open($zipFullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
        $addedNames = [];
        foreach ($generatedJpgs as $file) {
            $localName = $file['local_name'];
            if (isset($addedNames[$localName])) {
                $localName = uniqid() . '_' . $localName;
            }
            $addedNames[$localName] = true;
            $zip->addFile($file['full_path'], $localName);
        }
        $zip->close();
    }
    
    // Yaratilgan vaqtincha JPG larni o'chiramiz
    foreach ($generatedJpgs as $file) {
        @unlink($file['full_path']);
    }
    
    return $zipPath;
}

    /** Bir xil rangdagi to'rtburchak GD rasm yaratish (imagecopymerge uchun) */
    private function solidRect(int $w, int $h, int $r, int $g, int $b)
    {
        $img = imagecreatetruecolor($w, $h);
        imagefill($img, 0, 0, imagecolorallocate($img, $r, $g, $b));
        return $img;
    }




    // ──────────────────────────────────────────────────────────────
    // GD HELPER METHODS
    // ──────────────────────────────────────────────────────────────

    /** Matn chizish (yumaloq) — gorizontal markazlash imkoni bilan */
    private function gdText($img, int $size, string $font, $color, string $text, int $x, int $y, int $maxW, string $align = 'left'): void
    {
        if (!file_exists($font) || empty($text))
            return;
        $bbox = imagettfbbox($size, 0, $font, $text);
        $tw = abs($bbox[2] - $bbox[0]);
        if ($align === 'center') {
            $x = $x + intval(($maxW - $tw) / 2);
        } elseif ($align === 'right') {
            $x = $x + $maxW - $tw;
        }
        imagettftext($img, $size, 0, $x, $y + $size, $color, $font, $text);
    }

    /** Aralash qalin/oddiy fontli ko'p qatorli matn */
    private function gdTextMixedMultiline($img, int $size, array $parts, $color, int $x, int $y, int $maxW, string $align = 'left', int $lineH = 0): void
    {
        if ($lineH === 0)
            $lineH = $size + 14;

        $words = [];
        foreach ($parts as $p) {
            if (empty($p['text']) || !file_exists($p['font']))
                continue;
            // Split by space and keep the trailing space to distinguish them
            $wList = explode(' ', $p['text']);
            $count = count($wList);
            foreach ($wList as $i => $w) {
                $txt = $w . ($i < $count - 1 ? ' ' : '');
                if ($txt !== '')
                    $words[] = ['text' => $txt, 'font' => $p['font']];
            }
        }

        $lines = [];
        $currentLine = [];
        $currentLineW = 0;

        foreach ($words as $w) {
            $f = $w['font'];
            $txt = $w['text'];

            // Measure word without space
            $trimmed = rtrim($txt);
            if ($trimmed === '') {
                // It is just a space
                $tw = $size / 3;
            } else {
                $b = imagettfbbox($size, 0, $f, $trimmed);
                $tw = abs($b[4] - $b[6]);
                if (substr($txt, -1) === ' ')
                    $tw += ($size / 3);
            }

            if ($txt !== ' ' && $currentLineW + $tw > $maxW && !empty($currentLine)) {
                $lines[] = ['words' => $currentLine, 'width' => $currentLineW];
                $currentLine = [$w];
                $currentLineW = $tw;
            } else {
                $currentLine[] = $w;
                $currentLineW += $tw;
            }
        }
        if (!empty($currentLine)) {
            $lines[] = ['words' => $currentLine, 'width' => $currentLineW];
        }

        $cy = $y;
        $totalLines = count($lines);
        foreach ($lines as $lineIdx => $l) {
            $lineWords = $l['words'];
            // Trim leading/trailing space words
            while (count($lineWords) > 0 && $lineWords[0]['text'] === ' ') {
                array_shift($lineWords);
            }
            while (count($lineWords) > 0 && $lineWords[count($lineWords) - 1]['text'] === ' ') {
                array_pop($lineWords);
            }

            // Remove trailing-space within each word text for measuring
            $nonSpaceWords = array_filter($lineWords, fn($w) => rtrim($w['text']) !== '');
            $nonSpaceWords = array_values($nonSpaceWords);
            $wordCount = count($nonSpaceWords);

            // Calculate total words width (no trailing space)
            $totalWordsW = 0;
            foreach ($nonSpaceWords as $w) {
                $trimmed = rtrim($w['text']);
                $b = imagettfbbox($size, 0, $w['font'], $trimmed);
                $totalWordsW += abs($b[4] - $b[6]);
            }

            $isLastLine = ($lineIdx === $totalLines - 1);
            $useJustify = ($align === 'justify' && !$isLastLine && $wordCount > 1);

            if ($useJustify) {
                // Distribute extra space evenly between words
                $gapW = ($maxW - $totalWordsW) / ($wordCount - 1);
                $cx = $x;
                foreach ($nonSpaceWords as $i => $w) {
                    $trimmed = rtrim($w['text']);
                    $b = imagettfbbox($size, 0, $w['font'], $trimmed);
                    imagettftext($img, $size, 0, (int)$cx, $cy + $size, $color, $w['font'], $trimmed);
                    $ww = abs($b[4] - $b[6]);
                    $cx += $ww + ($i < $wordCount - 1 ? $gapW : 0);
                }
            } else {
                // left / center / right / last line of justify (left)
                $lw = $totalWordsW + ($wordCount - 1) * ($size / 3);
                $cx = $x;
                if ($align === 'center')
                    $cx = $x + intval(($maxW - $lw) / 2);
                elseif ($align === 'right')
                    $cx = $x + $maxW - $lw;

                foreach ($nonSpaceWords as $i => $w) {
                    $f = $w['font'];
                    $trimmed = rtrim($w['text']);
                    $b = imagettfbbox($size, 0, $f, $trimmed);
                    imagettftext($img, $size, 0, (int)$cx, $cy + $size, $color, $f, $trimmed);
                    $ww = abs($b[4] - $b[6]);
                    $cx += $ww + ($i < $wordCount - 1 ? ($size / 3) : 0);
                }
            }
            $cy += $lineH;
        }
    }

    /** Ko'p qatorli matn */
    private function gdTextMultiline($img, int $size, string $font, $color, string $text, int $x, int $y, int $maxW, string $align = 'left', int $lineH = 0): int
    {
        if (!file_exists($font) || empty($text))
            return $y;
        if ($lineH === 0)
            $lineH = $size + 8;
        $words = explode(' ', $text);
        $line = '';
        $lines = [];
        foreach ($words as $word) {
            $test = $line ? $line . ' ' . $word : $word;
            $bbox = imagettfbbox($size, 0, $font, $test);
            $tw = abs($bbox[2] - $bbox[0]);
            if ($tw > $maxW && $line) {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $test;
            }
        }
        if ($line)
            $lines[] = $line;
            
        foreach ($lines as $i => $l) {
            if ($align === 'justify' && $i < count($lines) - 1) {
                $this->gdTextJustifiedLine($img, $size, $font, $color, $l, $x, $y, $maxW);
            } else {
                $actualAlign = $align === 'justify' ? 'center' : $align;
                $this->gdText($img, $size, $font, $color, $l, $x, $y, $maxW, $actualAlign);
            }
            $y += $lineH;
        }
        return $y;
    }

    private function gdTextJustifiedLine($img, int $size, string $font, $color, string $lineStr, int $x, int $y, int $maxW): void
    {
        $words = explode(' ', $lineStr);
        if (count($words) <= 1) {
            $this->gdText($img, $size, $font, $color, $lineStr, $x, $y, $maxW, 'left');
            return;
        }
        
        $totalWordsW = 0;
        foreach ($words as $word) {
            $bbox = imagettfbbox($size, 0, $font, $word);
            $totalWordsW += abs($bbox[2] - $bbox[0]);
        }
        
        $totalSpaceW = $maxW - $totalWordsW;
        $gapW = $totalSpaceW / (count($words) - 1);
        
        $currentX = $x;
        foreach ($words as $i => $word) {
            imagettftext($img, $size, 0, (int)$currentX, $y + $size, $color, $font, $word);
            if ($i < count($words) - 1) {
                $bbox = imagettfbbox($size, 0, $font, $word);
                $wordW = abs($bbox[2] - $bbox[0]);
                $currentX += $wordW + $gapW;
            }
        }
    }

    /** Gorizontal chiziq */
    private function gdHr($img, int $x, int $y, int $w, $color, int $thickness = 1): void
    {
        imagesetthickness($img, $thickness);
        imageline($img, $x, $y, $x + $w, $y, $color);
        imagesetthickness($img, 1);
    }

    /** To'ldirilgan yumaloq to'rtburchak */
    private function imageFilledRoundedRect($img, int $x, int $y, int $w, int $h, int $r, $color): void
    {
        imagefilledrectangle($img, $x + $r, $y, $x + $w - $r, $y + $h, $color);
        imagefilledrectangle($img, $x, $y + $r, $x + $w, $y + $h - $r, $color);
        imagefilledellipse($img, $x + $r, $y + $r, $r * 2, $r * 2, $color);
        imagefilledellipse($img, $x + $w - $r, $y + $r, $r * 2, $r * 2, $color);
        imagefilledellipse($img, $x + $r, $y + $h - $r, $r * 2, $r * 2, $color);
        imagefilledellipse($img, $x + $w - $r, $y + $h - $r, $r * 2, $r * 2, $color);
    }

    /** Faqat yuqori burchaklari yumaloq to'rtburchak (sarlavha uchun) */
    private function imageFilledRoundedRectTop($img, int $x, int $y, int $w, int $h, int $r, $color): void
    {
        imagefilledrectangle($img, $x + $r, $y, $x + $w - $r, $y + $h, $color);
        imagefilledrectangle($img, $x, $y + $r, $x + $w, $y + $h, $color);
        imagefilledellipse($img, $x + $r, $y + $r, $r * 2, $r * 2, $color);
        imagefilledellipse($img, $x + $w - $r, $y + $r, $r * 2, $r * 2, $color);
    }

    /** Yumaloq to'rtburchak outline */
    private function imageRoundedRect($img, int $x, int $y, int $w, int $h, int $r, $color, int $thickness = 1): void
    {
        imagesetthickness($img, $thickness);
        imageline($img, $x + $r, $y, $x + $w - $r, $y, $color);
        imageline($img, $x + $r, $y + $h, $x + $w - $r, $y + $h, $color);
        imageline($img, $x, $y + $r, $x, $y + $h - $r, $color);
        imageline($img, $x + $w, $y + $r, $x + $w, $y + $h - $r, $color);
        imagearc($img, $x + $r, $y + $r, $r * 2, $r * 2, 180, 270, $color);
        imagearc($img, $x + $w - $r, $y + $r, $r * 2, $r * 2, 270, 360, $color);
        imagearc($img, $x + $r, $y + $h - $r, $r * 2, $r * 2, 90, 180, $color);
        imagearc($img, $x + $w - $r, $y + $h - $r, $r * 2, $r * 2, 0, 90, $color);
        imagesetthickness($img, 1);
    }

    /**
     * Template'dagi yashil rangdagi piksellarni davlat rangiga almashtirish.
     * Shakllar, matnlar, gradiyentlar — hech narsa o'zgarmaydi, faqat rang.
     *
     * Har bir pikselning HSL qiymatini hisoblaydi.
     * Agar Hue yashil zonada (80-170) va Saturation yetarli (>15%) bo'lsa,
     * Hue va Saturation ni maqsad rangga almashtirib, Lightness ni saqlaydi.
     */
    private function replaceGreenWithColor($canvas, int $targetR, int $targetG, int $targetB, int $w, int $h): void
    {
        // Maqsad rangning HSL qiymatlarini hisoblash
        [$tH, $tS, $tL] = $this->rgbToHsl($targetR, $targetG, $targetB);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($canvas, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                [$h2, $s2, $l2] = $this->rgbToHsl($r, $g, $b);

                // Yashil zona: Hue 80-170, Saturation > 15%
                if ($h2 >= 80 && $h2 <= 170 && $s2 > 0.15) {
                    // Davlat rangining Hue va Saturation ini ishlat, Lightness ni saqla
                    [$nr, $ng, $nb] = $this->hslToRgb($tH, $tS, $l2);
                    $newColor = imagecolorallocate($canvas, $nr, $ng, $nb);
                    if ($newColor === false) {
                        $newColor = imagecolorclosest($canvas, $nr, $ng, $nb);
                    }
                    imagesetpixel($canvas, $x, $y, $newColor);
                }
            }
        }
    }

    /** RGB -> HSL konvertatsiya */
    private function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;

        if ($d == 0) {
            return [0, 0.0, $l];
        }

        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        if ($max == $r) {
            $h = fmod(($g - $b) / $d + 6, 6);
        } elseif ($max == $g) {
            $h = ($b - $r) / $d + 2;
        } else {
            $h = ($r - $g) / $d + 4;
        }
        $h *= 60;

        return [$h, $s, $l];
    }

    /** HSL -> RGB konvertatsiya */
    private function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s == 0) {
            $v = (int) round($l * 255);
            return [$v, $v, $v];
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;
        $hk = $h / 360;

        $tr = $hk + 1 / 3;
        $tg2 = $hk;
        $tb = $hk - 1 / 3;

        $calc = function ($t) use ($p, $q) {
            if ($t < 0) $t += 1;
            if ($t > 1) $t -= 1;
            if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
            if ($t < 1 / 2) return $q;
            if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
            return $p;
        };

        return [
            (int) round($calc($tr) * 255),
            (int) round($calc($tg2) * 255),
            (int) round($calc($tb) * 255),
        ];
    }

    /** Rasm va maska tekshiruvi orqali poligon shaklda rasm chizish */
    private function drawMaskedImage($destObj, $srcImage, array $points): void
    {
        $minX = $points[0];
        $maxX = $points[0];
        $minY = $points[1];
        $maxY = $points[1];
        for ($i = 2; $i < count($points); $i += 2) {
            if ($points[$i] < $minX)
                $minX = $points[$i];
            if ($points[$i] > $maxX)
                $maxX = $points[$i];
            if ($points[$i + 1] < $minY)
                $minY = $points[$i + 1];
            if ($points[$i + 1] > $maxY)
                $maxY = $points[$i + 1];
        }
        $bw = $maxX - $minX + 1;
        $bh = $maxY - $minY + 1;
        if ($bw <= 0 || $bh <= 0)
            return;

        // Create mask
        $mask = imagecreatetruecolor($maxX + 1, $maxY + 1);
        $black = imagecolorallocate($mask, 0, 0, 0);
        $white = imagecolorallocate($mask, 255, 255, 255);
        imagefill($mask, 0, 0, $black);
        imagefilledpolygon($mask, $points, count($points) / 2, $white);

        // Resize src image to cover bounding box exactly
        $sw = imagesx($srcImage);
        $sh = imagesy($srcImage);
        $ratio = max($bw / $sw, $bh / $sh);
        $newW = (int) ($sw * $ratio);
        $newH = (int) ($sh * $ratio);
        $temp = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($temp, $srcImage, 0, 0, 0, 0, $newW, $newH, $sw, $sh);

        // Final src cropped to exactly bw x bh
        $cropped = imagecreatetruecolor($bw, $bh);
        imagecopy($cropped, $temp, 0, 0, ($newW - $bw) / 2, ($newH - $bh) / 2, $bw, $bh);
        imagedestroy($temp);

        // Fast pixel copy using the mask
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                $c = imagecolorat($mask, $x, $y);
                if (($c & 0xFF) > 128) {
                    $rgb = imagecolorat($cropped, $x - $minX, $y - $minY);
                    imagesetpixel($destObj, $x, $y, $rgb);
                }
            }
        }
        imagedestroy($mask);
        imagedestroy($cropped);
    }




    // ──────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Rasmni kichiklashtirish (GD yordamida) — PDF hajmini kamaytirish uchun.
     * Qayta o'lchangsiz rasm yo'li qaytariladi (xato bo'lsa null).
     */
    private function getCompressedImage(string $srcPath, int $maxWidth = 800): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null; // GD yo'q
        }
        try {
            $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
            $data = file_get_contents($srcPath);
            $src = imagecreatefromstring($data);
            if (!$src)
                return null;

            $w = imagesx($src);
            $h = imagesy($src);

            // Agar rasm allaqachon kichik bo'lsa, to'g'ridan-to'g'ri siqamiz
            if ($w <= $maxWidth) {
                $dst = $src;
            } else {
                $newH = (int) round($h * $maxWidth / $w);
                $dst = imagecreatetruecolor($maxWidth, $newH);
                // Shaffoflik saqlash
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $newH, $w, $h);
                imagedestroy($src);
            }

            $tmpDir = public_path('images/tmp');
            if (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0777, true);
            }
            $tmpFile = tempnam($tmpDir, 'bg_') . '.jpg';
            imagejpeg($dst, $tmpFile, 60); // Kamroq quality bilan siqish (hajmni keskin kamaytiradi)
            imagedestroy($dst);

            return $tmpFile;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Davlat kodi asosida manzara rasmini tanlash
     * certificates/backgrounds/ papkasidan
     */
    private function getBackgroundImage(string $countryCode): ?string
    {
        // 3-harfli → 2-harfli
        $alpha3 = [
            'UZB' => 'UZ',
            'GBR' => 'GB',
            'USA' => 'US',
            'DEU' => 'DE',
            'FRA' => 'FR',
            'ITA' => 'IT',
            'ESP' => 'ES',
            'RUS' => 'RU',
            'JPN' => 'JP',
            'CHN' => 'CN',
            'KOR' => 'KR',
            'TUR' => 'TR',
            'POL' => 'PL',
            'KAZ' => 'KZ',
            'IND' => 'IN',
            'BRA' => 'BR',
            'CAN' => 'CA',
            'TKM' => 'TM',
            'AUS' => 'AU',
        ];
        $code = strlen($countryCode) === 3
            ? ($alpha3[strtoupper($countryCode)] ?? strtoupper(substr($countryCode, 0, 2)))
            : strtoupper($countryCode);

        $bgDir = public_path('images/certificates/backgrounds');

        // Kod → fayl nomi map
        $bgMap = [
            'UZ' => 'registon_samarqand.png',
            'GB' => 'london_bridge.png',
            'DE' => 'reichstag_berlin.png',
            'RU' => 'russia_moscow.png',
            'FR' => 'france_paris.png',
            'TR' => 'turkey_istanbul.png',
            'JP' => 'japan_fuji.png',
            'CN' => 'china_wall.png',
            'US' => 'usa_washington.png',
            'KZ' => 'kazakhstan_astana.png',
            'KR' => 'korea_seoul.png',
            'IN' => 'india_tajmahal.png',
            'IT' => 'italy_rome.png',
            'ES' => 'spain_barcelona.png',
            'PL' => 'poland_krakow.png',
            'BR' => 'brazil_rio.png',
            'CA' => 'canada_niagara.png',
            'TM' => 'turkmenistan_ashgabat.png',
        ];

        if (isset($bgMap[$code])) {
            $path = $bgDir . DIRECTORY_SEPARATOR . $bgMap[$code];
            if (file_exists($path))
                return $path;
        }

        // Fallback: papkadagi birinchi PNG
        $files = glob($bgDir . DIRECTORY_SEPARATOR . '*.png');
        return $files[0] ?? null;
    }

    /**
     * Davlat kodi asosida ramz/xarita rasmini tanlash
     * public/images/countries/ papkasidan
     */
    private function getCountrySymbolImage(string $countryCode): ?string
    {
        $alpha3 = [
            'UZB' => 'UZ',
            'GBR' => 'GB',
            'USA' => 'US',
            'DEU' => 'DE',
            'FRA' => 'FR',
            'ITA' => 'IT',
            'ESP' => 'ES',
            'RUS' => 'RU',
            'JPN' => 'JP',
            'CHN' => 'CN',
            'KOR' => 'KR',
            'TUR' => 'TR',
            'POL' => 'PL',
            'KAZ' => 'KZ',
            'IND' => 'IN',
            'BRA' => 'BR',
            'CAN' => 'CA',
            'TKM' => 'TM',
            'AUS' => 'AU',
        ];
        $code = strlen($countryCode) === 3
            ? ($alpha3[strtoupper($countryCode)] ?? strtoupper(substr($countryCode, 0, 2)))
            : strtoupper($countryCode);

        $bgDir = public_path('images/countries');

        $map = [
            'UZ' => 'uzbekistan.png',
            'GB' => 'united_kingdom.png',
            'DE' => 'germany.png',
            'RU' => 'russia.png',
            'FR' => 'france.png',
            'TR' => 'turkey.png',
            'JP' => 'japan.png',
            'CN' => 'china.png',
            'US' => 'usa.png',
            'KZ' => 'kazakhstan.png',
            'KR' => 'south_korea.png',
            'IN' => 'india.png',
            'IT' => 'italy.png',
            'ES' => 'spain.png',
            'PL' => 'poland.png',
            'BR' => 'brazil.png',
            'CA' => 'canada.png',
            'TM' => 'turkmenistan.png',
        ];

        if (isset($map[$code])) {
            $path = $bgDir . DIRECTORY_SEPARATOR . $map[$code];
            if (file_exists($path))
                return $path;
        }

        $files = glob($bgDir . DIRECTORY_SEPARATOR . '*.png');
        return $files[0] ?? null;
    }

    /**
     * HEX → [r, g, b]
     */
    private function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Davlat ranglari — bayroq asosida
     */
    private function getCountryColors(string $code): array
    {
        $alpha3 = [
            'UZB' => 'UZ',
            'GBR' => 'GB',
            'USA' => 'US',
            'DEU' => 'DE',
            'FRA' => 'FR',
            'ITA' => 'IT',
            'ESP' => 'ES',
            'RUS' => 'RU',
            'JPN' => 'JP',
            'CHN' => 'CN',
            'KOR' => 'KR',
            'TUR' => 'TR',
            'POL' => 'PL',
            'KAZ' => 'KZ',
            'IND' => 'IN',
            'BRA' => 'BR',
            'CAN' => 'CA',
            'TKM' => 'TM',
            'AUS' => 'AU',
            'NLD' => 'NL',
            'SWE' => 'SE',
            'CHE' => 'CH',
            'AUT' => 'AT',
            'BEL' => 'BE',
            'PRT' => 'PT',
            'GRC' => 'GR',
            'SAU' => 'SA',
            'ARE' => 'AE',
            'AZE' => 'AZ',
            'TJK' => 'TJ',
            'KGZ' => 'KG',
        ];

        $code = strlen($code) === 3
            ? ($alpha3[strtoupper($code)] ?? strtoupper(substr($code, 0, 2)))
            : strtoupper($code);

        $colors = [
            'UZ' => ['primary' => '#0099b5', 'secondary' => '#1eb53a', 'accent' => '#c9a227'],
            'TR' => ['primary' => '#e30a17', 'secondary' => '#1a1a2e', 'accent' => '#e30a17'],
            'KZ' => ['primary' => '#00afca', 'secondary' => '#ffc61e', 'accent' => '#006994'],
            'US' => ['primary' => '#3c3b6e', 'secondary' => '#b22234', 'accent' => '#c9a227'],
            'GB' => ['primary' => '#012169', 'secondary' => '#c8102e', 'accent' => '#c9a227'],
            'DE' => ['primary' => '#000000', 'secondary' => '#dd0000', 'accent' => '#ffcc00'],
            'FR' => ['primary' => '#0055a4', 'secondary' => '#ef4135', 'accent' => '#0055a4'],
            'IT' => ['primary' => '#009246', 'secondary' => '#cd212a', 'accent' => '#c9a227'],
            'ES' => ['primary' => '#c60b1e', 'secondary' => '#ffc400', 'accent' => '#c60b1e'],
            'RU' => ['primary' => '#0039a6', 'secondary' => '#d52b1e', 'accent' => '#c9a227'],
            'JP' => ['primary' => '#bc002d', 'secondary' => '#1a1a2e', 'accent' => '#bc002d'],
            'CN' => ['primary' => '#de2910', 'secondary' => '#8b0000', 'accent' => '#ffde00'],
            'KR' => ['primary' => '#0047a0', 'secondary' => '#cd2e3a', 'accent' => '#1a1a1a'],
            'PL' => ['primary' => '#dc143c', 'secondary' => '#ffffff', 'accent' => '#dc143c'],
            'IN' => ['primary' => '#ff9933', 'secondary' => '#138808', 'accent' => '#000080'],
            'BR' => ['primary' => '#009c3b', 'secondary' => '#ffdf00', 'accent' => '#002776'],
            'CA' => ['primary' => '#ff0000', 'secondary' => '#ffffff', 'accent' => '#990000'],
            'TM' => ['primary' => '#00843d', 'secondary' => '#d22630', 'accent' => '#c9a227'],
            'AZ' => ['primary' => '#0092bc', 'secondary' => '#e4002b', 'accent' => '#00af66'],
            'TJ' => ['primary' => '#cc0000', 'secondary' => '#006600', 'accent' => '#c9a227'],
            'KG' => ['primary' => '#e8112d', 'secondary' => '#fecc00', 'accent' => '#e8112d'],
        ];

        return $colors[$code] ?? ['primary' => '#1a5276', 'secondary' => '#2980b9', 'accent' => '#c9a227'];
    }

    /**
     * QR kod yaratish (base64) — sayt URL bilan
     */
    private function getQrBase64(Certificate $certificate, string $url = ''): string
    {
        if (empty($url)) {
            $url = url("/certificate/verify/{$certificate->certificate_number}");
        }
        try {
            return 'data:image/png;base64,' . base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->generate($url)
            );
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Oylik to'plam PDF yaratish — 3 qism:
     *  1-bet: Davlat cover rasmi
     *  2-bet: Umumiy ma'lumotlar + Mundarija
     *  3-bet+: Maqolalar
     */
    public function generateMonthlyCollection(Conference $conference): string
    {
        $conference->load(['country', 'articles.author']);

        $country = $conference->country;
        $colors = $this->getCountryColors($country->code ?? 'GB');
        $articles = $conference->articles()->published()->get();

        // ── Blade view cache tozalash ──────────────────────────────
        // Har safar yangi kompilatsiya qilinsin (cache stale bo'lmasli uchun)
        $compiledViewsDir = storage_path('framework/views');
        if (is_dir($compiledViewsDir)) {
            foreach (glob($compiledViewsDir . '/*.php') as $file) {
                @unlink($file);
            }
        }

        // ── Maqola HTML laridan og'ir elementlarni tozalash ────────
        // Bu yerda Blade ga o'tmasdan avval qilinadi — ishonchli yechim
        foreach ($articles as $article) {
            if (!empty($article->content)) {
                // Regex katta base64 larda ishlamaydi (PHP limits), shuning uchun strip_tags ishlatamiz
                $allowedTags = '<p><br><b><strong><i><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><table><tr><td><th><tbody><thead><tfoot><blockquote><hr><span><a>';
                $cleanHtml = strip_tags($article->content, $allowedTags);
                // Qolgan keraksiz style attributelarni tozalash (faqat font-weight, color kabilarga ruxsat berish mumkin, lekin yaxshisi hammasini tozalash)
                $cleanHtml = preg_replace('/ style="[^"]*"/i', '', $cleanHtml);
                $cleanHtml = preg_replace('/ class="[^"]*"/i', '', $cleanHtml);
                $article->content = $cleanHtml;
            }
        }

        // ── DomPDF font cache tozalash ─────────────────────────────
        $fontCacheDir = storage_path('fonts');
        if (is_dir($fontCacheDir)) {
            foreach (glob($fontCacheDir . '/*.php') as $f) {
                @unlink($f);
            }
        }

        $html = view('pdf.collection', [
            'conference' => $conference,
            'country' => $country,
            'articles' => $articles,
            'colors' => $colors,
        ])->render();

        $conferenceTitle = preg_replace('/[\/\\:\*\?"<>\|]/', '', $conference->title);
        if (empty(trim($conferenceTitle))) {
            $conferenceTitle = 'Collection_' . ($country->code ?? 'XX') . '_' . ($conference->month_year ?? date('Y-m'));
        }
        $filename = trim($conferenceTitle) . '.pdf';
        $path = 'collections/' . $filename;

        $directory = Storage::disk('public')->path('collections');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $finalPath = Storage::disk('public')->path($path);

        // ── Node.js Puppeteer orqali render qilish (hajmni keskin kamaytiradi: 2.4MB -> ~100KB) ──
        $scriptPath = base_path('scripts/html-to-pdf.cjs');

        $nodePath = env('NODE_PATH', '');
        if (empty($nodePath)) {
            $possiblePaths = [
                'C:\\Program Files\\nodejs\\node.exe',
                'C:\\Program Files (x86)\\nodejs\\node.exe',
                'node'
            ];
            foreach ($possiblePaths as $p) {
                if ($p === 'node' || file_exists($p)) {
                    $nodePath = $p;
                    break;
                }
            }
        }

        if (file_exists($scriptPath) && $nodePath !== '') {
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $cmd = escapeshellarg($nodePath) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($finalPath);
            \Illuminate\Support\Facades\Log::info("Starting Puppeteer: " . $cmd);

            $process = proc_open($cmd, $descriptors, $pipes, base_path());

            if (is_resource($process)) {
                fwrite($pipes[0], $html);
                fclose($pipes[0]);

                $stdout = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $stderr = stream_get_contents($pipes[2]);
                fclose($pipes[2]);

                $returnCode = proc_close($process);

                // Agar Puppeteer yordamida fayl yaratilgan bo'lsa, qaytarish
                if ($returnCode === 0 && file_exists($finalPath)) {
                    return $path;
                } else {
                    \Illuminate\Support\Facades\Log::error('Puppeteer failed for collection PDF', [
                        'stdout' => $stdout,
                        'stderr' => $stderr,
                        'returnCode' => $returnCode,
                        'html_length' => strlen($html)
                    ]);
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Puppeteer proc_open failed entirely. Command: ' . $cmd);
            }
        }

        // ── Fallback: DomPDF (Agar node ishlolmasa) ───────────
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 72,
            'defaultPaperSize' => 'A4',
            'isFontSubsettingEnabled' => true,
            'isCssFloatEnabled' => false,
            'isJavascriptEnabled' => false,
        ]);

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
