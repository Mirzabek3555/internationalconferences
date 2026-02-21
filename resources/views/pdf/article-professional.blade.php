<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $article->title ?? 'Academic Article' }}</title>
    <style>
        /* ========================================
           PROFESSIONAL ACADEMIC ARTICLE TEMPLATE
           Scopus / Google Scholar Style
        ======================================== */
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
            font-size: 10.5pt;
            line-height: 1.55;
            color: #1a1a1a;
            background: #fff;
        }

        .sans {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
        }

        /* ========================================
           COVER PAGE
        ======================================== */
        .cover-page {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: #fff;
            page-break-after: always;
            overflow: hidden;
        }

        /* National Pattern Background */
        .pattern-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50 0L100 50L50 100L0 50z' fill='{{ urlencode($colors['primary'] ?? '%231a5276') }}' fill-opacity='0.5'/%3E%3C/svg%3E");
            background-size: 60px 60px;
        }

        /* Top Header Section */
        .cover-top {
            background: linear-gradient(135deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            padding: 12mm 20mm;
            position: relative;
        }

        .cover-top-content {
            display: table;
            width: 100%;
        }

        .cover-top-left {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
        }

        .cover-top-right {
            display: table-cell;
            vertical-align: middle;
            width: 30%;
            text-align: right;
        }

        .conference-label {
            color: rgba(255, 255, 255, 0.85);
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .conference-title-header {
            color: #fff;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.3;
        }

        .logo-circle {
            width: 18mm;
            height: 18mm;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            display: table;
            float: right;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
        }

        .logo-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .logo-text {
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* Country Image Section */
        .country-image-cover {
            height: 55mm;
            position: relative;
            overflow: hidden;
        }

        .country-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .country-img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg,
                    rgba(0, 0, 0, 0.4) 0%,
                    rgba(0, 0, 0, 0.2) 40%,
                    rgba(0, 0, 0, 0.6) 100%);
        }

        .country-name-overlay {
            position: absolute;
            bottom: 8mm;
            left: 0;
            right: 0;
            text-align: center;
        }

        .country-name-text {
            color: #fff;
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }

        /* Default Country Section (no image) */
        .country-default {
            height: 55mm;
            background: linear-gradient(135deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    22 0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    22 100%);
            display: table;
            width: 100%;
        }

        .country-default-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .country-name-default {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 38px;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            letter-spacing: 5px;
            text-transform: uppercase;
        }

        /* Part Number Section */
        .part-section {
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
                0d;
            padding: 8mm 20mm;
            text-align: center;
            border-top: 1px solid
                {{ $colors['primary'] ?? '#1a5276' }}
                22;
            border-bottom: 1px solid
                {{ $colors['primary'] ?? '#1a5276' }}
                22;
        }

        .part-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .part-number {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 42px;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            line-height: 1.1;
        }

        .part-date {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #666;
            margin-top: 2mm;
        }

        /* Article Info Section */
        .article-section {
            padding: 10mm 20mm;
        }

        .article-type-label {
            display: inline-block;
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            color: #fff;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            font-weight: 600;
            padding: 2mm 4mm;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-radius: 3px;
            margin-bottom: 4mm;
        }

        .article-title-cover {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.4;
            margin-bottom: 5mm;
        }

        .author-info-box {
            background: #f8f9fa;
            border-left: 4px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            padding: 4mm 5mm;
        }

        .author-name-cover {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
        }

        .author-affiliation-cover {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin-top: 1mm;
        }

        /* Cover Footer */
        .cover-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .cover-footer-content {
            background: #fff;
            padding: 5mm 20mm;
            border-top: 1px solid #e0e0e0;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: middle;
        }

        .page-badge {
            display: inline-block;
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            color: #fff;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            font-weight: 700;
            padding: 2mm 5mm;
            border-radius: 3px;
        }

        .website-text {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
        }

        .year-text {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .footer-strip {
            height: 4mm;
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    40%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    40%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    70%,
                    {{ $colors['accent'] ?? '#c9a227' }}
                    70%,
                    {{ $colors['accent'] ?? '#c9a227' }}
                    100%);
        }

        /* ========================================
           CONTENT PAGES
        ======================================== */
        .content-page {
            width: 210mm;
            min-height: 297mm;
            position: relative;
            background: #fff;
            page-break-after: always;
        }

        .content-page:last-child {
            page-break-after: avoid;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            height: 5mm;
        }

        .page-header-text {
            padding: 2mm 18mm;
            border-bottom: 1px solid #e0e0e0;
        }

        .header-text-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-text-table td {
            vertical-align: middle;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .header-left-text {
            font-size: 8px;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-center-text {
            font-size: 9px;
            font-weight: 800;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-align: center;
        }

        .header-right-text {
            font-size: 7px;
            color: #888;
            text-align: right;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 100px;
            font-weight: 800;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            opacity: 0.018;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        /* Left Accent */
        .left-accent {
            position: absolute;
            left: 0;
            top: 30mm;
            width: 2.5mm;
            height: 180mm;
            background: linear-gradient(180deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    50%,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    33 100%);
        }

        /* Main Content */
        .main-content {
            padding: 6mm 20mm 30mm 20mm;
            position: relative;
            z-index: 1;
        }

        /* Article Header */
        .article-header {
            text-align: center;
            margin-bottom: 5mm;
        }

        .article-badge {
            display: inline-block;
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
                15;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7px;
            font-weight: 600;
            padding: 1.5mm 4mm;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 1px solid
                {{ $colors['primary'] ?? '#1a5276' }}
                33;
            border-radius: 2px;
        }

        .article-title {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 15pt;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.35;
            margin: 4mm 0;
        }

        .title-divider {
            width: 25mm;
            height: 1mm;
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            margin: 0 auto 4mm;
            border-radius: 1px;
        }

        /* Author Section */
        .author-section {
            text-align: center;
            padding-bottom: 4mm;
            margin-bottom: 5mm;
            border-bottom: 1px solid #e0e0e0;
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
            color: #666;
            font-style: italic;
            margin-top: 1.5mm;
        }

        .author-email {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin-top: 1mm;
        }

        /* Abstract - davlat bayroq ranglariga asoslangan fon */
        .abstract-box {
            background: {{ $colors['primary'] ?? '#1a5276' }}12;
            border-left: 3px solid
                {{ $colors['secondary'] ?? '#2980b9' }}
            ;
            padding: 4mm 5mm;
            margin-bottom: 5mm;
            border-radius: 0 3px 3px 0;
        }

        .abstract-heading {
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
            color: #333;
            text-align: justify;
            line-height: 1.5;
        }

        /* Keywords - davlat bayroq secondary rangiga asoslangan fon */
        .keywords-box {
            background: {{ $colors['secondary'] ?? '#2980b9' }}12;
            border-left: 3px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin-bottom: 5mm;
            padding: 3mm 5mm;
            border-radius: 0 3px 3px 0;
        }

        .keywords-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .keywords-text {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: #555;
            font-style: italic;
        }

        /* Body Content */
        .body-content {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 10.5pt;
            line-height: 1.65;
            text-align: justify;
            color: #1a1a1a;
        }

        .body-content p {
            margin-bottom: 2.5mm;
            text-indent: 4mm;
        }

        .body-content p:first-child {
            text-indent: 0;
        }

        .section-title {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin: 5mm 0 2.5mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .subsection-title {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            font-weight: 600;
            color: #1a1a1a;
            margin: 3mm 0 2mm;
        }

        /* References */
        .references-box {
            margin-top: 6mm;
            padding-top: 4mm;
            border-top: 2px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
        }

        .references-heading {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin-bottom: 3mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .reference-item {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 8.5pt;
            color: #444;
            margin-bottom: 1.5mm;
            padding-left: 4mm;
            text-indent: -4mm;
            line-height: 1.4;
        }

        /* Page Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer-line {
            margin: 0 18mm;
            border-top: 1px solid #e0e0e0;
        }

        .footer-text {
            padding: 2.5mm 18mm;
        }

        .footer-text-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-text-table td {
            vertical-align: middle;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .footer-left {
            font-size: 7pt;
            color: #888;
        }

        .footer-center {
            text-align: center;
        }

        .page-num {
            display: inline-block;
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            color: #fff;
            font-size: 9pt;
            font-weight: 700;
            padding: 1.5mm 3.5mm;
            border-radius: 2px;
        }

        .footer-right {
            font-size: 7pt;
            color: #888;
            text-align: right;
        }

        .footer-bar {
            height: 3mm;
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
        }
    </style>
</head>

<body>
    {{-- ========================
    COVER PAGE
    ======================== --}}
    <div class="cover-page">
        <div class="pattern-bg"></div>

        {{-- Top Header --}}
        <div class="cover-top">
            <div class="cover-top-content">
                <div class="cover-top-left">
                    <div class="conference-label">International Scientific Online Conference</div>
                    <div class="conference-title-header">
                        {{ $country->conference_name ?? $conference->title ?? 'Innovative Developments and Research' }}
                    </div>
                </div>
                <div class="cover-top-right">
                    <div class="logo-circle">
                        <div class="logo-inner">
                            <div class="logo-text">ISOC</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Country Image or Default --}}
        @if(isset($country->cover_image) && $country->cover_image && file_exists(public_path($country->cover_image)))
            <div class="country-image-cover">
                <img src="{{ public_path($country->cover_image) }}" class="country-img" alt="{{ $country->name }}">
                <div class="country-img-overlay"></div>
                <div class="country-name-overlay">
                    <div class="country-name-text">{{ strtoupper($country->name_en ?? $country->name) }}</div>
                </div>
            </div>
        @else
            <div class="country-default">
                <div class="country-default-inner">
                    <div class="country-name-default">{{ strtoupper($country->name_en ?? $country->name ?? 'COUNTRY') }}
                    </div>
                </div>
            </div>
        @endif

        {{-- Part Section --}}
        <div class="part-section">
            <div class="part-label">Part</div>
            <div class="part-number">{{ $article->order_number ?? 1 }}</div>
            <div class="part-date">
                {{ $conference->conference_date ? $conference->conference_date->format('F d, Y') : date('F d, Y') }}
            </div>
        </div>

        {{-- Article Info --}}
        <div class="article-section">
            <div class="article-type-label">Research Article</div>
            <div class="article-title-cover">{{ $article->title }}</div>
            <div class="author-info-box">
                <div class="author-name-cover">{{ $article->author_name ?? $article->author_display_name }}</div>
                @if($article->author_affiliation)
                    <div class="author-affiliation-cover">{{ $article->author_affiliation }}</div>
                @endif
            </div>
        </div>

        {{-- Cover Footer --}}
        <div class="cover-footer">
            <div class="cover-footer-content">
                <table class="footer-table">
                    <tr>
                        <td style="width: 30%; text-align: left;">
                            <span class="website-text">www.internationalscientificconferences.org</span>
                        </td>
                        <td style="width: 40%; text-align: center;">
                            <span class="page-badge">{{ $article->page_range ?? '1-10' }}</span>
                        </td>
                        <td style="width: 30%; text-align: right;">
                            <span
                                class="year-text">{{ $conference->conference_date ? $conference->conference_date->format('Y') : date('Y') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="footer-strip"></div>
        </div>
    </div>

    {{-- ========================
    CONTENT PAGE
    ======================== --}}
    <div class="content-page">
        {{-- Header --}}
        <div class="page-header"></div>
        <div class="page-header-text">
            <table class="header-text-table">
                <tr>
                    <td style="width: 35%;">
                        <span class="header-left-text">ARTIQLE |
                            {{ strtoupper($country->name_en ?? $country->name ?? '') }}</span>
                    </td>
                    <td style="width: 30%;">
                        <span class="header-center-text">ISOC</span>
                    </td>
                    <td style="width: 35%;">
                        <span class="header-right-text">{{ mb_substr($conference->title ?? '', 0, 35) }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Watermark --}}
        <div class="watermark">{{ strtoupper($country->code ?? 'ISOC') }}</div>

        {{-- Left Accent --}}
        <div class="left-accent"></div>

        {{-- Main Content --}}
        <div class="main-content">
            {{-- Article Header --}}
            <div class="article-header">
                <div class="article-badge">Research Article</div>
                <h1 class="article-title">{{ $article->title }}</h1>
                <div class="title-divider"></div>
            </div>

            {{-- Author --}}
            <div class="author-section">
                <div class="author-name">{{ $article->author_name ?? $article->author_display_name }}</div>
                @if($article->author_affiliation)
                    <div class="author-affiliation">{{ $article->author_affiliation }}</div>
                @endif
                @if(isset($article->author_email) && $article->author_email)
                    <div class="author-email">{{ $article->author_email }}</div>
                @endif
            </div>

            {{-- Abstract --}}
            @if($article->abstract)
                <div class="abstract-box">
                    <div class="abstract-heading">Abstract</div>
                    <div class="abstract-text">{{ $article->abstract }}</div>
                </div>
            @endif

            {{-- Keywords --}}
            @if(isset($article->keywords) && $article->keywords)
                <div class="keywords-box">
                    <span class="keywords-label">Keywords: </span>
                    <span class="keywords-text">{{ $article->keywords }}</span>
                </div>
            @endif

            {{-- Body --}}
            <div class="body-content">
                @php
                    $bodyContent = $article->content ?? '';
                    $bodyContent = str_replace("\r\n", "\n", $bodyContent);
                    $bodyContent = str_replace("\r", "\n", $bodyContent);
                    $bodyContent = preg_replace('/\n\s*\n/', '{{PARA_BREAK}}', $bodyContent);
                    $bodyContent = str_replace("\n", ' ', $bodyContent);
                    $bodyContent = preg_replace('/ {2,}/', ' ', $bodyContent);
                    $bodyContent = str_replace('{{PARA_BREAK}}', "\n\n", $bodyContent);
                @endphp
                {!! nl2br(e(trim($bodyContent))) !!}
            </div>

            {{-- References --}}
            @if(isset($article->references) && $article->references)
                <div class="references-box">
                    <div class="references-heading">FOYDALANILGAN ADABIYOTLAR:</div>
                    @foreach(explode("\n", $article->references) as $ref)
                        @if(trim($ref))
                            <div class="reference-item">{{ $ref }}</div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="page-footer">
            <div class="footer-line"></div>
            <div class="footer-text">
                <table class="footer-text-table">
                    <tr>
                        <td style="width: 35%;">
                            <span class="footer-left">{{ $country->name ?? '' }} |
                                {{ $conference->conference_date ? $conference->conference_date->format('Y') : date('Y') }}</span>
                        </td>
                        <td style="width: 30%;">
                            <span class="footer-center"><span
                                    class="page-num">{{ $article->page_range ?? '1' }}</span></span>
                        </td>
                        <td style="width: 35%;">
                            <span class="footer-right">www.internationalscientificconferences.org</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="footer-bar"></div>
        </div>
    </div>
</body>

</html>