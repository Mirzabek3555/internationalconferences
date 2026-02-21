<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $article->title }}</title>
    <style>
        @page {
            /* Reduced margins for better fit in overlay */
            margin: 10mm 15mm 10mm 10mm;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Serif', 'Times New Roman', serif;
            font-size: 11pt;
            /* Slightly smaller to fit more text if needed, but 12pt is standard. Let's keep 12pt or check. User didn't ask for font size change. */
            font-size: 12pt;
            line-height: 1.15;
            color: #000;
            text-align: left;
            margin: 0;
            padding: 0;
            word-spacing: -0.5pt;
        }

        /* ============= LEFT SIDEBAR - thin accent line ============= */
        .sidebar-line {
            position: fixed;
            left: -8mm;
            /* Adjusted for new margins */
            top: 0;
            bottom: 0;
            width: 3mm;
            height: 100%;
            background-color:
                {{ $colors['primary'] }}
            ;
            z-index: 1;
        }

        /* ============= HEADER SECTION ============= */
        .header {
            text-align: center;
            width: 100%;
            margin-bottom: 2mm;
            /* Reduced */
            padding-bottom: 2mm;
            /* Reduced */
            border-bottom: 0.5pt solid
                {{ $colors['primary'] }}
            ;
        }

        .header-country {
            font-size: 10pt;
            color:
                {{ $colors['secondary'] }}
            ;
            font-weight: bold;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .header-conf-name {
            font-size: 10pt;
            font-weight: bold;
            color:
                {{ $colors['primary'] }}
            ;
            text-transform: uppercase;
            margin-bottom: 0mm;
            /* Reduced */
            letter-spacing: 0.5px;
        }

        .header-date {
            font-size: 9pt;
            color: #666;
        }

        /* ============= ARTICLE INFO ============= */
        .article-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3mm;
            /* Reduced */
            margin-top: 2mm;
            /* Reduced */
            line-height: 1.2;
            color: #000;
            text-transform: uppercase;
        }

        .article-author {
            text-align: center;
            font-size: 12pt;
            font-style: italic;
            color: #000;
            margin-bottom: 1mm;
        }

        .article-affiliation {
            text-align: center;
            font-size: 10pt;
            font-style: italic;
            color: #555;
            margin-bottom: 3mm;
            /* Reduced */
        }

        /* ============= ABSTRACT ============= */
        .abstract-container {
            font-size: 11pt;
            background-color: #f9f9f9;
            border-left: 2pt solid
                {{ $colors['accent'] }}
            ;
            padding: 2mm 3mm;
            /* Reduced */
            margin-bottom: 3mm;
            /* Reduced */
            text-align: justify;
            line-height: 1.2;
        }

        .abstract-title {
            font-weight: bold;
            color:
                {{ $colors['primary'] }}
            ;
            text-transform: uppercase;
            font-size: 10pt;
            margin-bottom: 0.5mm;
            /* Reduced */
            display: block;
        }

        .abstract-text {
            color: #333;
        }

        /* ============= KEYWORDS ============= */
        .keywords-container {
            margin-bottom: 3mm;
            /* Reduced */
            font-size: 11pt;
        }

        .keywords-label {
            font-weight: bold;
            color:
                {{ $colors['primary'] }}
            ;
            font-size: 10pt;
        }

        .keywords-text {
            font-style: italic;
            color: #333;
        }

        /* ============= CONTENT ============= */
        .content {
            text-align: justify;
            font-size: 12pt;
            line-height: 1.15;
            word-spacing: -0.5pt;
        }

        .content p {
            margin-bottom: 2mm;
            margin-top: 0;
            text-indent: 10mm;
        }

        .content p:first-child {
            text-indent: 0;
        }

        .content h1,
        .content h2,
        .content h3 {
            color: #000;
            margin-top: 4mm;
            margin-bottom: 2mm;
            font-weight: bold;
        }

        .content h1 {
            font-size: 13pt;
        }

        .content h2 {
            font-size: 12pt;
        }

        .content h3 {
            font-size: 11pt;
        }

        /* ============= IMAGES ============= */
        img {
            max-width: 100%;
            height: auto;
            margin: 3mm auto;
            display: block;
        }

        /* ============= TABLES ============= */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin: 4mm 0;
        }

        th {
            background-color:
                {{ $colors['primary'] }}
            ;
            color: #fff;
            padding: 2mm;
            font-weight: bold;
            border: 0.5pt solid #333;
        }

        td {
            border: 0.5pt solid #333;
            padding: 2mm;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        /* ============= FORMULAS ============= */
        .formula-box {
            display: block;
            margin: 4mm 0;
            text-align: center;
            font-family: 'DejaVu Serif', serif;
            font-size: 12pt;
            padding: 2mm;
            background-color: #fafafa;
            border: 0.5pt solid #ddd;
        }

        /* Subscript and superscript */
        sub {
            font-size: 0.7em;
            vertical-align: sub;
        }

        sup {
            font-size: 0.7em;
            vertical-align: super;
        }

        /* ============= REFERENCES ============= */
        .references-container {
            margin-top: 6mm;
            padding-top: 3mm;
            border-top: 0.5pt solid #ccc;
        }

        .references-title {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 2mm;
            text-transform: uppercase;
        }

        .references-list {
            font-size: 10pt;
            line-height: 1.2;
            color: #333;
        }

        .reference-item {
            margin-bottom: 1mm;
            padding-left: 5mm;
            text-indent: -5mm;
        }

        /* ============= FOOTER ============= */
        .footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #888;
        }

        .footer-left {
            float: left;
            color:
                {{ $colors['primary'] }}
            ;
        }

        .footer-center {
            display: inline-block;
        }

        .footer-right {
            float: right;
        }
    </style>
</head>

<body>
    <!-- Left Sidebar - thin accent line -->
    <div class="sidebar-line"></div>

    <!-- Header -->
    <div class="header">
        <div class="header-country">{{ strtoupper($country->name_en ?? $country->name) }}</div>
        <div class="header-conf-name">
            {{ $country->conference_name ?? 'INTERNATIONAL SCIENTIFIC CONFERENCES OF MODERN TECHNOLOGIES' }}
        </div>
        <div class="header-date">{{ $conference->conference_date->format('d.m.Y') }} | {{ $country->name }}</div>
    </div>

    <!-- Article Title -->
    <div class="article-title">{{ $article->title }}</div>

    <!-- Author -->
    <div class="article-author">{{ $article->author_name ?? $article->author_display_name }}</div>

    <!-- Affiliation -->
    @if($article->author_affiliation)
        <div class="article-affiliation">{{ $article->author_affiliation }}</div>
    @endif

    <!-- Abstract -->
    @if($article->abstract)
        <div class="abstract-container">
            <span class="abstract-title">Abstract</span>
            <span class="abstract-text">{{ $article->abstract }}</span>
        </div>
    @endif

    <!-- Keywords -->
    @if($article->keywords)
        <div class="keywords-container">
            <span class="keywords-label">Keywords: </span>
            <span class="keywords-text">{{ $article->keywords }}</span>
        </div>
    @endif

    <!-- Content -->
    <div class="content">
        @php
            $displayContent = $processedContent ?? $article->content ?? '';
            // Satr uzilishlarini normalizatsiya
            $displayContent = str_replace("\r\n", "\n", $displayContent);
            $displayContent = str_replace("\r", "\n", $displayContent);
            // Paragraf uzilishlarini saqlash
            $displayContent = preg_replace('/\n\s*\n/', '{{PARA_BREAK}}', $displayContent);
            // Yakka satr uzilishlarini bo'shliqqa aylantirish
            $displayContent = str_replace("\n", ' ', $displayContent);
            // Ortiqcha bo'shliqlarni tozalash
            $displayContent = preg_replace('/ {2,}/', ' ', $displayContent);
            // Paragraflarni <p> teglarga aylantirish
            $paragraphs = explode('{{PARA_BREAK}}', $displayContent);
            $displayContent = '';
            foreach ($paragraphs as $para) {
                $para = trim($para);
                if (!empty($para)) {
                    $displayContent .= '<p>' . e($para) . '</p>';
                }
            }
        @endphp
        {!! $displayContent !!}
    </div>

    <!-- References -->
    @if(isset($article->references) && $article->references)
        <div class="references-container">
            <div class="references-title">FOYDALANILGAN ADABIYOTLAR:</div>
            <div class="references-list">
                @foreach(explode("\n", $article->references) as $index => $reference)
                    @if(trim($reference))
                        <div class="reference-item">[{{ $index + 1 }}] {{ trim($reference) }}</div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("DejaVu Serif", "normal");
            $pdf->page_text(540, 800, "{PAGE_NUM}", $font, 9, array(0.4, 0.4, 0.4));
        }
    </script>

</body>

</html>