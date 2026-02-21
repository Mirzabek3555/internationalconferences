<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $article->title }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Serif', 'Times New Roman', Georgia, serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1a1a1a;
            background: #ffffff;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            position: relative;
            background: #ffffff;
            padding: 0;
            overflow: hidden;
        }

        /* Header Strip */
        .header-strip {
            background: linear-gradient(135deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            height: 6mm;
            width: 100%;
        }

        /* Left Sidebar with vertical text */
        .left-sidebar {
            position: absolute;
            left: 0;
            top: 6mm;
            width: 8mm;
            height: calc(100% - 10mm);
            background: linear-gradient(180deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    70%,
                    transparent 100%);
        }

        .vertical-text {
            position: absolute;
            left: 1mm;
            top: 50%;
            transform: rotate(-90deg) translateX(-50%);
            transform-origin: left center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6pt;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
            opacity: 0.9;
        }

        /* Brand Section */
        .brand-section {
            text-align: center;
            padding: 6mm 20mm 5mm 25mm;
            border-bottom: 1px solid #e0e0e0;
        }

        .brand-logo-img {
            width: 50mm;
            height: auto;
            margin: 0 auto 3mm;
        }

        .brand-name {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .conference-name {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #666666;
            margin-top: 2mm;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Country Badge */
        .country-badge {
            display: inline-block;
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
                15;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            font-weight: 600;
            padding: 2mm 4mm;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 3px;
            margin-top: 3mm;
        }

        /* Article Info Section */
        .article-section {
            padding: 6mm 20mm 5mm 25mm;
        }

        /* Article Title */
        .article-title {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 16pt;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.35;
            text-align: center;
            margin-bottom: 4mm;
        }

        .title-underline {
            width: 40mm;
            height: 1mm;
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            margin: 0 auto 5mm;
            border-radius: 2px;
        }

        /* Author Block */
        .author-block {
            text-align: center;
            padding: 3mm 8mm;
            margin-bottom: 4mm;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .author-name {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            font-weight: 600;
            color: #1a1a1a;
        }

        .author-affiliation {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: #666666;
            font-style: italic;
            margin-top: 1mm;
        }

        /* Abstract Section */
        .abstract-container {
            background: #f8f9fa;
            border-left: 4px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            padding: 4mm 5mm;
            margin-bottom: 4mm;
        }

        .section-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2mm;
        }

        .abstract-text {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: #444444;
            text-align: justify;
            line-height: 1.5;
        }

        /* Keywords Section */
        .keywords-container {
            margin-bottom: 4mm;
            padding: 3mm 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .keywords-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline;
        }

        .keywords-text {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: #666666;
            font-style: italic;
            display: inline;
        }

        /* Article Metadata Grid */
        .metadata-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4mm;
            font-size: 9pt;
        }

        .metadata-grid td {
            padding: 2mm 3mm;
            vertical-align: top;
            border-bottom: 1px solid #e0e0e0;
        }

        .metadata-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-weight: 600;
            color: #666666;
            width: 40%;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        .metadata-value {
            font-family: 'DejaVu Serif', Georgia, serif;
            color: #1a1a1a;
        }

        /* Footer */
        .page-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer-content {
            padding: 3mm 20mm;
            border-top: 1px solid #e0e0e0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: #888888;
            text-align: center;
        }

        .footer-strip {
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            height: 3mm;
        }

        .page-number {
            display: inline-block;
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            color: #fff;
            font-size: 8pt;
            font-weight: 700;
            padding: 1.5mm 3mm;
            border-radius: 3px;
            margin-bottom: 1mm;
        }

        .website-link {
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Header Strip -->
        <div class="header-strip"></div>

        <!-- Left Sidebar with Vertical Text -->
        <div class="left-sidebar">
            <div class="vertical-text">INTERNATIONALSCIENTIFICCONFERENCES</div>
        </div>

        <!-- Brand Section -->
        <div class="brand-section">
            @php
                $logoPath = public_path('images/logos/isc-logo.png');
                $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
            @endphp
            @if($logoData)
                <img src="data:image/png;base64,{{ $logoData }}" alt="ISC Logo" class="brand-logo-img">
            @else
                <div style="text-align: center; margin-bottom: 3mm;">
                    <span
                        style="font-family: 'DejaVu Sans', sans-serif; font-size: 24px; font-weight: 800; color: {{ $colors['primary'] ?? '#1a5276' }};">ISC</span>
                </div>
            @endif
            <div class="brand-name">International Scientific Conferences</div>
            <div class="conference-name">
                {{ $country->conference_name ?? ($country->name_en ?? $country->name) . ' Scientific Conference' }}
            </div>
            <div class="country-badge">
                {{ $country->name_en ?? $country->name }} |
                {{ $conference->conference_date ? $conference->conference_date->format('F Y') : now()->format('F Y') }}
            </div>
        </div>

        <!-- Article Section -->
        <div class="article-section">
            <!-- Title -->
            <h1 class="article-title">{{ $article->title }}</h1>
            <div class="title-underline"></div>

            <!-- Author -->
            <div class="author-block">
                <div class="author-name">{{ $article->author_name }}</div>
                @if($article->author_affiliation)
                    <div class="author-affiliation">{{ $article->author_affiliation }}</div>
                @endif

                {{-- Qo'shimcha mualliflar --}}
                @if($article->co_authors)
                    <div style="margin-top: 2mm; padding-top: 2mm; border-top: 1px dashed #ccc;">
                        @foreach(explode("\n", $article->co_authors) as $coAuthor)
                            @if(trim($coAuthor))
                                <div style="font-size: 9pt; color: #555;">{{ trim($coAuthor) }}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Abstract -->
            @if($article->abstract)
                <div class="abstract-container">
                    <div class="section-label">Abstract</div>
                    <div class="abstract-text">{{ $article->abstract }}</div>
                </div>
            @endif

            <!-- Keywords -->
            @if($article->keywords)
                <div class="keywords-container">
                    <span class="keywords-label">Keywords: </span>
                    <span class="keywords-text">{{ $article->keywords }}</span>
                </div>
            @endif

            <!-- Metadata Grid -->
            <table class="metadata-grid">
                <tr>
                    <td class="metadata-label">Article Pages</td>
                    <td class="metadata-value">{{ $article->page_range ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="metadata-label">Conference</td>
                    <td class="metadata-value">{{ $conference->title ?? 'International Scientific Conference' }}</td>
                </tr>
                <tr>
                    <td class="metadata-label">Country</td>
                    <td class="metadata-value">{{ $country->name_en ?? $country->name }}</td>
                </tr>
                <tr>
                    <td class="metadata-label">Publication Date</td>
                    <td class="metadata-value">
                        {{ $article->published_at ? $article->published_at->format('d F Y') : now()->format('d F Y') }}
                    </td>
                </tr>
                @if($article->article_link)
                    <tr>
                        <td class="metadata-label">Article Link</td>
                        <td class="metadata-value">{{ $article->article_link }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Footer -->
        <div class="page-footer">
            <div class="footer-content">
                <div class="page-number">1</div>
                <div><span class="website-link">www.internationalscientificconferences.org</span> | International
                    Scientific Conferences</div>
            </div>
            <div class="footer-strip"></div>
        </div>
    </div>
</body>

</html>