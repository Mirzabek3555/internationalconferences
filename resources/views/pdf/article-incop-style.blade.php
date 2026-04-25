<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $article->title }}</title>
    <style>
        @page {
            margin: 12mm 12mm 26mm 12mm; /* Pastki margin 18mm dan 26mm ga oshirildi - joy yetarliligi kafolatlanadi */
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Serif', 'Times New Roman', Georgia, serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #000000;
            background: #ffffff;
        }

        /* ===== LEFT SIDEBAR DESIGN ===== */
        .left-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 22mm;
            height: 100%;
            background: #ffffff;
            z-index: 100;
            border-right: 0.3pt solid #e0e0e0;
        }

        /* Globe and book image at top */
        .sidebar-header-image {
            position: absolute;
            top: 8mm;
            left: 2mm;
            width: 18mm;
        }

        .sidebar-header-image img {
            width: 100%;
            height: auto;
        }

        /* Vertical text - International Scientific Conferences */
        .sidebar-main-text {
            position: absolute;
            left: 8mm;
            top: 50mm;
            transform: rotate(-90deg);
            transform-origin: left top;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            font-weight: 700;
            color: #d35400;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        /* Vertical subtext */
        .sidebar-sub-text {
            position: absolute;
            left: 14mm;
            top: 50mm;
            transform: rotate(-90deg);
            transform-origin: left top;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6pt;
            color: #666666;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        /* ISC Logo at bottom */
        .sidebar-logo {
            position: absolute;
            left: 1mm;
            bottom: 25mm;
            width: 20mm;
        }

        .sidebar-logo img {
            width: 100%;
            height: auto;
        }

        /* ===== MAIN CONTENT AREA ===== */
        .main-content {
            margin-left: 24mm;
            padding-right: 2mm;
            padding-bottom: 5mm; /* Sahifaning eng oxirigacha bormaslik uchun qo'shimcha ichki padding */
        }

        /* ===== HEADER SECTION ===== */
        .header-section {
            text-align: center;
            margin-bottom: 5mm;
            padding-bottom: 3mm;
            border-bottom: 0.5pt solid #1a5276;
        }

        .conference-name {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            font-weight: 600;
            color: #1a5276;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1mm;
        }

        .conference-details {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6.5pt;
            color: #666666;
            margin-bottom: 4mm;
        }

        .article-title {
            font-family: 'DejaVu Serif', 'Times New Roman', serif;
            font-size: 12pt;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
            line-height: 1.25;
            margin-bottom: 3mm;
            text-align: center;
        }

        .author-info {
            text-align: center;
            margin-bottom: 2mm;
        }

        .author-name {
            font-family: 'DejaVu Serif', serif;
            font-size: 10pt;
            font-weight: 600;
            color: #000000;
        }

        .author-affiliation {
            font-family: 'DejaVu Serif', serif;
            font-size: 8pt;
            font-style: italic;
            color: #444444;
            margin-top: 1mm;
        }

        .co-authors {
            font-size: 8pt;
            color: #333333;
            margin-top: 1mm;
        }

        /* ===== ARTICLE META INFO ===== */
        .article-meta {
            background: #f5f7fa;
            border: 0.3pt solid #d0d0d0;
            border-radius: 2pt;
            padding: 2mm 3mm;
            margin-bottom: 4mm;
            font-size: 7pt;
        }

        .meta-row {
            display: inline;
            margin-right: 4mm;
        }

        .meta-label {
            font-weight: 600;
            color: #555555;
        }

        .meta-value {
            color: #000000;
        }

        /* ===== ABSTRACT & KEYWORDS ===== */
        .abstract-section {
            margin-bottom: 3mm;
        }

        .section-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            font-weight: 700;
            color: #1a5276;
            margin-bottom: 1mm;
        }

        .abstract-text {
            font-size: 8.5pt;
            line-height: 1.3;
            text-align: justify;
            color: #000000;
        }

        .keywords-section {
            margin-bottom: 4mm;
            padding-bottom: 3mm;
            border-bottom: 0.3pt solid #cccccc;
        }

        .keywords-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            font-weight: 700;
            color: #1a5276;
        }

        .keywords-text {
            font-size: 8pt;
            font-style: italic;
            color: #333333;
        }

        /* ===== MAIN CONTENT SECTIONS ===== */
        .content-section {
            margin-bottom: 3mm;
        }

        .section-title {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
            margin-bottom: 1.5mm;
        }

        .section-number {
            color: #1a5276;
        }

        .section-text {
            font-size: 9pt;
            line-height: 1.35;
            text-align: justify;
        }

        .section-text p {
            margin-bottom: 2mm;
            text-indent: 4mm;
            page-break-inside: auto;
            /* Yozuvlar bo'linganda o'ta yopishib qolmaslik yoki bitta qator bo'lib oxiriga sig'may qolishni oldini olish (DomPDF rule) */
            orphans: 3;
            widows: 3;
        }

        .section-text p:first-of-type {
            margin-top: 1mm;
        }

        /* ===== REFERENCES ===== */
        .references-section {
            margin-top: 4mm;
            padding-top: 3mm;
            border-top: 0.3pt solid #cccccc;
        }

        .references-title {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .reference-item {
            font-size: 7.5pt;
            line-height: 1.3;
            margin-bottom: 1mm;
            padding-left: 4mm;
            text-indent: -4mm;
            text-align: justify;
        }

        .ref-number {
            font-weight: 600;
            color: #1a5276;
        }

        /* ===== FOOTER ===== */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 24mm;
            right: 0;
            height: 10mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: #1a5276;
            border-top: 0.3pt solid #e0e0e0;
            padding-top: 2mm;
        }

        .footer-url {
            color: #1a5276;
        }

        /* ===== PAGE BREAK ===== */
        .page-break {
            page-break-after: always;
        }

        /* ===== KATEX FORMULA STYLES ===== */
        @if(!empty($katexCss ?? ''))
            {!! $katexCss !!}
        @endif

        /* DOCX content uchun qo'shimcha stillar */
        .docx-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 2mm auto;
        }

        .docx-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 2mm 0;
            font-size: 8pt;
        }

        .docx-content table td,
        .docx-content table th {
            border: 0.3pt solid #999;
            padding: 1mm 2mm;
            text-align: left;
        }

        .docx-content table th {
            background: #f0f0f0;
            font-weight: 700;
        }

        .docx-content h2, .docx-content h3, .docx-content h4 {
            font-family: 'DejaVu Sans', sans-serif;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
            margin-top: 3mm;
            margin-bottom: 1.5mm;
            page-break-after: avoid;
        }

        .docx-content h2 { font-size: 10pt; }
        .docx-content h3 { font-size: 9pt; }
        .docx-content h4 { font-size: 8.5pt; }

        .docx-content p {
            margin-bottom: 2mm;
            text-indent: 4mm;
            text-align: justify;
            page-break-inside: auto;
            orphans: 3;
            widows: 3;
        }

        .docx-content ol, .docx-content ul {
            padding-left: 6mm;
            margin-bottom: 2mm;
        }

        .docx-content li {
            margin-bottom: 1mm;
        }

        .math-formula {
            display: inline-block;
            vertical-align: middle;
        }

        .math-error {
            color: #cc0000;
            font-style: italic;
        }
    </style>
</head>

<body>
    @php
        // Logo paths - SVG files
        $headerImagePath = public_path('images/isc-header-globe.svg');
        $hasHeaderImage = file_exists($headerImagePath);

        $logoPath = public_path('images/isc-logo-full.svg');
        $hasLogo = file_exists($logoPath);

        // Alternative logo paths (PNG fallback)
        if (!$hasLogo) {
            $logoPath = public_path('images/isoc_logo.png');
            $hasLogo = file_exists($logoPath);
        }
        if (!$hasHeaderImage) {
            $headerImagePath = public_path('images/isoc_logo.png');
            $hasHeaderImage = file_exists($headerImagePath);
        }
    @endphp

    <!-- Left Sidebar -->
    <div class="left-sidebar">
        <!-- Header Image (Globe with Books) -->
        @if($hasHeaderImage)
            <div class="sidebar-header-image">
                <img src="{{ $headerImagePath }}" alt="">
            </div>
        @endif

        <!-- Vertical Main Text -->
        <div class="sidebar-main-text">International Scientific Conferences</div>

        <!-- Vertical Sub Text -->
        <div class="sidebar-sub-text">Open Access | Scientific online | Conference Proceedings</div>

        <!-- ISC Logo at Bottom -->
        @if($hasLogo)
            <div class="sidebar-logo">
                <img src="{{ $logoPath }}" alt="ISC">
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Header Section -->
        <div class="header-section">
            <div class="conference-name">{{ $country->conference_name ?? 'International Scientific Online Conference' }}
            </div>
            <div class="conference-details">
                {{ $conference->title ?? 'Scientific Research Publication' }} •
                {{ $conference->conference_date ? $conference->conference_date->format('F d, Y') : now()->format('F d, Y') }}
                •
                {{ $country->name_en ?? $country->name }}
            </div>

            <h1 class="article-title">{{ $article->title }}</h1>

            <div class="author-info">
                <div class="author-name">{{ $article->author_name }}</div>
                @if($article->author_affiliation)
                    <div class="author-affiliation">{{ $article->author_affiliation }}</div>
                @endif

                @if($article->co_authors)
                    <div class="co-authors">
                        {{ str_replace("\n", ", ", trim($article->co_authors)) }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Article Meta Info -->
        <div class="article-meta">
            <span class="meta-row">
                <span class="meta-label">Pages:</span>
                <span class="meta-value">{{ $article->page_range ?? 'N/A' }}</span>
            </span>
            <span class="meta-row">
                <span class="meta-label">Published:</span>
                <span
                    class="meta-value">{{ $article->published_at ? $article->published_at->format('d.m.Y') : now()->format('d.m.Y') }}</span>
            </span>
            @if($article->article_link)
                <span class="meta-row">
                    <span class="meta-label">DOI:</span>
                    <span class="meta-value">{{ $article->article_link }}</span>
                </span>
            @endif
        </div>

        <!-- Abstract -->
        @if($article->abstract)
            <div class="abstract-section">
                <div class="section-label">Abstract</div>
                <div class="abstract-text">{{ $article->abstract }}</div>
            </div>
        @endif

        <!-- Keywords -->
        @if($article->keywords)
            <div class="keywords-section">
                <span class="keywords-label">Keywords: </span>
                <span class="keywords-text">{{ $article->keywords }}</span>
            </div>
        @endif

        <!-- Main Content -->
        @if(!empty($isDocxContent ?? false) && !empty($processedHtml ?? ''))
            {{-- DOCX dan kelgan HTML kontent (formulalar, jadvallar, rasmlar bilan) --}}
            <div class="content-section docx-content">
                {!! $processedHtml !!}
            </div>
        @elseif($article->content)
            @php
                $content = $article->content;
                // Try to split into sections
                $sections = [];
                $parts = preg_split('/(?=\d+\.\s*[A-Z])/u', $content, -1, PREG_SPLIT_NO_EMPTY);

                if (count($parts) > 1) {
                    foreach ($parts as $part) {
                        if (preg_match('/^(\d+)\.\s*([^\n]+)\n?(.*)$/su', trim($part), $matches)) {
                            $sections[] = [
                                'number' => $matches[1],
                                'title' => trim($matches[2]),
                                'content' => trim($matches[3])
                            ];
                        } else {
                            $sections[] = ['number' => '', 'title' => '', 'content' => trim($part)];
                        }
                    }
                } else {
                    $sections[] = ['number' => '', 'title' => '', 'content' => $content];
                }
            @endphp

            @foreach($sections as $section)
                <div class="content-section">
                    @if($section['title'])
                        <div class="section-title">
                            @if($section['number'])
                                <span class="section-number">{{ $section['number'] }}.</span>
                            @endif
                            {{ $section['title'] }}
                        </div>
                    @endif
                    <div class="section-text">
                        @php
                            $sectionContent = $section['content'];
                            $sectionContent = str_replace("\r\n", "\n", $sectionContent);
                            $sectionContent = str_replace("\r", "\n", $sectionContent);
                            
                            // 2 va undan ortiq yangi qatorlarni topib paragraph ajratgichga aylantirish
                            $sectionContent = preg_replace('/\n\s*\n/', '{{PARA_BREAK}}', $sectionContent);
                            
                            // Paragraf ichidagi bitta enter'larni joyiga aylantirish (yaxlit matn bo'lishi uchun)
                            $sectionContent = str_replace("\n", ' ', $sectionContent);
                            $sectionContent = preg_replace('/ {2,}/', ' ', $sectionContent);
                            
                            // Paragraflar arrayiga bo'lish
                            $paragraphs = explode('{{PARA_BREAK}}', $sectionContent);
                        @endphp
                        
                        @foreach($paragraphs as $para)
                            @if(trim($para))
                                <p>{!! e(trim($para)) !!}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

        <!-- References -->
        @if($article->references)
            <div class="references-section">
                <div class="references-title">FOYDALANILGAN ADABIYOTLAR:</div>
                @php
                    $references = array_filter(explode("\n", $article->references));
                    $refNum = 1;
                @endphp
                @foreach($references as $ref)
                    @if(trim($ref))
                        <div class="reference-item">
                            @php
                                $trimRef = trim($ref);
                                if (!preg_match('/^\[?\d+[\.\)\]]/', $trimRef)) {
                                    echo '<span class="ref-number">[' . $refNum . ']</span> ' . $trimRef;
                                } else {
                                    echo $trimRef;
                                }
                                $refNum++;
                            @endphp
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>


</body>

</html>