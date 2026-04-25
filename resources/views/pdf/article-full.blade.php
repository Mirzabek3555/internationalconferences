<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $article->title }}</title>
    <style>
        @page {
            /* Margins: Top, Right, Bottom, Left */
            margin: 15mm 15mm 10mm 30mm;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #2c3e50;
        }

        /* ============= WATERMARK ============= */
        .watermark {
            position: fixed;
            top: 50%;
            left: 55%;
            /* Offset for sidebar */
            transform: translate(-50%, -50%);
            width: 120mm;
            opacity: 0.03;
            /* Very faint */
            z-index: -10;
        }

        /* ============= LEFT SIDEBAR CONTAINER ============= */
        .sidebar-container {
            position: fixed;
            left: -28mm;
            top: 0;
            bottom: 0;
            width: 28mm;
            height: 100%;
            z-index: -1;
            border-right: 1pt solid #eee;
            background-color: #fff;
        }

        /* Logo placed vertically at the bottom of the sidebar */
        .sidebar-logo {
            position: absolute;
            bottom: 10mm;
            /* Moved up to clear bottom margin nicely */
            left: 4mm;
            /* Centered in the 28mm sidebar */
            width: 18mm;
            text-align: center;
        }

        .sidebar-logo img {
            width: 100%;
            height: auto;
            transform: rotate(-90deg);
            transform-origin: center center;
        }

        /* Vertical brands text at the top */
        .sidebar-text {
            position: absolute;
            bottom: 60mm;
            /* Starts above the logo with some gap */
            left: 12mm;
            /* Adjusted to align visually with the logo's center line */
            width: 200mm;
            white-space: nowrap;
            transform: rotate(-90deg);
            transform-origin: left bottom;
            text-align: left;
        }

        .brand-text {
            font-size: 30pt;
            font-weight: bold;
            color:
                {{ $colors['primary'] }}
            ;
            text-transform: uppercase;
            letter-spacing: 6px;
        }

        .sub-brand-text {
            font-size: 8pt;
            color: #95a5a6;
            text-transform: uppercase;
            margin-left: 5mm;
            letter-spacing: 2px;
            font-weight: normal;
        }

        /* Decoration at the top/bottom of sidebar */
        .sidebar-top-decor {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4mm;
            background-color:
                {{ $colors['primary'] }}
            ;
        }

        .sidebar-bottom-decor {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4mm;
            background-color:
                {{ $colors['secondary'] }}
            ;
        }

        /* ============= HEADER SECTION ============= */
        .header {
            text-align: center;
            width: 100%;
            margin-bottom: 8mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid
                {{ $colors['primary'] }}
            ;
        }

        .header-conf-name {
            font-size: 13pt;
            font-weight: bold;
            color:
                {{ $colors['primary'] }}
            ;
            text-transform: uppercase;
            margin-bottom: 1mm;
            letter-spacing: 0.5px;
        }

        .header-date {
            font-size: 10pt;
            color: #e74c3c;
            /* Red accent */
            font-weight: bold;
        }

        /* ============= ARTICLE INFO ============= */
        .article-title {
            text-align: center;
            font-size: 15pt;
            font-weight: bold;
            margin-bottom: 3mm;
            line-height: 1.3;
            color: #000;
        }

        .article-author {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            color:
                {{ $colors['secondary'] }}
            ;
            margin-bottom: 1mm;
        }

        .article-affiliation {
            text-align: center;
            font-size: 10pt;
            font-style: italic;
            color: #7f8c8d;
            margin-bottom: 8mm;
        }

        /* ============= CONTENT ============= */
        .abstract-container {
            font-size: 10pt;
            background-color: #f8f9fa;
            /* Light gray background */
            border-left: 4px solid
                {{ $colors['accent'] }}
            ;
            padding: 5mm;
            margin-bottom: 8mm;
            text-align: justify;
            border-radius: 0 4px 4px 0;
        }

        .abstract-title {
            font-weight: bold;
            color:
                {{ $colors['primary'] }}
            ;
            text-transform: uppercase;
            font-size: 9pt;
            margin-bottom: 1mm;
            display: block;
        }

        .content {
            text-align: justify;
            font-size: 11pt;
            line-height: 1.6;
        }

        .content p {
            margin-bottom: 4mm;
            text-indent: 10mm;
            page-break-inside: avoid;
        }

        .content p:first-child {
            text-indent: 0;
        }

        .content h1,
        .content h2,
        .content h3 {
            color:
                {{ $colors['primary'] }}
            ;
            margin-top: 5mm;
            margin-bottom: 3mm;
        }

        img {
            max-width: 100%;
            height: auto;
            margin: 5mm auto;
            display: block;
            border: 1px solid #eee;
            padding: 1mm;
            background: #fff;
        }

        /* Modern Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin: 6mm 0;
            page-break-inside: auto;
        }

        th {
            background-color:
                {{ $colors['primary'] }}
            ;
            color: #fff;
            padding: 3mm;
            font-weight: bold;
            border: 1px solid
                {{ $colors['primary'] }}
            ;
        }

        td {
            border: 1px solid #ddd;
            padding: 3mm;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: -18mm;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #95a5a6;
            border-top: 1px solid #eee;
            padding-top: 2mm;
        }
    </style>
</head>

<body>

    <!-- Watermark (Optional) -->
    @if(file_exists(public_path('images/logos/isc-logo.png')))
        <img src="{{ public_path('images/logos/isc-logo.png') }}" class="watermark">
    @endif

    <!-- Left Sidebar with Logo and Text -->
    <div class="sidebar-container">
        <div class="sidebar-top-decor"></div>

        <div class="sidebar-text">
            <span class="brand-text">International Scientific Conferences</span>
            <span class="sub-brand-text">Open Access Proceedings</span>
        </div>

        <!-- Rotated Logo at the bottom area of sidebar -->
        <div class="sidebar-logo">
            @php
                $logoPath = public_path('images/logos/isc-logo.png');
                $hasLogo = file_exists($logoPath);
            @endphp
            @if($hasLogo)
                <img src="{{ $logoPath }}" alt="ISC">
            @else
                <div style="font-weight:bold; font-size:18pt; color: {{ $colors['primary'] }}; transform: rotate(-90deg);">
                    ISC</div>
            @endif
        </div>

        <div class="sidebar-bottom-decor"></div>
    </div>

    <!-- Page Numbering -->
    <div class="footer">
        <script type="text/php">
            if (isset($pdf)) {
                $font = $fontMetrics->get_font("DejaVu Sans, Arial, sans-serif", "normal");
                $pdf->page_text(297, 820, "{PAGE_NUM}", $font, 9, array(0.5, 0.5, 0.5));
            }
        </script>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="header-conf-name">{{ $country->conference_name ?? 'INTERNATIONAL SCIENTIFIC CONFERENCES' }}</div>
        <div class="header-date">Date: {{ $conference->conference_date->format('jS F Y') }}</div>
    </div>

    <!-- Article Body -->
    <div class="article-title">{{ $article->title }}</div>

    <div class="article-author">{{ $article->author_name ?? $article->author_display_name }}</div>
    <div class="article-affiliation">{{ $article->author_affiliation }}</div>

    @if($article->abstract)
        <div class="abstract-container">
            <span class="abstract-title">Abstract</span>
            {{ $article->abstract }}
            @if($article->keywords)
                <br><br>
                <strong>Keywords:</strong> {{ $article->keywords }}
            @endif
        </div>
    @endif

    <div class="content">
        @php
            $fullContent = $article->content ?? '';
            $fullContent = str_replace("\r\n", "\n", $fullContent);
            $fullContent = str_replace("\r", "\n", $fullContent);
            $fullContent = preg_replace('/\n\s*\n/', '{{PARA_BREAK}}', $fullContent);
            $fullContent = str_replace("\n", ' ', $fullContent);
            $fullContent = preg_replace('/ {2,}/', ' ', $fullContent);
            $fullParagraphs = explode('{{PARA_BREAK}}', $fullContent);
            $fullContent = '';
            foreach ($fullParagraphs as $fp) {
                $fp = trim($fp);
                if (!empty($fp)) {
                    $fullContent .= '<p>' . e($fp) . '</p>';
                }
            }
        @endphp
        {!! $fullContent !!}
    </div>

    @if(isset($article->references) && $article->references)
        <div style="margin-top: 10mm; padding-top: 5mm; border-top: 1px dashed #ccc;">
            <strong>FOYDALANILGAN ADABIYOTLAR:</strong><br>
            <div style="font-size: 10pt; margin-top: 2mm; color: #555;">
                {!! nl2br(e($article->references)) !!}
            </div>
        </div>
    @endif

</body>

</html>