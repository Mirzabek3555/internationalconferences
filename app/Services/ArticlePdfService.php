<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Conference;
use App\Models\Country;
use TCPDF;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ArticlePdfService
{
    /**
     * Matndan to'liq PDF yaratish - davlat ramzlari bilan
     * Professional akademik standartlarda: marginlar, formulalar, jadvallar
     */
    public function generateFromText(Article $article, Country $country): string
    {
        $article->load(['conference']);
        $conference = $article->conference;

        // Matematik va kimyoviy formulalarni qayta ishlash
        $processedContent = $this->processFormulaContent($article->content ?? '');

        // PDF shablonini yaratish - akademik format
        $html = view('pdf.article-academic', [
            'article' => $article,
            'conference' => $conference,
            'country' => $country,
            'colors' => $this->getCountryColors($country->code ?? 'GB'),
            'processedContent' => $processedContent,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        // Yuqori sifat uchun optionlar - Optimization focused
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Serif');
        $pdf->setOption('isFontSubsettingEnabled', true); // Critical for size reduction

        // Saqlash
        $filename = 'article_' . time() . '_' . $article->id . '.pdf';
        $path = 'articles/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        // Papkani yaratish
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $pdf->output());

        return $path;
    }

    /**
     * INCOP.org uslubidagi akademik PDF yaratish
     * Kompakt, professional, Google Scholar uchun optimallashtirilgan
     */
    public function generateIncopStyle(Article $article, Country $country): string
    {
        // Maximum resources for very large PDFs - UNLIMITED
        set_time_limit(0);
        ini_set('memory_limit', '-1'); // Unlimited memory
        ini_set('max_execution_time', '0');
        $article->load(['conference']);
        $conference = $article->conference;

        // PDF shablonini yaratish - INCOP uslubi
        $html = view('pdf.article-incop-style', [
            'article' => $article,
            'conference' => $conference,
            'country' => $country,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');


        // Yuqori sifat uchun optionlar - Optimization focused
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Serif');
        $pdf->setOption('isFontSubsettingEnabled', true); // Critical for size reduction

        // Saqlash
        $filename = 'incop_article_' . time() . '_' . $article->id . '.pdf';
        $path = 'articles/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        // Papkani yaratish
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $pdf->output());

        // Memory ni tozalash
        unset($pdf);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        return $path;
    }

    /**
     * DOCX fayldan professional PDF yaratish
     * 
     * Pipeline:
     * 1. DOCX → HTML (mammoth.js + OMML → MathML → KaTeX)
     * 2. HTML → PDF (Puppeteer — formulalar to'liq saqlanadi)
     * 3. Base PDF → mergeWithCoverPage (INCOP dizayn overlay)
     */
    public function generateFromDocx(string $docxPath, Article $article, Country $country): string
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $article->load(['conference']);
        $conference = $article->conference;
        $countryColors = $this->getCountryColors($country->code ?? 'GB');
        $primaryRgb = $this->hexToRgb($countryColors['primary']);

        // Calculate the first page top margin dynamically based on the exact height of the header blocks
        // so Puppeteer can reserve the exact white space needed.
        $leftMargin = 33; // 28mm sidebar + 5mm gap
        $rightMargin = 15;
        $contentWidth = 210 - $leftMargin - $rightMargin;
        $firstPageTopMargin = $this->calculateHeaderHeight($article, $country, $conference, $contentWidth) + 2; // +2mm safe margin buffer

        // 1. DOCX ni HTML ga aylantirish (formulalar bilan)
        $docxProcessor = new DocxProcessorService();
        $result = $docxProcessor->processDocx($docxPath);
        $articleHtml = $result['html'];

        // 2. Article content ni yangilash (HTML saqlanadi — formulalar uchun)
        if (!empty($articleHtml)) {
            $article->update(['content' => $articleHtml]);
        }

        // References ni HTML ga qo'shish (Puppeteer buni o'zi flow qiladi)
        if (!empty($article->references)) {
            $articleHtml .= '<div class="references-section">';
            $articleHtml .= '<h3 class="references-title">FOYDALANILGAN ADABIYOTLAR:</h3>';
            $rawRefs = explode("\n", $article->references);
            foreach ($rawRefs as $ref) {
                $ref = trim($ref);
                if (!empty($ref)) {
                    $articleHtml .= '<p class="reference-item">' . htmlspecialchars($ref) . '</p>';
                }
            }
            $articleHtml .= '</div>';
        }

        // 3. Puppeteer orqali HTML → PDF (formulalar to'liq render qilinadi)
        $basePdfFilename = 'docx_base_' . time() . '_' . $article->id . '.pdf';
        $basePdfRelPath = 'articles/base/' . $basePdfFilename;
        $basePdfFullPath = Storage::disk('public')->path($basePdfRelPath);

        $directory = dirname($basePdfFullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Pass calculated margins to node
        $this->renderHtmlToPdfWithPuppeteer(
            $articleHtml, 
            $basePdfFullPath, 
            (string)ceil($firstPageTopMargin) . 'mm', // First page top margin
            '22mm', // Subsequent pages top margin (running header size is 20mm + 2mm gap)
            '33mm', // Left margin
            '15mm', // Right margin
            '20mm'  // Bottom margin (footer size 17mm + 3mm gap)
        );

        // 4. Base PDF ni article ga saqlash
        $article->update(['pdf_path' => $basePdfRelPath]);

        // 5. INCOP dizayn overlay qo'shish (sidebar, header, footer, ranglar)
        $formattedPath = $this->mergeWithCoverPage($article, $country);

        // Memory tozalash
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        return $formattedPath;
    }

    /**
     * Puppeteer orqali HTML ni PDF ga render qilish
     * KaTeX formulalar, jadvallar, rasmlar to'liq saqlanadi
     */
    private function renderHtmlToPdfWithPuppeteer(
        string $html, 
        string $outputPath,
        string $topMarginFirst = '20mm',
        string $topMarginRest = '20mm',
        string $leftMargin = '20mm',
        string $rightMargin = '15mm',
        string $bottomMargin = '20mm'
    ): void
    {
        $scriptPath = base_path('scripts' . DIRECTORY_SEPARATOR . 'docx-to-pdf.cjs');

        if (!file_exists($scriptPath)) {
            throw new \Exception('docx-to-pdf.cjs skripti topilmadi: ' . $scriptPath);
        }

        // Node.js yo'lini topish
        $nodePath = $this->findNodePath();

        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        // Katta HTML datalarni stdout pipe orqali uzatishda deadlok yuzaga kelmasligi uchun faylga yozamiz
        $tempHtmlPath = storage_path('app/temp/puppeteer_input_' . uniqid() . '.html');
        if (!is_dir(dirname($tempHtmlPath))) {
            mkdir(dirname($tempHtmlPath), 0755, true);
        }
        file_put_contents($tempHtmlPath, $html);

        $command = sprintf(
            '%s %s %s %s %s %s %s %s %s',
            escapeshellarg($nodePath),
            escapeshellarg($scriptPath),
            escapeshellarg($outputPath),
            escapeshellarg($topMarginFirst),
            escapeshellarg($topMarginRest),
            escapeshellarg($leftMargin),
            escapeshellarg($rightMargin),
            escapeshellarg($bottomMargin),
            escapeshellarg($tempHtmlPath) // 8-CHI ARGUMENT
        );

        $process = proc_open($command, $descriptors, $pipes, base_path());

        if (!is_resource($process)) {
            @unlink($tempHtmlPath);
            throw new \Exception('Puppeteer process ochib bo\'lmadi');
        }

        // Fayldan o'qilayotgani uchun stdin yopiladi
        fclose($pipes[0]);

        // Natijani o'qish
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);
        
        // Vaqtincha HTML ni o'chiramiz
        @unlink($tempHtmlPath);

        if ($returnCode !== 0) {
            Log::error('Puppeteer PDF render xatosi', ['stderr' => $stderr, 'stdout' => $stdout]);
            throw new \Exception('HTML → PDF render xatosi: ' . ($stderr ?: $stdout));
        }

        if (!file_exists($outputPath)) {
            throw new \Exception('Puppeteer PDF fayl yaratmadi: ' . $outputPath);
        }

        Log::info('Puppeteer PDF yaratildi', ['path' => $outputPath, 'size' => filesize($outputPath)]);
    }

    /**
     * Node.js yo'lini topish (DocxProcessorService bilan bir xil)
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
     * DomPDF bilan PDF yaratish (fallback — formulalarsiz)
     */
    private function generatePdfWithDompdf(string $html, string $fullPath): void
    {
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Serif');
        $pdf->setOption('isFontSubsettingEnabled', true);
        file_put_contents($fullPath, $pdf->output());
    }

    /**
     * Mavjud PDF ga INCOP.org uslubidagi overlay (dizayn) qo'shish
     */
    public function applyOverlayToPdf(Article $article, Country $country): string
    {
        // Maximum resources - UNLIMITED
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $article->load(['conference']);
        $conference = $article->conference;

        // Asl PDF yo'lini olish
        $basePdfPath = Storage::disk('public')->path($article->pdf_path);

        if (!file_exists($basePdfPath)) {
            throw new \Exception('Asl PDF fayl topilmadi: ' . $article->pdf_path);
        }

        // ========================================
        // Rasmlarni optimizatsiya qilish (Size reduction)
        // ========================================
        $headerImgPath = $this->getOptimizedImagePath(public_path('images/logo.png'), 150);
        $logoPath = $this->getOptimizedImagePath(public_path('images/logo.png'), 250);

        // References ma'lumotlarini tayyorlash (inline chizish uchun)
        $referencesLines = [];
        if (!empty($article->references)) {
            $rawRefs = explode("\n", $article->references);
            foreach ($rawRefs as $ref) {
                $ref = trim($ref);
                if (!empty($ref)) {
                    $referencesLines[] = $ref;
                }
            }
        }

        // ULTRA HIGH compression settings
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetCompression(true);
        $pdf->setFontSubsetting(false); // DISABLE subsetting - saves memory!
        $pdf->setJPEGQuality(15); // MAXIMUM compression
        $pdf->setImageScale(1.53);

        $pdf->SetCreator('Artiqle - International Scientific Conferences');
        $pdf->SetAuthor($article->author_name ?? 'Unknown Author');
        $pdf->SetTitle($article->title);
        $pdf->SetAutoPageBreak(false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);

        // Asl PDF sahifalarini qo'shish
        try {
            // Compress imported pages to reduce size
            $pdf->SetCompression(true);
            $originalPageCount = $pdf->setSourceFile($basePdfPath);
        } catch (\Exception $e) {
            throw new \Exception('PDF faylni o\'qishda xatolik: ' . $e->getMessage());
        }

        // Davlat ranglarini olish (references uchun)
        $countryColors = $this->getCountryColors($country->code ?? 'GB');
        $primaryRgb = $this->hexToRgb($countryColors['primary']);

        // ===== Professional Academic Conference Layout Parameters =====
        // International indexing standards (A4: 210x297mm)

        $sidebarWidth = 28; // 28mm match with blade
        $leftMargin = $sidebarWidth + 5; // 5mm spacing after banner
        $rightMargin = 15; // 15mm right margin (wide text)
        $topMargin = 20; // 20mm top margin
        $bottomMargin = 20; // 20mm bottom margin

        $availableWidth = 210 - $leftMargin - $rightMargin; // ~145mm content width
        $pageHeight = 297;
        $footerHeight = 15; // 15mm footer zone for page numbers
        $safeBuffer = 6;    // Xavfsiz zona: matn uzilishini OLDINI OLISH uchun
        // Content footerHeight + safeBuffer dan OLDIN to'xtaydi
        // Mask esa safeBuffer zonasini yopadi

        // =====================================================
        // Barcha asl PDF sahifalarni oldindan import qilish
        // (Content Stream uchun kerak - bir sahifa bir nechta
        //  chiqish sahifasida ko'rsatilishi mumkin)
        // =====================================================
        $importedPages = [];
        for ($i = 1; $i <= $originalPageCount; $i++) {
            $importedPages[$i] = $pdf->importPage($i);
        }

        // Asl PDF ning HAQIQIY o'lchamlarini olish (A4 deb taxmin qilmasdan)
        $firstPageSize = $pdf->getTemplateSize($importedPages[1]);
        $origWidth = $firstPageSize['width'];   // Haqiqiy kenglik (mm)
        $origHeight = $firstPageSize['height']; // Haqiqiy balandlik (mm)

        // Masshtab koeffitsienti - asl PDF ni available width ga moslaymiz
        $scaleFactor = $availableWidth / $origWidth;
        $scaledOriginalHeight = $origHeight * $scaleFactor;

        // =====================================================
        // Asl PDF marginlarini O'TKAZIB YUBORISH (bo'sh joylarni kamaytirish)
        // Word/LibreOffice default margin: 25.4mm (1 inch)
        // Chap, yuqori, pastki marginlarni skip qilamiz
        // =====================================================
        $origTopMarginScaled = 20 * $scaleFactor;     // Yuqori margin skip
        $origBottomMarginScaled = 25 * $scaleFactor;   // Pastki margin skip (asl PDF footer ni ham o'tkazib yuborish)
        $origLeftMarginScaled = 20 * $scaleFactor;     // Chap margin skip - contentni chapga surish

        // Har bir asl sahifaning effektiv content tugash nuqtasi
        $effectivePageEnd = $scaledOriginalHeight - $origBottomMarginScaled;

        // =====================================================
        // CONTENT STREAM ALGORITMI
        // Barcha asl sahifalarni uzluksiz oqim sifatida ko'rib,
        // chiqish sahifalariga to'ldiramiz.
        // Sig'magan qism keyingi sahifada DAVOM etadi.
        // Marginlar o'tkazib yuboriladi - bo'sh joy kamayadi.
        // =====================================================
        // =====================================================
        // CONTENT STREAM ALGORITMI (Flow)
        // Matn sig'magan qism keyingi sahifada DAVOM etadi.
        // =====================================================

        // Asl PDF ning taxminiy marginlari (Word default: 25.4mm ≈ 1 inch)
        // Yuqori marginni oshirish — asl PDF tepasidagi bo'sh joyni o'tkazib yuboradi
        $srcTopMargin = 25;
        $srcLeftMargin = 15;
        $srcBottomMargin = 15;
        // Asl tarkibning taxminiy o'ng margini ham 15mm deb olamiz
        $srcRightMargin = 15;

        // GLOBAL SCALE Calculation - Consistency across pages
        // Available Width on Output: $availableWidth (Calculated above: 210 - $leftMargin - $rightMargin) 
        // ~167mm (agar left=28+5=33, right=15 bo'lsa)

        // Source Content Width: 210 - $srcLeftMargin - $srcRightMargin = 180mm

        $globalScale = $availableWidth / (210 - $srcLeftMargin - $srcRightMargin);
        if ($globalScale > 1)
            $globalScale = 1; // Don't upscale

        // Stream State Variables
        $currentSourcePageIdx = 1;
        $currentSourceY = $srcTopMargin; // Start reading source from top margin

        // Output State Variables
        $outputPageCount = 1;

        // --- START PAGE 1 ---
        $pdf->AddPage();
        $this->drawIncopSidebar($pdf, 28, $headerImgPath, $logoPath);

        // 1-sahifa Header (Katta header)
        $headerHeight = $this->drawIncopHeader($pdf, $article, $country, $conference, $leftMargin);
        $currentOutY = $headerHeight + 2; // Start content below header

        $outBottomLimit = 280; // Footer mask bilan ANIQ mos (sahifa raqami uchun 17mm)

        $this->drawIncopFooter($pdf, $article, $country, $outputPageCount, 0, $leftMargin);

        while ($currentSourcePageIdx <= $originalPageCount) {
            $templateId = $importedPages[$currentSourcePageIdx];

            // Oxirgi sahifada ham to'liq matn chizilishi kerak, kesib tashlanmaydi
            $sourcePageLimitY = 297 - $srcBottomMargin;

            $remainingSourceH = $sourcePageLimitY - $currentSourceY;

            // If practically empty, skip to next source
            if ($remainingSourceH < 0.5) {
                $currentSourcePageIdx++;
                $currentSourceY = $srcTopMargin;
                continue;
            }

            // How much vertical space is left on current Output Page?
            $spaceOnOutput = $outBottomLimit - $currentOutY;

            // Agar juda oz joy qolsa, yangi sahifaga o'tish (matn uzilishini oldini olish)
            if ($spaceOnOutput < 8) {
                // To'g'ridan-to'g'ri yangi sahifa ochish
                $pdf->AddPage();
                $outputPageCount++;

                $this->drawIncopSidebar($pdf, 28, $headerImgPath, $logoPath);
                $runningHeaderY = $this->drawIncopRunningHeader($pdf, $article, $conference, $leftMargin);
                $this->drawIncopFooter($pdf, $article, $country, $outputPageCount, 0, $leftMargin);

                $currentOutY = 25;
                $outBottomLimit = 280;
                $spaceOnOutput = $outBottomLimit - $currentOutY;
            }

            // Convert remaining source height to output scale
            $neededOutputH = $remainingSourceH * $globalScale;

            // Determine Chunk Size
            if ($neededOutputH <= $spaceOnOutput) {
                // FITS completely
                $printH = $neededOutputH;
                $advanceSourceMm = $remainingSourceH; // Consume all
                $isSourceFinished = true;
            } else {
                // DOES NOT FIT - Slice it
                $printH = $spaceOnOutput;
                $advanceSourceMm = $printH / $globalScale;
                $isSourceFinished = false;
            }

            // Draw the Slice using Clipping
            // Calculate Template Position to align the slice:
            // Source Point ($currentSourceY) -> Output Point ($currentOutY)
            // Template Y (top of source page 0) = $currentOutY - ($currentSourceY * $globalScale)

            $templateY = $currentOutY - ($currentSourceY * $globalScale);
            $templateX = $leftMargin - ($srcLeftMargin * $globalScale);
            $templateW = 210 * $globalScale;
            $templateH = 297 * $globalScale;

            $pdf->StartTransform();
            $pdf->Rect($leftMargin, $currentOutY, $availableWidth, $printH, 'CNZ');
            $pdf->useTemplate($templateId, $templateX, $templateY, $templateW, $templateH);
            $pdf->StopTransform();

            // Avvalgi noto'g'ri orqaga qaytish logikasi olib tashlandi.
            $currentOutY += $printH;
            $currentSourceY += $advanceSourceMm;

            // Handle Source Page Completion
            if ($isSourceFinished) {
                $currentSourcePageIdx++;
                $currentSourceY = $srcTopMargin;
                // Bo'shliq QO'SHILMAYDI - references to'g'ridan-to'g'ri davom etishi uchun
            }

            // Handle Output Page Full
            // Check if we are at limit (using small epsilon)
            if ($currentOutY >= ($outBottomLimit - 0.5)) {
                if (!$isSourceFinished || $currentSourcePageIdx <= $originalPageCount) {
                    // Create New Output Page
                    $pdf->AddPage();
                    $outputPageCount++;

                    $this->drawIncopSidebar($pdf, 28, $headerImgPath, $logoPath);
                    $runningHeaderY = $this->drawIncopRunningHeader($pdf, $article, $conference, $leftMargin);
                    $this->drawIncopFooter($pdf, $article, $country, $outputPageCount, 0, $leftMargin);

                    // Content header maskasi bilan ANIQ mos — 25mm
                    $currentOutY = 25;
                    // Footer mask bilan ANIQ mos — 280mm
                    $outBottomLimit = 280;
                }
            }
        }

        // =====================================================
        // 2. FOYDALANILGAN ADABIYOTLAR (Inline - matn tagida davom etadi)
        // =====================================================
        if (!empty($referencesLines)) {
            // Joriy sahifada qolgan joyni tekshirish
            $spaceLeft = $outBottomLimit - $currentOutY;

            // Agar juda oz joy qolsa (sarlavha ham sig'maydigan bo'lsa), yangi sahifa
            if ($spaceLeft < 15) {
                $pdf->AddPage();
                $outputPageCount++;
                $this->drawIncopSidebar($pdf, 28, $headerImgPath, $logoPath);
                $this->drawIncopRunningHeader($pdf, $article, $conference, $leftMargin);
                $this->drawIncopFooter($pdf, $article, $country, $outputPageCount, 0, $leftMargin);
                $currentOutY = 25;
                $outBottomLimit = 280;
            }

            // Sarlavha - MARKAZDA, Bold, Italic (rasmga mos)
            $pdf->SetY($currentOutY);
            $pdf->SetX($leftMargin);
            $pdf->SetFont('times', 'BI', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell($availableWidth, 5, 'FOYDALANILGAN ADABIYOTLAR:', 0, 1, 'C');
            $currentOutY = $pdf->GetY() + 1;

            // Har bir references qatorni chizish
            foreach ($referencesLines as $refLine) {
                // Bu qator uchun kerakli balandlikni hisoblash
                $pdf->SetFont('times', '', 12);
                $lineHeight = $pdf->getStringHeight($availableWidth, $refLine);

                // Sahifada joy borligini tekshirish
                if (($currentOutY + $lineHeight) > $outBottomLimit) {
                    // Yangi sahifa kerak
                    $pdf->AddPage();
                    $outputPageCount++;
                    $this->drawIncopSidebar($pdf, 28, $headerImgPath, $logoPath);
                    $this->drawIncopRunningHeader($pdf, $article, $conference, $leftMargin);
                    $this->drawIncopFooter($pdf, $article, $country, $outputPageCount, 0, $leftMargin);
                    $currentOutY = 25;
                    $outBottomLimit = 280;
                }

                // References qatorni yozish - Times 12pt (asosiy matn bilan bir xil)
                $pdf->SetY($currentOutY);
                $pdf->SetX($leftMargin);
                $pdf->SetFont('times', '', 12);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->MultiCell($availableWidth, 5, $refLine, 0, 'J');
                $currentOutY = $pdf->GetY() + 0.3;
            }
        }

        // =====================================================
        // 3. SAQLASH
        // =====================================================
        $filename = 'formatted_' . time() . '_' . $article->id . '.pdf';
        $path = 'articles/formatted/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->Output($fullPath, 'F');

        // Memory ni tozalash
        unset($pdf);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles(); // Garbage collection
        }

        // Sahifa sonini yangilash
        $article->update(['page_count' => $outputPageCount]);

        return $path;
    }

    /**
     * Cover Page + Content Stream + References
     * 
     * Layout:
     * Sahifa 1 yuqori: Header (konferensiya, sarlavha, muallif, annotatsiya, kalit so'zlar)
     * Sahifa 1 pastki: Maqola matni shu yerdan boshlanadi
     * Sahifalar 2..N: Maqola matni davom etadi (sidebar bilan)
     * Oxirgi: Foydalanilgan adabiyotlar
     */
    public function mergeWithCoverPage(Article $article, Country $country): string
    {
        // Maximum resources
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $article->load(['conference']);
        $conference = $article->conference;

        // Asl PDF yo'lini olish
        $basePdfPath = Storage::disk('public')->path($article->pdf_path);

        if (!file_exists($basePdfPath)) {
            throw new \Exception('Asl PDF fayl topilmadi: ' . $article->pdf_path);
        }

        // ========================================
        // Rasmlarni optimizatsiya qilish
        // ========================================
        $headerImgPath = $this->getOptimizedImagePath(public_path('images/logo.png'), 150);
        $logoPath = $this->getOptimizedImagePath(public_path('images/logo.png'), 250);

        // Adabiyotlar endi DOCX ichidan to'g'ridan to'g'ri o'qiladi yoki shablon oxirida qoldiriladi.
        // Hozirgi $article->references array ini alohida append qilish uchun tayyorlash:
        $referencesLines = [];
        if (!empty($article->references)) {
            $rawRefs = explode("\n", $article->references);
            foreach ($rawRefs as $ref) {
                $ref = trim($ref);
                if (!empty($ref)) {
                    $referencesLines[] = $ref;
                }
            }
        }

        // ========================================
        // FPDI yaratish va asl PDF ni import qilish
        // ========================================
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetCompression(true);
        $pdf->setFontSubsetting(false);
        $pdf->setJPEGQuality(15);
        $pdf->setImageScale(1.53);

        $pdf->SetCreator('Artiqle - International Scientific Conferences');
        $pdf->SetAuthor($article->author_name ?? 'Unknown Author');
        $pdf->SetTitle($article->title);
        $pdf->SetAutoPageBreak(false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);

        // Asl PDF ni import qilish
        try {
            $originalPageCount = $pdf->setSourceFile($basePdfPath);
        } catch (\Exception $e) {
            throw new \Exception('PDF faylni o\'qishda xatolik: ' . $e->getMessage());
        }

        // Barcha sahifalarni oldindan import qilish
        $importedPages = [];
        for ($i = 1; $i <= $originalPageCount; $i++) {
            $importedPages[$i] = $pdf->importPage($i);
        }

        // =====================================================
        // LAYOUT PARAMETRLARI
        // =====================================================
        $sidebarWidth = 28;
        $leftMargin = $sidebarWidth + 5; // 33mm
        $rightMargin = 15;
        $contentWidth = 210 - $leftMargin - $rightMargin; 

        // =====================================================
        // 1-SAHIFA: MAQOLA MATNI + HEADER
        // =====================================================
        $pdf->AddPage();
        $outputPageCount = 1;

        // Puppeteer generatsiya qilgan base PDF da birinchi sahifada tepadagi header
        // hududi oq bo'shliq qilib ajratilgan, shuning uchun avval shuni bostiramiz.
        $pdf->useTemplate($importedPages[1], 0, 0, 210, 297);

        $this->drawIncopSidebar($pdf, $sidebarWidth, $headerImgPath, $logoPath);
        $this->drawIncopHeader($pdf, $article, $country, $conference, $leftMargin);
        $this->drawIncopFooter($pdf, $article, $country, $outputPageCount, 0, $leftMargin);

        // =====================================================
        // QOLGAN SAHIFALAR: Matn davom etadi + Running header
        // =====================================================
        for ($i = 2; $i <= $originalPageCount; $i++) {
            $pdf->AddPage();
            $outputPageCount++;
            
            // Base PDF dan shunchaki sahifani overlay qilamiz (marginlari tayyor)
            $pdf->useTemplate($importedPages[$i], 0, 0, 210, 297);

            $this->drawIncopSidebar($pdf, $sidebarWidth, $headerImgPath, $logoPath);
            $this->drawIncopRunningHeader($pdf, $article, $conference, $leftMargin);
            $this->drawIncopFooter($pdf, $article, $country, $outputPageCount, 0, $leftMargin);
        }

        // Note: Foydalanilgan adabiyotlar endi HTML tarkibida keladi va Puppeteer tomonidan flow qilinadi.
        // PHP orqali manual chizish olib tashlandi.

        // =====================================================
        // SAQLASH
        // =====================================================
        $filename = 'formatted_' . time() . '_' . $article->id . '.pdf';
        $path = 'articles/formatted/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->Output($fullPath, 'F');

        // Memory tozalash
        unset($pdf);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        // Sahifa sonini yangilash
        $article->update(['page_count' => $outputPageCount]);

        return $path;
    }

    /**
     * Rasmni optimizatsiya qilish (Kichraytirish va JPG ga o'girish)
     * Hajmni kamaytirish uchun
     */
    private function getOptimizedImagePath(string $path, int $maxWidth = 250): string
    {
        if (!file_exists($path)) {
            return '';
        }

        // Fayl hashiga qarab cache qilish (maxWidth ham hashga qo'shiladi)
        $hash = md5_file($path);
        $tempDir = storage_path('app/temp/images');
        $tempPath = $tempDir . '/' . $hash . '_w' . $maxWidth . '.jpg';

        if (file_exists($tempPath)) {
            return $tempPath;
        }

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $info = getimagesize($path);
        if (!$info)
            return $path;

        $mime = $info['mime'];
        $width = $info[0];
        $height = $info[1];

        // Yangi o'lcham (Optimizatsiya)
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($height / $width) * $newWidth;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $image = null;
        if ($mime == 'image/jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($mime == 'image/png') {
            $image = imagecreatefrompng($path);
        }

        if (!$image)
            return $path;

        $bg = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefilledrectangle($bg, 0, 0, $newWidth, $newHeight, $white);

        // Resize and Copy (Resampled for smooth scaling)
        imagecopyresampled($bg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save as JPG with quality 20 (ULTRA aggressive compression)
        imagejpeg($bg, $tempPath, 20);

        imagedestroy($image);
        imagedestroy($bg);

        return $tempPath;
    }

    private function drawIncopSidebar($pdf, $width = 40, $headerImgPath = '', $logoPath = ''): void
    {
        // KENG SIDEBAR
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Rect(0, 0, $width, 297, 'F');
        $pdf->Line($width, 0, $width, 297);

        // 1. TEPADAGI LOGO (Kitob/Globus) - Gorizontal
        $topLogoPath = public_path('images/isc-globe.png');
        if (!file_exists($topLogoPath)) {
            $topLogoPath = public_path('images/logo.png'); // Fallback
        }

        // Optimize image size (250px width max)
        $topLogoPath = $this->getOptimizedImagePath($topLogoPath, 250);

        if (file_exists($topLogoPath)) {
            // Markazda, tepadan biroz pastroq
            // Image(file, x, y, w, h)
            $pdf->Image($topLogoPath, ($width - 24) / 2, 5, 24);
        }

        // LOGO VA MATN JOYLASHUVI
        // Pastki logo vertikal. Matn pastga tushiriladi.

        // 4. PASTDAGI LOGO (ISC) - KICHRAYTIRILGAN VA CHAPGA SURILGAN
        // Y=240 dan boshlanadi
        $bottomLogoY = 260;

        if (empty($logoPath)) {
            $logoPath = public_path('images/logo.png');
        }

        if (file_exists($logoPath)) {
            $pdf->StartTransform();
            // Rotate center X=8 (Align center, 24/2 - visual offset), Y=260
            $pdf->Rotate(90, 8, $bottomLogoY);
            // Logo o'lchami 45mm (Kichraytirish, 60 edi)
            $pdf->Image($logoPath, -10, $bottomLogoY - 11, 45);
            $pdf->StopTransform();
        }

        // 2. O'RTADAGI MATN (Vertikal)
        // Matnni joyiga qaytaramiz (Y=220) - 30pt font bilan tepaga tegib qolmaydi
        $textCenterY = 220;

        $pdf->StartTransform();
        $pdf->Rotate(90, 2, $textCenterY); // O'ngroqqa (X=2), Pastroqqa (Y=220)
        $pdf->SetFont('times', 'B', 30);
        $pdf->SetTextColor(204, 102, 0); // ORANGE
        $pdf->Text(2, $textCenterY, 'International Scientific Conferences');
        $pdf->StopTransform();

        // 3. SUBTITLE
        // Asosiy matn o'ngga surilganda, buni ham surish kerak (X=16)
        $pdf->StartTransform();
        $pdf->Rotate(90, 16, $textCenterY);
        $pdf->SetFont('times', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Text(16, $textCenterY, 'Open Access | Scientific online | Conference Proceedings');
        $pdf->StopTransform();
    }

    /**
     * Helper to calculate the exact header height before drawing
     */
    private function calculateHeaderHeight(Article $article, Country $country, Conference $conference, float $contentWidth): float
    {
        // Minimal PDF instance for measuring text
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->AddPage();
        
        $currentY = 15; // Starting Y

        // 1. Conference Name
        $pdf->SetFont('helvetica', 'B', 9);
        $currentY += 5; // Cell height

        // 2. Subtitle
        $pdf->SetFont('helvetica', '', 7);
        $currentY += 4; // Cell height

        // 3. Separator line
        $currentY += 1; // Gap
        $currentY += 4; // Padding

        // 4. Info Block heights
        $titleObj = strtoupper($article->title);
        $authorObj = $article->author_name;
        $affiliationObj = $article->author_affiliation ?? '';
        $abstractObj = strip_tags($article->abstract);
        $keywordsObj = strip_tags($article->keywords);

        $pdf->SetFont('times', 'B', 14);
        $titleH = $pdf->getStringHeight($contentWidth, $titleObj);

        $pdf->SetFont('times', 'B', 12);
        $authorH = $pdf->getStringHeight($contentWidth, $authorObj);

        $pdf->SetFont('times', 'I', 10);
        $affiliationH = empty($affiliationObj) ? 0 : $pdf->getStringHeight($contentWidth, $affiliationObj);

        $pdf->SetFont('times', '', 11);
        $abstractH = empty($abstractObj) ? 0 : $pdf->getStringHeight($contentWidth - 6, "Annotatsiya: " . $abstractObj) + 4;

        $pdf->SetFont('times', 'I', 11);
        $keywordsH = empty($keywordsObj) ? 0 : $pdf->getStringHeight($contentWidth - 6, "Kalit so'zlar: " . $keywordsObj) + 4;

        $padding = 5;
        $gap = 3;
        $totalBlockHeight = $titleH + $gap + $authorH + ($affiliationH ? 1 + $affiliationH : 0) + $gap 
                            + ($abstractH ? $abstractH + $gap + 2 : 0) 
                            + ($keywordsH ? $keywordsH + $gap + 2 : 0) 
                            + $padding * 2;

        return $currentY + $totalBlockHeight;
    }

    /**
     * INCOP Header chizish (Rasmga mos)
     * @return float Header balandligi
     */
    private function drawIncopHeader($pdf, $article, $country, $conference, $leftMargin = 28): float
    {
        $margins = $pdf->getMargins();
        $pageWidth = 210;
        $contentWidth = $pageWidth - $leftMargin - 15; // 15mm right margin
        $centerX = $leftMargin + ($contentWidth / 2);

        // Davlat bayroq ranglarini olish
        $countryColors = $this->getCountryColors($country->code ?? 'GB');
        $primaryRgb = $this->hexToRgb($countryColors['primary']);
        $secondaryRgb = $this->hexToRgb($countryColors['secondary']);
        $accentRgb = $this->hexToRgb($countryColors['accent']);

        // Ranglarni ochiqroq (pastel) qilish funksiyasi - orqa fon uchun
        // 85% oq bilan aralashtirish = juda ochiq rang
        $lightPrimary = [
            'r' => (int) ($primaryRgb['r'] + (255 - $primaryRgb['r']) * 0.82),
            'g' => (int) ($primaryRgb['g'] + (255 - $primaryRgb['g']) * 0.82),
            'b' => (int) ($primaryRgb['b'] + (255 - $primaryRgb['b']) * 0.82),
        ];
        $lightSecondary = [
            'r' => (int) ($secondaryRgb['r'] + (255 - $secondaryRgb['r']) * 0.82),
            'g' => (int) ($secondaryRgb['g'] + (255 - $secondaryRgb['g']) * 0.82),
            'b' => (int) ($secondaryRgb['b'] + (255 - $secondaryRgb['b']) * 0.82),
        ];

        $currentY = 15;

        // 1. CONFERENCE NAME (Davlat primary rangida, Uppercase, Bold)
        $pdf->SetY($currentY);
        $pdf->SetX($leftMargin);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor($primaryRgb['r'], $primaryRgb['g'], $primaryRgb['b']);
        $confName = strtoupper($country->conference_name ?? 'INTERNATIONAL SCIENTIFIC CONFERENCES OF MODERN TECHNOLOGIES');
        $pdf->Cell($contentWidth, 5, $confName, 0, 1, 'C');

        // 2. Subtitle (Grey, Date, Country)
        $pdf->SetX($leftMargin);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(100, 100, 100);
        $confTitle = ucfirst(strtolower($conference->title ?? 'International scientific conferences'));
        $confDate = $conference->conference_date ? $conference->conference_date->format('F d, Y') : date('F d, Y');
        $countryName = $country->name_en ?? $country->name;
        $pdf->Cell($contentWidth, 4, "$confTitle • $confDate • $countryName", 0, 1, 'C');

        // 3. Separator Line (davlat primary rangida)
        $currentY = $pdf->GetY() + 1;
        $pdf->SetDrawColor($primaryRgb['r'], $primaryRgb['g'], $primaryRgb['b']);
        $pdf->SetLineWidth(0.3);
        $pdf->Line($leftMargin, $currentY, $pageWidth - 15, $currentY);

        $currentY += 4;

        // 4. ARTICLE CONTENT BLOCK (Title, Author, Abstract, Keywords)
        // Davlat bayroq ranglariga asoslangan orqa fon

        $titleObj = strtoupper($article->title);

        $authorsListObj = [];
        $mainAuthor = mb_convert_case(trim($article->author_display_name), MB_CASE_TITLE, 'UTF-8');
        if (!empty($mainAuthor)) {
            $authorsListObj[] = $mainAuthor;
        }

        if (!empty($article->co_authors)) {
            $coAuthorsRaw = explode("\n", trim($article->co_authors));
            foreach ($coAuthorsRaw as $ca) {
                $nameParts = explode(',', $ca);
                $caName = mb_convert_case(trim($nameParts[0]), MB_CASE_TITLE, 'UTF-8');
                if (!empty($caName)) {
                    $authorsListObj[] = $caName;
                }
            }
        }
        $authorObj = implode(', ', $authorsListObj);

        $affiliationObj = $article->author_affiliation ?? '';
        $abstractObj = strip_tags($article->abstract);
        $keywordsObj = strip_tags($article->keywords);

        // Calculate Heights
        $pdf->SetFont('times', 'B', 14);
        $titleH = $pdf->getStringHeight($contentWidth, $titleObj);

        $pdf->SetFont('times', 'B', 12);
        $authorH = $pdf->getStringHeight($contentWidth, $authorObj);

        $pdf->SetFont('times', 'I', 10);
        $affiliationH = empty($affiliationObj) ? 0 : $pdf->getStringHeight($contentWidth, $affiliationObj);

        $pdf->SetFont('times', '', 11);
        $abstractH = empty($abstractObj) ? 0 : $pdf->getStringHeight($contentWidth, "Annotatsiya: " . $abstractObj);

        $pdf->SetFont('times', 'I', 11);
        $keywordsH = empty($keywordsObj) ? 0 : $pdf->getStringHeight($contentWidth, "Kalit so'zlar: " . $keywordsObj);

        $padding = 5;
        $gap = 3;

        $totalBlockHeight = $titleH + $gap + $authorH + ($affiliationH ? $gap + $affiliationH : 0) + $gap + $abstractH + ($abstractH ? $gap : 0) + $keywordsH + ($keywordsH ? $gap : 0) + $padding * 2;

        // Umumiy blok uchun juda ochiq primary rang fon
        $veryLightPrimary = [
            'r' => (int) ($primaryRgb['r'] + (255 - $primaryRgb['r']) * 0.92),
            'g' => (int) ($primaryRgb['g'] + (255 - $primaryRgb['g']) * 0.92),
            'b' => (int) ($primaryRgb['b'] + (255 - $primaryRgb['b']) * 0.92),
        ];
        $pdf->SetFillColor($veryLightPrimary['r'], $veryLightPrimary['g'], $veryLightPrimary['b']);
        $pdf->Rect($leftMargin, $currentY, $contentWidth, $totalBlockHeight, 'F');

        // Chap tomonda davlat rangli chegaraviy chiziq (3mm kenglikda)
        $pdf->SetFillColor($primaryRgb['r'], $primaryRgb['g'], $primaryRgb['b']);
        $pdf->Rect($leftMargin, $currentY, 2.5, $totalBlockHeight, 'F');

        $innerY = $currentY + $padding;

        // Title
        $pdf->SetY($innerY);
        $pdf->SetX($leftMargin);
        $pdf->SetFont('times', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell($contentWidth, 6, $titleObj, 0, 'C');
        $innerY = $pdf->GetY() + $gap;

        // Author
        $pdf->SetY($innerY);
        $pdf->SetX($leftMargin);
        $pdf->SetFont('times', 'B', 12);
        $pdf->MultiCell($contentWidth, 6, $authorObj, 0, 'C');
        $innerY = $pdf->GetY();

        // Author Affiliation (Ish joyi / O'qish joyi)
        if (!empty($affiliationObj)) {
            $innerY += 1;
            $pdf->SetY($innerY);
            $pdf->SetX($leftMargin);
            $pdf->SetFont('times', 'I', 10);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->MultiCell($contentWidth, 5, $affiliationObj, 0, 'C');
            $pdf->SetTextColor(0, 0, 0);
            $innerY = $pdf->GetY();
        }
        $innerY += $gap;

        // Abstract - davlat primary rangining ochiq fonida
        if (!empty($abstractObj)) {
            $abstractStartY = $innerY;
            // Annotatsiya uchun oldindan balandlikni hisoblash
            $pdf->SetFont('times', '', 11);
            $tempAbstractH = $pdf->getStringHeight($contentWidth - 6, "Annotatsiya: " . $abstractObj);
            $abstractBoxH = $tempAbstractH + 4; // 2mm yuqori + 2mm pastki padding

            // Annotatsiya orqa foni - davlat primary rangidan kelib chiqib
            $pdf->SetFillColor($lightPrimary['r'], $lightPrimary['g'], $lightPrimary['b']);
            $pdf->Rect($leftMargin + 3, $abstractStartY, $contentWidth - 3, $abstractBoxH, 'F');

            // Annotatsiya chap chegarasi - secondary rang
            $pdf->SetFillColor($secondaryRgb['r'], $secondaryRgb['g'], $secondaryRgb['b']);
            $pdf->Rect($leftMargin + 3, $abstractStartY, 2, $abstractBoxH, 'F');

            $pdf->SetY($abstractStartY + 2);
            $pdf->SetX($leftMargin + 6);
            $pdf->SetFont('times', '', 11);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->MultiCell($contentWidth - 6, 5, "Annotatsiya: " . $abstractObj, 0, 'J');
            $innerY = $pdf->GetY() + $gap + 2;
        }

        // Keywords - davlat secondary rangining ochiq fonida
        if (!empty($keywordsObj)) {
            $keywordsStartY = $innerY;
            // Kalit so'zlar uchun oldindan balandlikni hisoblash
            $pdf->SetFont('times', 'I', 11);
            $tempKeywordsH = $pdf->getStringHeight($contentWidth - 6, "Kalit so'zlar: " . $keywordsObj);
            $keywordsBoxH = $tempKeywordsH + 4; // 2mm yuqori + 2mm pastki padding

            // Kalit so'zlar orqa foni - davlat secondary rangidan kelib chiqib
            $pdf->SetFillColor($lightSecondary['r'], $lightSecondary['g'], $lightSecondary['b']);
            $pdf->Rect($leftMargin + 3, $keywordsStartY, $contentWidth - 3, $keywordsBoxH, 'F');

            // Kalit so'zlar chap chegarasi - accent rang
            $pdf->SetFillColor($accentRgb['r'], $accentRgb['g'], $accentRgb['b']);
            $pdf->Rect($leftMargin + 3, $keywordsStartY, 2, $keywordsBoxH, 'F');

            $pdf->SetY($keywordsStartY + 2);
            $pdf->SetX($leftMargin + 6);
            $pdf->SetFont('times', 'I', 11);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->MultiCell($contentWidth - 6, 5, "Kalit so'zlar: " . $keywordsObj, 0, 'J');
            $innerY = $pdf->GetY() + $gap + 2;
        }

        return $innerY + 5; // Return bottom position (5mm gap before content starts)
    }

    /**
     * INCOP Running Header (Har bir sahifa uchun)
     */
    private function drawIncopRunningHeader($pdf, $article, $conference, $leftMargin = 28): float
    {
        $pageWidth = 210;
        $contentWidth = $pageWidth - $leftMargin - 15;

        // Running header uchun oq fon maskasi
        // contentStartOnContinuation (20mm) ga mos ravishda
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($leftMargin, 0, $pageWidth - $leftMargin, 20, 'F');

        $currentY = 8;
        $pdf->SetY($currentY);
        $pdf->SetX($leftMargin);

        // Conference Title and Date
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(30, 78, 121);

        $confTitle = mb_strtoupper($conference->title ?? 'International Scientific Conference');
        if (mb_strlen($confTitle) > 80)
            $confTitle = mb_substr($confTitle, 0, 80) . '...';

        $date = $conference->conference_date ? $conference->conference_date->format('d.m.Y') : date('d.m.Y');

        $pdf->Cell($contentWidth, 5, "$confTitle • $date", 0, 1, 'C');

        // Line - header tagida ajratuvchi chiziq
        $y = $pdf->GetY() + 2;
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetLineWidth(0.3);
        $pdf->Line($leftMargin, $y, $pageWidth - 15, $y);

        return $y + 3; // 3mm gap chiziqdan keyin
    }

    /**
     * INCOP Footer chizish (Rasmga mos)
     */
    private function drawIncopFooter($pdf, $article, $country, $pageNo, $totalPages, $leftMargin = 28): void
    {
        $pageWidth = 210;
        // Footer maskasi — kichik: faqat sahifa raqami uchun joy
        // Content 280mm da to'xtaydi, qolgan 17mm footer uchun
        $footerMaskStart = 280; // outBottomLimit bilan ANIQ mos
        $footerMaskHeight = 297 - $footerMaskStart; // 17mm
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, $footerMaskStart, $pageWidth, $footerMaskHeight, 'F');

        // Sahifa raqami - pastga surilgan, markazda
        $pdf->SetY(290);
        $pdf->SetX($leftMargin);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell($pageWidth - $leftMargin - 5, 5, (string) $pageNo, 0, 0, 'C');
    }

    /**
     * Minimal footer - faqat sahifa raqami va website
     * Asl PDF kontentiga minimal ta'sir qilish uchun
     */
    private function drawMinimalFooter($pdf, $article, int $pageNo, int $totalPages, array $colors, float $pageWidth, float $pageHeight): void
    {
        $rgb = $this->hexToRgb($colors['primary']);

        // Pastda juda yupqa rang chizig'i (1.5mm)
        $pdf->SetFillColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->Rect(0, $pageHeight - 1.5, $pageWidth, 1.5, 'F');

        // Sahifa raqami (markazda, pastda)
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->SetXY(0, $pageHeight - 6);
        $pdf->Cell($pageWidth, 4, $pageNo . ' / ' . $totalPages, 0, 0, 'C');

        // Website (o'ng pastda)
        $pdf->SetFont('helvetica', '', 5);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY($pageWidth - 55, $pageHeight - 6);
        $pdf->Cell(50, 4, 'internationalscientificconferences.org', 0, 0, 'R');
    }

    /**
     * Overlay: Chap yon panel
     */
    private function drawOverlaySidebar($pdf, array $colors, array $size): void
    {
        $rgb = $this->hexToRgb($colors['primary']);

        // Chap tomonda kengaytirilgan rang chizig'i (8mm) vertikal matn uchun
        $pdf->SetFillColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->Rect(0, 0, 8, $size['height'], 'F');

        // Vertikal matn (pastdan yuqoriga) - INTERNATIONALSCIENTIFICCONFERENCES
        $pdf->SetFont('helvetica', 'B', 6);
        $pdf->SetTextColor(255, 255, 255); // Oq rang

        // Matnni vertikal yozish
        $pdf->StartTransform();
        $pdf->Rotate(90, 5, $size['height'] - 20);
        $pdf->SetXY(5, $size['height'] - 20);
        $pdf->Cell(0, 4, 'INTERNATIONALSCIENTIFICCONFERENCES', 0, 0, 'L');
        $pdf->StopTransform();
    }

    /**
     * Overlay: Yuqori header
     */
    private function drawOverlayHeader($pdf, $country, $conference, array $colors, array $size): void
    {
        $rgb = $this->hexToRgb($colors['primary']);

        // Yuqori rang chizig'i (4mm)
        $pdf->SetFillColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->Rect(0, 0, $size['width'], 4, 'F');

        // Konferensiya nomi (kichik, o'ng tomonda)
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetTextColor(100, 100, 100);
        $conferenceName = $country->conference_name ?? 'INTERNATIONAL SCIENTIFIC CONFERENCE';
        $pdf->SetXY($size['width'] - 100, 5);
        $pdf->Cell(95, 4, mb_strtoupper(mb_substr($conferenceName, 0, 60)), 0, 0, 'R');
    }

    /**
     * Overlay: Pastki footer
     */
    private function drawOverlayFooter($pdf, $article, $conference, int $pageNo, int $totalPages, array $colors, array $size): void
    {
        $rgb = $this->hexToRgb($colors['primary']);
        $country = $conference->country;

        // Pastki rang chizig'i
        $pdf->SetFillColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->Rect(0, $size['height'] - 3, $size['width'], 3, 'F');

        // Footer chizig'i
        $pdf->SetDrawColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(10, $size['height'] - 8, $size['width'] - 10, $size['height'] - 8);

        // Chap: Davlat va sana
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(10, $size['height'] - 7);
        $dateStr = $conference->conference_date ? $conference->conference_date->format('d.m.Y') : date('d.m.Y');
        $pdf->Cell(50, 4, ($country->name_en ?? $country->name) . ' | ' . $dateStr, 0, 0, 'L');

        // O'rta: Sahifa raqami
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->SetXY(0, $size['height'] - 7);
        $pdf->Cell($size['width'], 4, $pageNo, 0, 0, 'C');

        // O'ng: Sahifalar diapazoni va website
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY($size['width'] - 80, $size['height'] - 7);
        $pageRange = $article->page_range ?? $pageNo;
            $pdf->Cell(70, 4, 'pp. ' . $pageRange . ' | internationalscientificconferences.org', 0, 0, 'R');
    }

    /**
     * Matematik va kimyoviy formulalarni HTML formatiga aylantirish
     * Word formulalarni o'qilishi qulay formatda ko'rsatish
     */
    private function processFormulaContent(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        // HTML taglarini ajratib olamiz, toki img src="..." ichidagi base64 kodlar buzilmasin
        $parts = preg_split('/(<[^>]*>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $resultHtml = '';

        foreach ($parts as $part) {
            if (str_starts_with($part, '<')) {
                $resultHtml .= $part;
            } else {
                // [formula] placeholder larni chiroyli box ichida ko'rsatish
                $formulaCounter = 0;
                $part = preg_replace_callback(
                    '/\[formula\]/i',
                    function ($matches) use (&$formulaCounter) {
                        $formulaCounter++;
                        return '<div class="formula-box"><em>[Formula ' . $formulaCounter . ']</em></div>';
                    },
                    $part
                );

                // Matematik ifodalar uchun maxsus formatlar:
                
                // 1. Kasrlar: a/b -> a÷b yoki (a)/(b)
                $part = preg_replace('/\b(\d+)\/(\d+)\b/', '$1÷$2', $part);

                // 2. Daraja: x^2, x^n, 10^6
                $part = preg_replace_callback(
                    '/(\w+)\^(\d+|\{[^}]+\})/',
                    function ($matches) {
                        $base = $matches[1];
                        $exp = trim($matches[2], '{}');
                        $superscripts = ['0' => '⁰', '1' => '¹', '2' => '²', '3' => '³', '4' => '⁴', '5' => '⁵', '6' => '⁶', '7' => '⁷', '8' => '⁸', '9' => '⁹'];
                        $result = '';
                        foreach (str_split($exp) as $char) {
                            $result .= $superscripts[$char] ?? $char;
                        }
                        return $base . $result;
                    },
                    $part
                );

                // 3. Indeks: x_1, x_n, a_i
                $part = preg_replace_callback(
                    '/(\w+)_(\d+|\{[^}]+\})/',
                    function ($matches) {
                        $base = $matches[1];
                        $sub = trim($matches[2], '{}');
                        return $base . '<sub>' . $sub . '</sub>';
                    },
                    $part
                );

                // 4. Kimyoviy formulalar: H2O, CO2, NaCl, CaCO3
                $part = preg_replace_callback(
                    '/\b([A-Z][a-z]?)(\d+)(?=[A-Z]|\s|,|\.|\)|$)/u',
                    function ($matches) {
                        return $matches[1] . '<sub>' . $matches[2] . '</sub>';
                    },
                    $part
                );

                // 5. Matematik belgilar - oddiy matnda
                $mathReplacements = [
                    '+-' => '±', '->' => '→', '<-' => '←', '<=' => '≤', '>=' => '≥',
                    '!=' => '≠', '~=' => '≈', '==' => '≡', '<<' => '≪', '>>' => '≫',
                    'sqrt' => '√', 'infinity' => '∞', 'approx' => '≈', 'prop' => '∝',
                    'empty' => '∅', 'angle' => '∠',
                    'alpha' => 'α', 'beta' => 'β', 'gamma' => 'γ', 'delta' => 'δ',
                    'epsilon' => 'ε', 'theta' => 'θ', 'lambda' => 'λ', 'mu' => 'μ',
                    'pi' => 'π', 'sigma' => 'σ', 'tau' => 'τ', 'phi' => 'φ',
                    'omega' => 'ω', 'Delta' => 'Δ', 'Sigma' => 'Σ', 'Omega' => 'Ω',
                ];

                foreach ($mathReplacements as $search => $replace) {
                    $part = preg_replace('/\b' . preg_quote($search, '/') . '\b/', $replace, $part);
                }

                $specialOps = ['+-', '->', '<-', '<=', '>=', '!=', '~=', '==', '<<', '>>'];
                foreach ($specialOps as $op) {
                    $part = str_replace($op, $mathReplacements[$op], $part);
                }

                // 6. Fizik kattaliklar va o'lchov birliklari
                $units = [
                    'm/s' => 'm/s', 'm/s^2' => 'm/s²', 'kg/m^3' => 'kg/m³',
                    'J/mol' => 'J/mol', 'kJ/mol' => 'kJ/mol',
                ];

                foreach ($units as $search => $replace) {
                    $part = str_replace($search, $replace, $part);
                }

                $resultHtml .= $part;
            }
        }

        return $resultHtml;
    }

    /**
     * Word (.docx) fayldan professional akademik PDF yaratish
     * Matematik formulalar, jadvallar, rasmlar to'g'ri konvertatsiya qilinadi
     * 
     * @param string $wordFilePath Word fayl yo'li
     * @param Article $article Maqola modeli
     * @param Country $country Davlat modeli
     * @return string Yaratilgan PDF fayl yo'li
     */
    public function generateFromWord(string $wordFilePath, Article $article, Country $country): string
    {
        $article->load(['conference']);
        $conference = $article->conference;

        // Word faylni HTML ga aylantirish
        $htmlContent = $this->convertWordToHtml($wordFilePath);

        // Matematik va kimyoviy formulalarni qayta ishlash
        $processedContent = $this->processFormulaContent($htmlContent);

        // PDF shablonini yaratish - akademik format
        $html = view('pdf.article-academic', [
            'article' => $article,
            'conference' => $conference,
            'country' => $country,
            'colors' => $this->getCountryColors($country->code ?? 'GB'),
            'processedContent' => $processedContent,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        // Yuqori sifat uchun optionlar
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Serif');

        // Saqlash
        $filename = 'article_word_' . time() . '_' . $article->id . '.pdf';
        $path = 'articles/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        // Papkani yaratish
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $pdf->output());

        // Article content ni yangilash
        $article->update(['content' => $processedContent]);

        return $path;
    }

    /**
     * Konferensiya proceedings to'liq hujjatini yaratish
     * Muqova + Mundarija + Barcha maqolalar
     */
    public function generateProceedings(Conference $conference): string
    {
        $conference->load([
            'country',
            'articles' => function ($q) {
                $q->where('status', 'published')->orderBy('order_number');
            }
        ]);

        $country = $conference->country;
        $colors = $this->getCountryColors($country->code ?? 'GB');

        // Konferensiya to'liq proceedings PDF
        $html = view('pdf.conference-proceedings', [
            'conference' => $conference,
            'country' => $country,
            'colors' => $colors,
            'articles' => $conference->articles,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        // Saqlash
        $filename = 'proceedings_' . $country->code . '_' . $conference->conference_date->format('Y_m') . '.pdf';
        $path = 'proceedings/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        // Papkani yaratish
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $pdf->output());

        return $path;
    }

    /**
     * Maqolani formatlangan PDF ga aylantirish
     * Davlat ramzlari va sayt nomi bilan bezatilgan
     */
    public function formatArticle(Article $article): string
    {
        $article->load(['conference.country']);
        $country = $article->conference->country;
        $conference = $article->conference;

        // FPDI bilan PDF birlashtirish
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetCreator('ISOC - International Scientific Online Conference');
        $pdf->SetAuthor($article->author_display_name);
        $pdf->SetTitle($article->title);

        // Muqova sahifasi qo'shish
        $coverPath = $this->generateCoverPage($article);

        // Muqova sahifasini qo'shish
        if (file_exists($coverPath)) {
            $pdf->setSourceFile($coverPath);
            $tplId = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tplId, 0, 0, 210, 297);
        }

        // Asl maqola PDF ni qo'shish
        $originalPdfPath = Storage::disk('public')->path($article->pdf_path);

        if (file_exists($originalPdfPath)) {
            $pageCount = $pdf->setSourceFile($originalPdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $pdf->AddPage();
                $pdf->useTemplate($tplId, 0, 0, 210, 297);

                // Har sahifada header va footer qo'shish
                $this->addPageHeader($pdf, $article, $country, $pageNo);
                $this->addPageFooter($pdf, $article, $conference, $pageNo, $pageCount);
            }
        }

        // Saqlash
        $filename = 'formatted_' . time() . '_' . $article->id . '.pdf';
        $path = 'articles/formatted/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        // Papkani yaratish
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->Output($fullPath, 'F');

        return $path;
    }

    /**
     * Cover sahifasi yaratish (DOMPDF yordamida)
     */
    private function generateCoverPage(Article $article): string
    {
        $article->load(['conference.country']);
        $country = $article->conference->country;
        $conference = $article->conference;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.article-cover', [
            'article' => $article,
            'author' => $article->author,
            'conference' => $conference,
            'country' => $country,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'cover_' . time() . '_' . $article->id . '.pdf';
        $path = storage_path('app/temp/' . $filename);

        // Papkani yaratish
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $pdf->output());

        return $path;
    }

    /**
     * Sahifa headerini qo'shish - davlat ranglarida
     */
    private function addPageHeader($pdf, $article, $country, $pageNo): void
    {
        // Davlat ranglari
        $colors = $this->getCountryColors($country->code ?? 'GB');

        // Top border - davlat rangi
        $rgb = $this->hexToRgb($colors['primary']);
        $pdf->SetFillColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->Rect(0, 0, 210, 5, 'F');

        // Header chizig'i
        $pdf->SetDrawColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, 15, 200, 15);

        // Chap tomonda ISOC va davlat
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->SetXY(10, 8);
        $pdf->Cell(80, 5, 'ARTIQLE | ' . strtoupper($country->name_en ?? $country->name), 0, 0, 'L');

        // O'ng tomonda konferensiya nomi
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(100, 8);
        $conferenceName = $country->conference_name ?? $article->conference->title ?? 'International Conference';
        $pdf->Cell(100, 5, mb_substr($conferenceName, 0, 50), 0, 0, 'R');

        // Yon chiziq - chap tomonda
        $pdf->SetFillColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->Rect(0, 20, 3, 260, 'F');

        // Yon chiziq - o'ng tomonda (ochiqlashtirilgan)
        $rgbLight = $this->hexToRgb($colors['secondary']);
        $pdf->SetFillColor($rgbLight['r'], $rgbLight['g'], $rgbLight['b']);
        $pdf->SetAlpha(0.3);
        $pdf->Rect(207, 50, 3, 180, 'F');
        $pdf->SetAlpha(1);
    }

    /**
     * Sahifa footerini qo'shish - davlat ranglarida
     */
    private function addPageFooter($pdf, $article, $conference, $pageNo, $totalPages): void
    {
        $country = $conference->country;
        $colors = $this->getCountryColors($country->code ?? 'GB');
        $rgb = $this->hexToRgb($colors['primary']);

        // Footer chizig'i
        $pdf->SetDrawColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, 280, 200, 280);

        // Bottom border
        $pdf->SetFillColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->Rect(0, 292, 210, 5, 'F');

        $pdf->SetFont('helvetica', '', 8);

        // Chap tomonda - davlat va sana
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(10, 283);
        $pdf->Cell(60, 5, $country->name . ' | ' . $conference->conference_date->format('Y'), 0, 0, 'L');

        // O'rtada sahifa raqami - davlat rangida
        $pdf->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(85, 282);
        $pdf->Cell(40, 7, $article->page_range, 0, 0, 'C');

        // O'ng tomonda website
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(140, 283);
        $pdf->Cell(60, 5, 'www.artiqle.uz', 0, 0, 'R');
    }

    /**
     * Davlat ranglari sxemasi - milliy identifikatsiya uchun
     * primary: asosiy davlat rangi (bayroq dominant rangi)
     * secondary: ikkinchi rang (bayroq ikkinchi rangi)
     * accent: aksent rang (oltin/kumush yoki kontrastli rang)
     * 
     * Qo'llab-quvvatlanadi: ISO 3166-1 alpha-2 (2 harfli) va alpha-3 (3 harfli) kodlar
     */
    private function getCountryColors(string $code): array
    {
        // 3 harfli kodlarni 2 harfliga o'girish
        $alpha3to2 = [
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
            'MYS' => 'MY',
            'SGP' => 'SG',
            'THA' => 'TH',
            'VNM' => 'VN',
            'IDN' => 'ID',
            'PHL' => 'PH',
            'PAK' => 'PK',
            'BGD' => 'BD',
            'EGY' => 'EG',
            'NGA' => 'NG',
            'ZAF' => 'ZA',
            'MEX' => 'MX',
            'ARG' => 'AR',
            'COL' => 'CO',
            'CHL' => 'CL',
            'PER' => 'PE',
            'NZL' => 'NZ',
            'IRL' => 'IE',
            'ISR' => 'IL',
            'AZE' => 'AZ',
            'TJK' => 'TJ',
            'KGZ' => 'KG',
            'AFG' => 'AF',
            'IRN' => 'IR',
            'IRQ' => 'IQ',
            'SYR' => 'SY',
            'JOR' => 'JO',
            'LBN' => 'LB',
        ];

        // Agar 3 harfli kod bo'lsa, 2 harfliga o'girish
        $normalizedCode = strlen($code) === 3 ? ($alpha3to2[strtoupper($code)] ?? strtoupper(substr($code, 0, 2))) : strtoupper($code);

        $colors = [
            // Uzbekistan - ko'k, yashil, oq, oltin
            'UZ' => ['primary' => '#1eb53a', 'secondary' => '#0099b5', 'accent' => '#c9a227'],

            // United Kingdom - qizil, ko'k, oq
            'GB' => ['primary' => '#c8102e', 'secondary' => '#012169', 'accent' => '#c9a227'],

            // United States - qizil, ko'k, oq
            'US' => ['primary' => '#b22234', 'secondary' => '#3c3b6e', 'accent' => '#c9a227'],

            // Germany - qora, qizil, oltin
            'DE' => ['primary' => '#000000', 'secondary' => '#dd0000', 'accent' => '#ffcc00'],

            // France - ko'k, oq, qizil
            'FR' => ['primary' => '#0055a4', 'secondary' => '#ef4135', 'accent' => '#c9a227'],

            // Italy - yashil, oq, qizil
            'IT' => ['primary' => '#009246', 'secondary' => '#cd212a', 'accent' => '#c9a227'],

            // Spain - qizil, sariq
            'ES' => ['primary' => '#c60b1e', 'secondary' => '#ffc400', 'accent' => '#8b4513'],

            // Russia - oq, ko'k, qizil
            'RU' => ['primary' => '#0039a6', 'secondary' => '#d52b1e', 'accent' => '#c9a227'],

            // Japan - qizil, oq
            'JP' => ['primary' => '#bc002d', 'secondary' => '#1a1a2e', 'accent' => '#c9a227'],

            // China - qizil, sariq
            'CN' => ['primary' => '#de2910', 'secondary' => '#ffde00', 'accent' => '#8b0000'],

            // South Korea - ko'k, qizil, qora
            'KR' => ['primary' => '#0047a0', 'secondary' => '#cd2e3a', 'accent' => '#1a1a1a'],

            // Turkey - qizil, oq
            'TR' => ['primary' => '#e30a17', 'secondary' => '#1a1a2e', 'accent' => '#c9a227'],

            // Poland - oq, qizil
            'PL' => ['primary' => '#dc143c', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Kazakhstan - osmon ko'k, oltin
            'KZ' => ['primary' => '#00afca', 'secondary' => '#ffc61e', 'accent' => '#006994'],

            // India - to'q sariq, yashil, ko'k
            'IN' => ['primary' => '#ff9933', 'secondary' => '#138808', 'accent' => '#000080'],

            // Brazil - yashil, sariq, ko'k
            'BR' => ['primary' => '#009c3b', 'secondary' => '#ffdf00', 'accent' => '#002776'],

            // Canada - qizil, oq
            'CA' => ['primary' => '#ff0000', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Turkmenistan - yashil, qizil, oq
            'TM' => ['primary' => '#00843d', 'secondary' => '#d22630', 'accent' => '#c9a227'],

            // Australia - ko'k, qizil, oq
            'AU' => ['primary' => '#00008b', 'secondary' => '#ff0000', 'accent' => '#c9a227'],

            // Netherlands - qizil, oq, ko'k
            'NL' => ['primary' => '#ae1c28', 'secondary' => '#21468b', 'accent' => '#f47920'],

            // Sweden - ko'k, sariq
            'SE' => ['primary' => '#006aa7', 'secondary' => '#fecc00', 'accent' => '#1a1a2e'],

            // Switzerland - qizil, oq
            'CH' => ['primary' => '#ff0000', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Austria - qizil, oq
            'AT' => ['primary' => '#ed2939', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Belgium - qora, sariq, qizil
            'BE' => ['primary' => '#000000', 'secondary' => '#fdda24', 'accent' => '#ef3340'],

            // Portugal - yashil, qizil
            'PT' => ['primary' => '#006600', 'secondary' => '#ff0000', 'accent' => '#ffcc00'],

            // Greece - ko'k, oq
            'GR' => ['primary' => '#0d5eaf', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Saudi Arabia - yashil, oq
            'SA' => ['primary' => '#006c35', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // UAE - qizil, yashil, oq, qora
            'AE' => ['primary' => '#00732f', 'secondary' => '#ff0000', 'accent' => '#c9a227'],

            // Malaysia - ko'k, sariq, qizil
            'MY' => ['primary' => '#010066', 'secondary' => '#cc0001', 'accent' => '#ffcc00'],

            // Singapore - qizil, oq
            'SG' => ['primary' => '#ed2939', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Thailand - ko'k, qizil, oq
            'TH' => ['primary' => '#2d2a4a', 'secondary' => '#a51931', 'accent' => '#f4f5f8'],

            // Vietnam - qizil, sariq
            'VN' => ['primary' => '#da251d', 'secondary' => '#ffcd00', 'accent' => '#1a1a2e'],

            // Indonesia - qizil, oq
            'ID' => ['primary' => '#ff0000', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Philippines - ko'k, qizil, sariq
            'PH' => ['primary' => '#0038a8', 'secondary' => '#ce1126', 'accent' => '#fcd116'],

            // Pakistan - yashil, oq
            'PK' => ['primary' => '#01411c', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Bangladesh - yashil, qizil
            'BD' => ['primary' => '#006a4e', 'secondary' => '#f42a41', 'accent' => '#ffffff'],

            // Egypt - qizil, oq, qora
            'EG' => ['primary' => '#ce1126', 'secondary' => '#000000', 'accent' => '#c9a227'],

            // Nigeria - yashil, oq
            'NG' => ['primary' => '#008751', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // South Africa - yashil, oltin, qizil, ko'k
            'ZA' => ['primary' => '#007a4d', 'secondary' => '#ffb612', 'accent' => '#002395'],

            // Mexico - yashil, oq, qizil
            'MX' => ['primary' => '#006341', 'secondary' => '#ce1126', 'accent' => '#c9a227'],

            // Argentina - osmon ko'k, oq
            'AR' => ['primary' => '#74acdf', 'secondary' => '#ffffff', 'accent' => '#f6b40e'],

            // Colombia - sariq, ko'k, qizil
            'CO' => ['primary' => '#fcd116', 'secondary' => '#003893', 'accent' => '#ce1126'],

            // Chile - ko'k, qizil, oq
            'CL' => ['primary' => '#0039a6', 'secondary' => '#d52b1e', 'accent' => '#ffffff'],

            // Peru - qizil, oq
            'PE' => ['primary' => '#d91023', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // New Zealand - ko'k, qizil
            'NZ' => ['primary' => '#00247d', 'secondary' => '#cc142b', 'accent' => '#ffffff'],

            // Ireland - yashil, oq, to'q sariq
            'IE' => ['primary' => '#169b62', 'secondary' => '#ff883e', 'accent' => '#ffffff'],

            // Israel - ko'k, oq
            'IL' => ['primary' => '#0038b8', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Azerbaijan - ko'k, qizil, yashil
            'AZ' => ['primary' => '#0092bc', 'secondary' => '#e4002b', 'accent' => '#00af66'],

            // Tajikistan - qizil, oq, yashil
            'TJ' => ['primary' => '#cc0000', 'secondary' => '#006600', 'accent' => '#f8c300'],

            // Kyrgyzstan - qizil, sariq
            'KG' => ['primary' => '#e8112d', 'secondary' => '#ffef00', 'accent' => '#ffffff'],

            // Afghanistan - qora, qizil, yashil
            'AF' => ['primary' => '#000000', 'secondary' => '#009900', 'accent' => '#d32011'],

            // Iran - yashil, oq, qizil
            'IR' => ['primary' => '#239f40', 'secondary' => '#da0000', 'accent' => '#ffffff'],
        ];

        return $colors[$normalizedCode] ?? ['primary' => '#1a5276', 'secondary' => '#2980b9', 'accent' => '#c9a227'];
    }

    /**
     * HEX rangni RGB ga aylantirish
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Konferensiya uchun barcha maqolalar to'plamini yaratish
     */
    public function generateCollection(Conference $conference): string
    {
        $conference->load([
            'country',
            'articles' => function ($q) {
                $q->where('status', 'published')->orderBy('id');
            }
        ]);

        // 0. Maqolalarning sahifalarini hisoblash va ma'lumotlar bazasini yangilash
        $tempPdf = new Fpdi();
        $startPage = 1;
        $orderCounter = 1;

        foreach ($conference->articles as $article) {
            $articlePdfPath = $article->formatted_pdf_path
                ? Storage::disk('public')->path($article->formatted_pdf_path)
                : Storage::disk('public')->path($article->pdf_path);

            if (file_exists($articlePdfPath)) {
                try {
                    $pageCount = $tempPdf->setSourceFile($articlePdfPath);
                    if ($pageCount > 0) {
                        $endPage = $startPage + $pageCount - 1;
                        
                        // Ma'lumotlarni xotirada va bazada yangilaymiz
                        if ($article->order_number != $orderCounter || $article->page_range !== "$startPage-$endPage") {
                            $article->order_number = $orderCounter;
                            $article->page_range = "$startPage-$endPage";
                            $article->save();
                        }
                        
                        $startPage = $endPage + 1;
                        $orderCounter++;
                    }
                } catch (\Exception $e) {
                    \Log::warning("Sahifa hisoblash xatoligi (Article ID: {$article->id}): " . $e->getMessage());
                }
            }
        }

        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetCreator('ISOC - International Scientific Online Conference');
        $pdf->SetTitle($conference->title . ' - Collection');
        $pdf->SetAutoPageBreak(false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);

        // 1. Muqova sahifasi
        $coverPath = $this->generateCollectionCover($conference);
        if (file_exists($coverPath)) {
            $pdf->setSourceFile($coverPath);
            $tplId = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tplId, 0, 0, 210, 297);
        }

        // 1.5. Info sahifasi (2-sahifa)
        $infoPath = $this->generateCollectionInfoPage($conference, $startPage - 1);
        if (file_exists($infoPath)) {
            $pdf->setSourceFile($infoPath);
            $tplId = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($tplId, 0, 0, 210, 297);

            // Dizayn (overlay) ni qo'llash
            $headerImgPath = $this->getOptimizedImagePath(public_path('images/logo.png'), 150);
            $logoPath = $this->getOptimizedImagePath(public_path('images/logo.png'), 250);
            $this->drawIncopSidebar($pdf, 28, $headerImgPath, $logoPath);
            $this->drawIncopRunningHeader($pdf, null, $conference, 33);
            $this->drawIncopFooter($pdf, null, $conference->country, 2, 0, 33);
        }

        // 2. Mundarija sahifasi oxirida bo'ladi, shuning o'rnini o'zgartiramiz
        // 3. Har bir maqolani qo'shish
        $currentPage = 1;
        foreach ($conference->articles as $article) {
            $articlePdfPath = $article->formatted_pdf_path
                ? Storage::disk('public')->path($article->formatted_pdf_path)
                : Storage::disk('public')->path($article->pdf_path);

            if (file_exists($articlePdfPath)) {
                $pageCount = $pdf->setSourceFile($articlePdfPath);

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplId = $pdf->importPage($pageNo);
                    $pdf->AddPage();
                    $pdf->useTemplate($tplId, 0, 0, 210, 297);
                    $currentPage++;
                }
            }
        }

        // 4. Mundarija sahifasi (Oxirida qo'shiladi)
        $tocPath = $this->generateTableOfContents($conference);
        if (file_exists($tocPath)) {
            $pdf->setSourceFile($tocPath);
            $pageCount = $pdf->setSourceFile($tocPath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tplId, 0, 0, 210, 297);
            }
        }

        // Saqlash
        $filename = 'collection_' . $conference->country->code . '_' . $conference->month_year . '.pdf';
        $path = 'collections/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->Output($fullPath, 'F');

        return $path;
    }

    private function renderSimpleHtmlToPdf(string $html, string $outputPath, string $top = '0', string $bottom = '0', string $left = '0', string $right = '0'): void
    {
        try {
            $scriptPath = base_path('scripts' . DIRECTORY_SEPARATOR . 'html-to-pdf.cjs');
            $nodePath = $this->findNodePath();

            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $command = sprintf(
                '%s %s %s %s %s %s %s',
                escapeshellarg($nodePath),
                escapeshellarg($scriptPath),
                escapeshellarg($outputPath),
                escapeshellarg($top),
                escapeshellarg($bottom),
                escapeshellarg($left),
                escapeshellarg($right)
            );

            $process = proc_open($command, $descriptors, $pipes, base_path());

            if (is_resource($process)) {
                fwrite($pipes[0], $html);
                fclose($pipes[0]);

                $stdout = stream_get_contents($pipes[1]);
                fclose($pipes[1]);

                $stderr = stream_get_contents($pipes[2]);
                fclose($pipes[2]);

                $status = proc_close($process);

                if ($status === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
                    \Log::info("Puppeteer muvaffaqiyatli PDF yaratdi (simple): {$outputPath}");
                    return;
                }
                
                \Log::error("Puppeteer simple failed", ['status' => $status, 'error' => $stderr, 'stdout' => $stdout]);
            }
        } catch (\Exception $e) {
            \Log::error("Puppeteer simple exception: " . $e->getMessage());
        }

        // Fallback to DomPDF
        \Log::info("Fallback to DomPDF for simple html...");
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->save($outputPath);
    }

    /**
     * To'plam muqova sahifasi yaratish
     */
    private function generateCollectionCover(Conference $conference): string
    {
        $country = $conference->country;

        $html = view('pdf.collection-cover', [
            'conference' => $conference,
            'country' => $country,
        ])->render();

        $filename = 'collection_cover_' . time() . '.pdf';
        $path = storage_path('app/temp/' . $filename);

        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->renderSimpleHtmlToPdf($html, $path, '0', '0', '0', '0');

        return $path;
    }

    /**
     * To'plam haqida ma'lumot sahifasi (2-sahifa)
     */
    private function generateCollectionInfoPage(Conference $conference, int $totalPages): string
    {
        $country = $conference->country;

        $html = view('pdf.collection-info', [
            'conference' => $conference,
            'country' => $country,
            'totalPages' => $totalPages,
        ])->render();

        $filename = 'collection_info_' . time() . '.pdf';
        $path = storage_path('app/temp/' . $filename);

        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->renderSimpleHtmlToPdf($html, $path, '0', '0', '0', '0');

        return $path;
    }

    public function convertWordToHtml(string $filePath): string
    {
        // Fayl mavjudligini tekshirish
        if (!file_exists($filePath)) {
            \Log::error('Word file not found: ' . $filePath);
            throw new \Exception('Word fayl topilmadi. Iltimos qaytadan yuklang.');
        }

        // Fayl o'qilishi mumkinligini tekshirish
        if (!is_readable($filePath)) {
            \Log::error('Word file not readable: ' . $filePath);
            throw new \Exception('Word faylni o\'qib bo\'lmadi. Fayl huquqlarini tekshiring.');
        }

        // Fayl hajmini tekshirish (10MB dan oshmasligi kerak)
        $fileSize = filesize($filePath);
        if ($fileSize === false || $fileSize > 10 * 1024 * 1024) {
            \Log::error('Word file too large: ' . $fileSize . ' bytes');
            throw new \Exception('Word fayl hajmi juda katta (10MB dan oshmasligi kerak).');
        }

        // PhpWord sozlamalari
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

        // MathML va boshqa murakkab elementlar uchun xatoliklarni suppress qilish
        libxml_use_internal_errors(true);

        try {
            // Faylni o'qish - matematik elementlar bilan xatolik bo'lishi mumkin
            try {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            } catch (\Exception $mathError) {
                // Agar matematik element xatoligi bo'lsa, faylni tozalash va qayta yuklash
                if (
                    strpos($mathError->getMessage(), 'msSub') !== false ||
                    strpos($mathError->getMessage(), 'OfficeMathML') !== false ||
                    strpos($mathError->getMessage(), 'is not implemented') !== false
                ) {

                    \Log::warning('Word file contains unsupported math elements, attempting to clean: ' . $mathError->getMessage());

                    // Word faylni matematik elementlardan tozalash
                    $cleanedPath = $this->cleanWordFileFromMath($filePath);

                    if ($cleanedPath && file_exists($cleanedPath)) {
                        try {
                            $phpWord = \PhpOffice\PhpWord\IOFactory::load($cleanedPath);
                            \Log::info('Successfully loaded cleaned Word file (math elements replaced with [formula])');
                        } catch (\Exception $retryError) {
                            \Log::error('Failed to load cleaned file: ' . $retryError->getMessage());
                            throw new \Exception('Word faylni qayta ishlashda xatolik yuz berdi. Iltimos, faylni tekshiring.');
                        } finally {
                            // Tozalangan faylni o'chirish
                            @unlink($cleanedPath);
                        }
                    } else {
                        throw new \Exception('Word faylni tozalashda xatolik. Iltimos, matematik formulalarni oddiy matn yoki rasm sifatida saqlang va qayta yuklang.');
                    }
                } else {
                    throw $mathError;
                }
            }

            // LibXML xatoliklarni tozalash
            libxml_clear_errors();

            // HTML yozuvchi (Writer) yaratish
            $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

            // Doimiy papka yaratish (rasmlar PDF generatsiya vaqtida kerak bo'ladi)
            // storage/app/public/temp_word_images ichiga saqlaymiz
            $uniqId = uniqid();
            $storagePath = 'public/temp_word_images/' . $uniqId;
            $fullStoragePath = storage_path('app/' . $storagePath);

            if (!file_exists($fullStoragePath)) {
                if (!mkdir($fullStoragePath, 0755, true) && !is_dir($fullStoragePath)) {
                    throw new \Exception('Vaqtincha papka yaratib bo\'lmadi.');
                }
            }

            // Biz PhpWord ga HTML ni aynan shu papkaga saqlashini aytamiz
            // Shunda u rasmlarni ham shu yerga 'media' papkasiga yozadi
            $tempHtmlFile = $fullStoragePath . '/content.html';
            $xmlWriter->save($tempHtmlFile);

            // ==========================================
            // Image Optimization Step for Word Documents
            // ==========================================
            // Word'dan chiqqan rasmlar odatda juda katta bo'ladi (original).
            // Ularni PDF ga qo'shishdan oldin kichraytiramiz.
            $this->compressImagesInDir($fullStoragePath);

            // HTML ni o'qish
            if (!file_exists($tempHtmlFile)) {
                throw new \Exception('HTML fayl yaratilmadi. Word fayl buzilgan bo\'lishi mumkin.');
            }

            $htmlContent = file_get_contents($tempHtmlFile);
            if ($htmlContent === false) {
                throw new \Exception('HTML faylni o\'qib bo\'lmadi.');
            }

            // Rasmlar yo'lini to'g'irlash
            // PhpWord rasmlarni nisbiy yo'l bilan (masalan: 'media/image1.png') saqlaydi
            // Biz ularni to'liq absolut yo'lga o'zgartirishimiz kerak, shunda DOMPDF ularni topa oladi
            $htmlContent = preg_replace_callback('/(<img[^>]+src=")([^"]+)("[^>]*>)/i', function ($matches) use ($fullStoragePath) {
                $src = $matches[2];

                // Agar src allaqachon to'liq url yoki data-uri bo'lsa, tegmaymiz
                if (filter_var($src, FILTER_VALIDATE_URL) || strpos($src, 'data:') === 0) {
                    return $matches[0];
                }

                // Faylning to'liq yo'li
                // PhpWord odatda urlencode qiladi, shuning uchun urldecode qilamiz
                $imageDecodedPath = urldecode($src);
                $absolutePath = $fullStoragePath . '/' . $imageDecodedPath;

                // Windows da path separatorlarni to'g'irlash
                $absolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absolutePath);

                if (file_exists($absolutePath)) {
                    // DOMPDF local fayl tizimidan o'qishi uchun "file://" protokoli yoki to'g'ridan-to'g'ri path ishlatish mumkin
                    // Windows muhitida ba'zan file:///D:/... ko'rinishida talab qilinadi
                    return $matches[1] . $absolutePath . $matches[3];
                }

                return $matches[0];
            }, $htmlContent);

            // HTML ni tozalash (faqat body qismini olish)
            if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $htmlContent, $matches)) {
                $htmlContent = $matches[1];
            }

            // MathML va XML namespace elementlarni tozalash (DOMPDF ular bilan ishlolmaydi)
            // Math elementlarni oddiy textga aylantirish
            $htmlContent = preg_replace('/<m:oMath[^>]*>.*?<\/m:oMath>/is', '[formula]', $htmlContent);
            $htmlContent = preg_replace('/<math[^>]*>.*?<\/math>/is', '[formula]', $htmlContent);
            $htmlContent = preg_replace('/<mml:math[^>]*>.*?<\/mml:math>/is', '[formula]', $htmlContent);

            // Boshqa XML namespace taglarni olib tashlash
            $htmlContent = preg_replace('/<[a-z]+:[^>]+>.*?<\/[a-z]+:[^>]+>/is', '', $htmlContent);
            $htmlContent = preg_replace('/<\/?[a-z]+:[^>]+>/i', '', $htmlContent);

            // Keraksiz style larni tozalash
            $htmlContent = preg_replace('/font-family:[^;"]+;?/', '', $htmlContent);
            $htmlContent = preg_replace('/font-size:[^;"]+;?/', '', $htmlContent);

            // Eslatma: Temp papkani o'chirmaymiz, chunki rasmlar PDF generatsiya qilishda kerak bo'ladi.
            // Ularni keyinchalik cron job orqali tozalash maqsadga muvofiq.

            return $htmlContent;
        } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            \Log::error('PhpWord exception: ' . $e->getMessage());
            throw new \Exception('Word faylni o\'qishda xatolik: Fayl formati noto\'g\'ri yoki buzilgan bo\'lishi mumkin.');
        } catch (\Exception $e) {
            \Log::error('Word to HTML conversion failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mundarija sahifasi yaratish
     */
    private function generateTableOfContents(Conference $conference): string
    {
        $html = view('pdf.table-of-contents', [
            'conference' => $conference,
            'country' => $conference->country,
            'articles' => $conference->articles,
        ])->render();

        $filename = 'toc_' . time() . '.pdf';
        $path = storage_path('app/temp/' . $filename);

        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->renderSimpleHtmlToPdf($html, $path, '15mm', '15mm', '15mm', '15mm');

        return $path;
    }



    /**
     * Papka ichidagi rasmlarni siqish (recursive)
     */
    private function compressImagesInDir(string $dir): void
    {
        if (!is_dir($dir))
            return;

        // Barcha fayllar va papkalarni olish - subfolders included
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $this->overwriteWithCompressedImage($file->getPathname());
                }
            }
        }
    }

    /**
     * Rasmni siqib ustidan yozish
     */
    private function overwriteWithCompressedImage(string $path): void
    {
        try {
            $info = @getimagesize($path);
            if (!$info)
                return;

            $mime = $info['mime'];
            $width = $info[0];
            $height = $info[1];

            // Target size limits
            $maxWidth = 600; // Enough for document reading

            // Agar rasm kichik bo'lsa tegmash (100KB dan kichik va eni kichik)
            if ($width <= $maxWidth && filesize($path) < 100 * 1024) {
                return;
            }

            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = ($height / $width) * $newWidth;
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            $image = null;
            if ($mime == 'image/jpeg') {
                $image = @imagecreatefromjpeg($path);
            } elseif ($mime == 'image/png') {
                $image = @imagecreatefrompng($path);
            }

            if (!$image)
                return;

            $bg = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($bg, 255, 255, 255);
            imagefilledrectangle($bg, 0, 0, $newWidth, $newHeight, $white);

            // Resize
            imagecopyresampled($bg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            if ($mime == 'image/jpeg') {
                imagejpeg($bg, $path, 50);
            } elseif ($mime == 'image/png') {
                // Compress PNG (0-9)
                imagepng($bg, $path, 9);
            }

            imagedestroy($image);
            imagedestroy($bg);

        } catch (\Exception $e) {
            // Ignore errors
        }
    }
    /**
     * Word fayldan matematik elementlarni olib tashlash
     * Word fayl ZIP arxiv bo'lgani uchun, document.xml ni o'qib, matematik elementlarni tozalaymiz
     */
    private function cleanWordFileFromMath(string $filePath): ?string
    {
        try {
            // Word fayl ZIP arxiv sifatida ishlaydi
            $zip = new \ZipArchive();

            if ($zip->open($filePath) !== true) {
                \Log::error('Failed to open Word file as ZIP: ' . $filePath);
                return null;
            }

            // document.xml ni o'qish (asosiy kontent)
            $documentXml = $zip->getFromName('word/document.xml');

            if ($documentXml === false) {
                \Log::error('Failed to extract document.xml from Word file');
                $zip->close();
                return null;
            }

            // OMML formulalarini matnli ko'rinishga aylantirish
            // m:t taglar ichidagi matnni olish (bu formula matni)
            $cleanedXml = preg_replace_callback(
                '/<m:oMath[^>]*>(.*?)<\/m:oMath>/is',
                function ($matches) {
                    return $this->extractMathText($matches[1]);
                },
                $documentXml
            );

            // m:oMathPara ham
            $cleanedXml = preg_replace_callback(
                '/<m:oMathPara[^>]*>(.*?)<\/m:oMathPara>/is',
                function ($matches) {
                    return '<w:p><w:r><w:t>' . $this->extractMathText($matches[1]) . '</w:t></w:r></w:p>';
                },
                $cleanedXml
            );

            // Qolgan m: namespace elementlarini tozalash
            $cleanedXml = preg_replace('/<\/?m:[^>]+>/i', '', $cleanedXml);

            $zip->close();

            // Yangi tozalangan faylni yaratish
            $cleanedFilePath = storage_path('app/temp/cleaned_' . uniqid() . '.docx');

            // Temp papkani yaratish
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Tozalangan faylni yaratish uchun asl faylni nusxalash
            if (!copy($filePath, $cleanedFilePath)) {
                \Log::error('Failed to copy Word file for cleaning');
                return null;
            }

            // Yangi faylni ochib, document.xml ni yangilash
            $cleanedZip = new \ZipArchive();
            if ($cleanedZip->open($cleanedFilePath) !== true) {
                \Log::error('Failed to open cleaned Word file');
                @unlink($cleanedFilePath);
                return null;
            }

            // Eski document.xml ni o'chirish va yangisini qo'shish
            $cleanedZip->deleteName('word/document.xml');
            $cleanedZip->addFromString('word/document.xml', $cleanedXml);
            $cleanedZip->close();

            \Log::info('Successfully cleaned Word file from math elements');
            return $cleanedFilePath;

        } catch (\Exception $e) {
            \Log::error('Error cleaning Word file from math: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * OMML formula ichidan matnni chiqarib olish
     * Wp = mgh, x^2, H2O kabi formulalarni matn formatiga o'girish
     */
    private function extractMathText(string $ommlContent): string
    {
        $text = '';

        // m:t (math text) taglar ichidagi matnni olish
        preg_match_all('/<m:t[^>]*>([^<]*)<\/m:t>/i', $ommlContent, $matches);
        if (!empty($matches[1])) {
            $text = implode('', $matches[1]);
        }

        // Agar m:t topilmasa, barcha teglarni olib tashlab matnni olish
        if (empty($text)) {
            $text = strip_tags($ommlContent);
        }

        // Bo'sh joylarni normallashtirish
        $text = preg_replace('/\s+/', ' ', trim($text));

        // Agar matn bo'sh bo'lsa, placeholder qo'yish
        if (empty($text)) {
            return '<w:t>[formula]</w:t>';
        }

        return '<w:t>' . htmlspecialchars($text, ENT_XML1, 'UTF-8') . '</w:t>';
    }
}
