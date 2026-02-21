<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $article->title }}</title>
    @php
        // Har bir davlat uchun o'ziga xos rang sxemasi
        $countryColors = [
            'UZ' => ['primary' => '#1eb53a', 'secondary' => '#0099b5', 'accent' => '#ce1126', 'gradient' => 'linear-gradient(135deg, #0099b5 0%, #1eb53a 100%)'],
            'GB' => ['primary' => '#c8102e', 'secondary' => '#012169', 'accent' => '#c8102e', 'gradient' => 'linear-gradient(135deg, #012169 0%, #c8102e 100%)'],
            'US' => ['primary' => '#b22234', 'secondary' => '#3c3b6e', 'accent' => '#b22234', 'gradient' => 'linear-gradient(135deg, #3c3b6e 0%, #b22234 100%)'],
            'DE' => ['primary' => '#dd0000', 'secondary' => '#000000', 'accent' => '#ffcc00', 'gradient' => 'linear-gradient(135deg, #000000 0%, #dd0000 100%)'],
            'FR' => ['primary' => '#ef4135', 'secondary' => '#0055a4', 'accent' => '#ffffff', 'gradient' => 'linear-gradient(135deg, #0055a4 0%, #ef4135 100%)'],
            'IT' => ['primary' => '#cd212a', 'secondary' => '#009246', 'accent' => '#ffffff', 'gradient' => 'linear-gradient(135deg, #009246 0%, #cd212a 100%)'],
            'ES' => ['primary' => '#c60b1e', 'secondary' => '#ffc400', 'accent' => '#c60b1e', 'gradient' => 'linear-gradient(135deg, #c60b1e 0%, #ffc400 100%)'],
            'RU' => ['primary' => '#d52b1e', 'secondary' => '#0039a6', 'accent' => '#ffffff', 'gradient' => 'linear-gradient(135deg, #0039a6 0%, #d52b1e 100%)'],
            'JP' => ['primary' => '#bc002d', 'secondary' => '#ffffff', 'accent' => '#bc002d', 'gradient' => 'linear-gradient(135deg, #bc002d 0%, #2d2d2d 100%)'],
            'CN' => ['primary' => '#de2910', 'secondary' => '#ffde00', 'accent' => '#de2910', 'gradient' => 'linear-gradient(135deg, #de2910 0%, #8b0000 100%)'],
            'KR' => ['primary' => '#cd2e3a', 'secondary' => '#0047a0', 'accent' => '#000000', 'gradient' => 'linear-gradient(135deg, #0047a0 0%, #cd2e3a 100%)'],
            'TR' => ['primary' => '#e30a17', 'secondary' => '#ffffff', 'accent' => '#e30a17', 'gradient' => 'linear-gradient(135deg, #e30a17 0%, #8b0000 100%)'],
            'PL' => ['primary' => '#dc143c', 'secondary' => '#ffffff', 'accent' => '#dc143c', 'gradient' => 'linear-gradient(135deg, #dc143c 0%, #8b0000 100%)'],
            'KZ' => ['primary' => '#00afca', 'secondary' => '#fec50c', 'accent' => '#00afca', 'gradient' => 'linear-gradient(135deg, #00afca 0%, #006994 100%)'],
            'IN' => ['primary' => '#ff9933', 'secondary' => '#138808', 'accent' => '#000080', 'gradient' => 'linear-gradient(135deg, #ff9933 0%, #138808 100%)'],
            'BR' => ['primary' => '#009c3b', 'secondary' => '#ffdf00', 'accent' => '#002776', 'gradient' => 'linear-gradient(135deg, #009c3b 0%, #002776 100%)'],
        ];

        $code = $country->code ?? 'GB';
        $colors = $countryColors[$code] ?? ['primary' => '#1a5276', 'secondary' => '#2980b9', 'accent' => '#c9a227', 'gradient' => 'linear-gradient(135deg, #1a5276 0%, #2980b9 100%)'];
    @endphp
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
            font-family: 'DejaVu Sans', sans-serif;
            width: 210mm;
            height: 297mm;
            position: relative;
            background: #fff;
        }

        .cover {
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* Top Header Bar - Country Colors */
        .top-header {
            background:
                {{ $colors['gradient'] }}
            ;
            padding: 15px 25px;
            display: table;
            width: 100%;
        }

        .top-header-inner {
            display: table-row;
        }

        .top-header-left,
        .top-header-center,
        .top-header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .top-header-left {
            width: 20%;
        }

        .top-header-center {
            text-align: center;
        }

        .top-header-right {
            width: 20%;
            text-align: right;
        }

        .conference-title-header {
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .conference-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 9px;
            margin-top: 3px;
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            padding: 30px 20px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
        }

        .logo-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background:
                {{ $colors['gradient'] }}
            ;
            margin: 0 auto;
            display: table;
        }

        .logo-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .logo-text {
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .logo-subtitle {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 12px;
        }

        /* Conference Title Section */
        .conference-section {
            text-align: center;
            padding: 25px 30px;
            background: #fff;
        }

        .conference-type {
            color:
                {{ $colors['primary'] }}
            ;
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .conference-name {
            color:
                {{ $colors['primary'] }}
            ;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.3;
        }

        /* Part Section */
        .part-section {
            text-align: center;
            padding: 25px;
            background: #f8f9fa;
        }

        .part-label {
            color:
                {{ $colors['primary'] }}
            ;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .part-number {
            color:
                {{ $colors['primary'] }}
            ;
            font-size: 36px;
            font-weight: 800;
            margin: 8px 0;
        }

        /* Country Section with Cover Image */
        .country-section {
            background:
                {{ $colors['gradient'] }}
            ;
            padding: 25px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .country-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
        }

        .country-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .country-content {
            position: relative;
            z-index: 1;
        }

        .country-name {
            color: #fff;
            font-size: 26px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .country-date {
            color: rgba(255, 255, 255, 0.9);
            font-size: 12px;
            margin-top: 8px;
        }

        /* Article Section */
        .article-section {
            padding: 30px;
            background: #fff;
            border-top: 4px solid
                {{ $colors['primary'] }}
            ;
        }

        .article-label {
            color:
                {{ $colors['primary'] }}
            ;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .article-title {
            color: #1a1a1a;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        /* Author Section */
        .author-section {
            background: #f8f9fa;
            border-left: 4px solid
                {{ $colors['primary'] }}
            ;
            padding: 15px 20px;
            margin-top: 15px;
        }

        .author-name {
            color:
                {{ $colors['primary'] }}
            ;
            font-size: 14px;
            font-weight: 700;
        }

        .author-affiliation {
            color: #666;
            font-size: 10px;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background:
                {{ $colors['gradient'] }}
            ;
            padding: 12px 25px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: middle;
            color: #fff;
        }

        .footer-left {
            font-size: 11px;
            font-weight: 600;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
            font-size: 9px;
            opacity: 0.9;
        }

        /* Page Range Badge */
        .page-badge {
            background: #fff;
            color:
                {{ $colors['primary'] }}
            ;
            padding: 6px 18px;
            font-weight: 800;
            font-size: 14px;
            border-radius: 4px;
        }

        /* Flag decoration */
        .flag-decoration {
            position: absolute;
            top: 80px;
            right: 25px;
            width: 50px;
            height: 32px;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .flag-decoration img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Side Decorations */
        .side-decoration-left {
            position: absolute;
            top: 150px;
            left: 0;
            width: 5px;
            height: 200px;
            background:
                {{ $colors['primary'] }}
            ;
        }

        .side-decoration-right {
            position: absolute;
            top: 200px;
            right: 0;
            width: 5px;
            height: 150px;
            background:
                {{ $colors['primary'] }}
            ;
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <div class="cover">
        <!-- Side Decorations -->
        <div class="side-decoration-left"></div>
        <div class="side-decoration-right"></div>

        <!-- Top Header -->
        <div class="top-header">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:100%; text-align:center; vertical-align:middle;">
                        <div class="conference-title-header">
                            {{ $country->conference_name ?? strtoupper($country->name_en) . ' DEVELOPMENTS AND RESEARCH IN EDUCATION' }}
                        </div>
                        <div class="conference-subtitle">International scientific online conference</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Flag Decoration -->
        @if($country->flag_url && file_exists(storage_path('app/public/' . $country->flag_url)))
            <div class="flag-decoration">
                <img src="{{ storage_path('app/public/' . $country->flag_url) }}" alt="Flag">
            </div>
        @endif

        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo-circle">
                <div class="logo-inner">
                    <div class="logo-text">ISOC</div>
                </div>
            </div>
            <div class="logo-subtitle">International Scientific Online Conference</div>
        </div>

        <!-- Conference Section -->
        <div class="conference-section">
            <div class="conference-type">International Scientific Online Conference</div>
            <div class="conference-name">{{ $country->conference_name ?? $conference->title }}</div>
        </div>

        <!-- Part Section -->
        <div class="part-section">
            <div class="part-label">PART</div>
            <div class="part-number">{{ $article->order_number ?? 1 }}</div>
        </div>

        <!-- Country Section with optional cover image -->
        <div class="country-section">
            @if($country->cover_image && file_exists(public_path($country->cover_image)))
                <div class="country-bg">
                    <img src="{{ public_path($country->cover_image) }}" alt="">
                </div>
            @endif
            <div class="country-content">
                <div class="country-name">{{ strtoupper($country->name_en) }}
                    {{ $conference->conference_date->format('Y') }}</div>
                <div class="country-date">{{ $conference->conference_date->format('F d, Y') }}</div>
            </div>
        </div>

        <!-- Article Section -->
        <div class="article-section">
            <div class="article-label">Article</div>
            <div class="article-title">{{ $article->title }}</div>

            <div class="author-section">
                <div class="author-name">{{ $article->author_name ?? $article->author_display_name }}</div>
                @if($article->author_affiliation)
                    <div class="author-affiliation">{{ $article->author_affiliation }}</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left" style="width:30%;">ARTIQLE {{ date('Y') }}</td>
                    <td class="footer-center" style="width:40%;">
                        <span class="page-badge">{{ $article->page_range }}</span>
                    </td>
                    <td class="footer-right" style="width:30%;">www.internationalscientificconferences.org</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
