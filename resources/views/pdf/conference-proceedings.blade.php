<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $conference->title ?? 'International Scientific Conference' }}</title>
    <style>
        /* ========================================
           GLOBAL STYLES & TYPOGRAPHY
        ======================================== */
        @page {
            margin: 0;
            size: A4 portrait;
        }

        @page :first {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            --secondary-color:
                {{ $colors['secondary'] ?? '#2980b9' }}
            ;
            --accent-color:
                {{ $colors['accent'] ?? '#c9a227' }}
            ;
            --text-dark: #1a1a1a;
            --text-medium: #444444;
            --text-light: #666666;
            --text-muted: #888888;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --border-color: #e0e0e0;
        }

        body {
            font-family: 'DejaVu Serif', 'Times New Roman', Georgia, serif;
            font-size: 11pt;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--bg-white);
        }

        .sans-serif {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
        }

        /* ========================================
           COVER PAGE STYLES
        ======================================== */
        .cover-page {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: var(--bg-white);
            page-break-after: always;
            overflow: hidden;
        }

        /* National Pattern Watermark */
        .national-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.03;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='{{ urlencode($colors['primary'] ?? '%231a5276') }}' fill-opacity='0.8'%3E%3Cpath d='M40 40L20 60V20l20 20zm0-40L20 20V0h40L40 0zm0 80L60 60v20H20l20 0z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Cover Header Strip */
        .cover-header-strip {
            background: linear-gradient(135deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            height: 8mm;
            width: 100%;
        }

        /* Conference Type Badge */
        .conference-badge {
            position: absolute;
            top: 20mm;
            left: 50%;
            transform: translateX(-50%);
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            color: #fff;
            padding: 8px 30px;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        /* Logo Section */
        .cover-logo-section {
            text-align: center;
            padding: 40mm 30mm 15mm;
        }

        .logo-emblem {
            width: 30mm;
            height: 30mm;
            margin: 0 auto 10mm;
            border-radius: 50%;
            background: linear-gradient(135deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            display: table;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .logo-emblem-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .logo-text {
            color: #fff;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .logo-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 6px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Country Name - Large Display */
        .country-display {
            text-align: center;
            margin: 10mm 0;
        }

        .country-name-large {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 48px;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            letter-spacing: 6px;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.08);
        }

        .country-subtitle {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: var(--text-muted);
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 5mm;
        }

        /* Conference Title Section */
        .conference-title-section {
            background: linear-gradient(135deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    11 0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    11 100%);
            margin: 10mm 25mm;
            padding: 20mm 15mm;
            border-left: 4px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            border-right: 4px solid
                {{ $colors['secondary'] ?? '#2980b9' }}
            ;
        }

        .conference-main-title {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            text-align: center;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .conference-subtitle {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 8mm;
            padding-top: 8mm;
            border-top: 1px solid
                {{ $colors['primary'] ?? '#1a5276' }}
                33;
        }

        /* Date & Location */
        .date-location-section {
            text-align: center;
            padding: 8mm 30mm;
        }

        .conference-date {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 3mm;
        }

        .conference-location {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: var(--text-light);
            letter-spacing: 2px;
        }

        /* Decorative Country Image */
        .country-image-section {
            position: absolute;
            bottom: 60mm;
            left: 0;
            right: 0;
            height: 50mm;
            overflow: hidden;
        }

        .country-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.15;
        }

        .country-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(0deg,
                    var(--bg-white) 0%,
                    transparent 30%,
                    transparent 70%,
                    var(--bg-white) 100%);
        }

        /* Cover Footer */
        .cover-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-white);
            border-top: 1px solid var(--border-color);
            padding: 8mm 25mm;
        }

        .cover-footer-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .cover-footer-grid td {
            vertical-align: middle;
        }

        .footer-logo-placeholder {
            width: 25mm;
            height: 12mm;
            border: 1px dashed var(--border-color);
            display: inline-block;
            text-align: center;
            line-height: 12mm;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7px;
            color: var(--text-muted);
        }

        .footer-website {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            font-weight: 600;
        }

        .footer-year {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Cover Bottom Strip */
        .cover-footer-strip {
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    33%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    33%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    66%,
                    {{ $colors['accent'] ?? '#c9a227' }}
                    66%,
                    {{ $colors['accent'] ?? '#c9a227' }}
                    100%);
            height: 5mm;
            width: 100%;
        }

        /* ========================================
           INNER PAGE STYLES
        ======================================== */
        .inner-page {
            width: 210mm;
            min-height: 297mm;
            position: relative;
            background: var(--bg-white);
            page-break-after: always;
        }

        .inner-page:last-child {
            page-break-after: avoid;
        }

        /* Page Header */
        .page-header {
            position: relative;
            padding: 0;
        }

        .header-accent-bar {
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            height: 4mm;
        }

        .header-content {
            padding: 3mm 18mm 3mm;
            border-bottom: 1px solid var(--border-color);
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            text-align: left;
            vertical-align: middle;
            width: 40%;
        }

        .header-left-text {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-center {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            width: 20%;
        }

        .header-logo-mini {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
        }

        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            width: 40%;
        }

        .header-right-text {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            color: var(--text-muted);
        }

        /* Page Watermark */
        .page-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 90px;
            font-weight: 800;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            opacity: 0.02;
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }

        /* Content Area */
        .content-area {
            padding: 8mm 22mm 30mm 22mm;
            position: relative;
            z-index: 1;
        }

        /* Left Accent Bar */
        .left-accent-bar {
            position: absolute;
            left: 0;
            top: 25mm;
            width: 3mm;
            height: 60%;
            background: linear-gradient(180deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    50%,
                    transparent 100%);
        }

        /* Article Header */
        .article-header {
            margin-bottom: 6mm;
            text-align: center;
        }

        .article-type-badge {
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
            padding: 2mm 5mm;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-radius: 3px;
            margin-bottom: 4mm;
        }

        .article-title {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 16pt;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.35;
            margin-bottom: 5mm;
        }

        .article-title-underline {
            width: 30mm;
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
            padding: 4mm 0;
            margin-bottom: 6mm;
            border-bottom: 1px solid var(--border-color);
        }

        .author-name {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            font-weight: 600;
            color: var(--text-dark);
        }

        .author-affiliation {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: var(--text-light);
            font-style: italic;
            margin-top: 2mm;
        }

        .author-email {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin-top: 1mm;
        }

        /* Abstract Section */
        .abstract-container {
            background: var(--bg-light);
            border-left: 3px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            padding: 5mm 6mm;
            margin-bottom: 6mm;
        }

        .abstract-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3mm;
        }

        .abstract-text {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: var(--text-medium);
            text-align: justify;
            line-height: 1.5;
        }

        /* Keywords Section */
        .keywords-container {
            margin-bottom: 6mm;
            padding-bottom: 4mm;
            border-bottom: 1px solid var(--border-color);
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
            display: inline;
        }

        .keywords-text {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: var(--text-light);
            font-style: italic;
            display: inline;
        }

        /* Main Content */
        .main-content {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 11pt;
            line-height: 1.7;
            text-align: justify;
            color: var(--text-dark);
        }

        .main-content p {
            margin-bottom: 3mm;
            text-indent: 5mm;
        }

        .main-content p:first-child {
            text-indent: 0;
        }

        .section-heading {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin: 6mm 0 3mm;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .subsection-heading {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            font-weight: 600;
            color: var(--text-dark);
            margin: 4mm 0 2mm;
        }

        /* References */
        .references-section {
            margin-top: 8mm;
            padding-top: 4mm;
            border-top: 2px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
        }

        .references-title {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin-bottom: 4mm;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .reference-item {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 9pt;
            color: var(--text-medium);
            margin-bottom: 2mm;
            padding-left: 5mm;
            text-indent: -5mm;
        }

        /* Page Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer-line {
            border-top: 1px solid var(--border-color);
            margin: 0 18mm;
        }

        .footer-content {
            padding: 3mm 18mm;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            text-align: left;
            vertical-align: middle;
            width: 35%;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: var(--text-muted);
        }

        .footer-center {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            width: 30%;
        }

        .page-number {
            display: inline-block;
            background:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            color: #fff;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            padding: 2mm 4mm;
            border-radius: 3px;
        }

        .footer-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            width: 35%;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: var(--text-muted);
        }

        .footer-accent-bar {
            background: linear-gradient(90deg,
                    {{ $colors['primary'] ?? '#1a5276' }}
                    0%,
                    {{ $colors['secondary'] ?? '#2980b9' }}
                    100%);
            height: 3mm;
        }

        /* ========================================
           TABLE OF CONTENTS PAGE
        ======================================== */
        .toc-page {
            width: 210mm;
            min-height: 297mm;
            position: relative;
            background: var(--bg-white);
            page-break-after: always;
        }

        .toc-header {
            padding: 20mm 25mm 10mm;
            border-bottom: 2px solid
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            margin: 0 25mm;
        }

        .toc-title {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 24px;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .toc-content {
            padding: 10mm 25mm 30mm;
        }

        .toc-item {
            display: table;
            width: 100%;
            padding: 3mm 0;
            border-bottom: 1px dotted var(--border-color);
        }

        .toc-number {
            display: table-cell;
            width: 10mm;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            vertical-align: top;
            padding-top: 1mm;
        }

        .toc-article-info {
            display: table-cell;
            vertical-align: top;
        }

        .toc-article-title {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 10pt;
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.4;
        }

        .toc-article-author {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: var(--text-light);
            margin-top: 1mm;
        }

        .toc-page-number {
            display: table-cell;
            width: 15mm;
            text-align: right;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color:
                {{ $colors['primary'] ?? '#1a5276' }}
            ;
            vertical-align: top;
            padding-top: 1mm;
        }

        /* ========================================
           UTILITY CLASSES
        ======================================== */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .mt-1 {
            margin-top: 2mm;
        }

        .mt-2 {
            margin-top: 4mm;
        }

        .mt-3 {
            margin-top: 6mm;
        }

        .mb-1 {
            margin-bottom: 2mm;
        }

        .mb-2 {
            margin-bottom: 4mm;
        }

        .mb-3 {
            margin-bottom: 6mm;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    {{-- ============================================
    COVER PAGE
    ============================================ --}}
    <div class="cover-page">
        <div class="national-pattern"></div>

        {{-- Top Accent Strip --}}
        <div class="cover-header-strip"></div>

        {{-- Conference Type Badge --}}
        <div class="conference-badge">
            International Scientific Online Conference
        </div>

        {{-- Logo Section --}}
        <div class="cover-logo-section">
            <div class="logo-emblem">
                <div class="logo-emblem-inner">
                    <div class="logo-text">ISOC</div>
                    <div class="logo-subtitle">Conference</div>
                </div>
            </div>
        </div>

        {{-- Country Name Display --}}
        <div class="country-display">
            <div class="country-name-large">{{ strtoupper($country->name_en ?? $country->name ?? 'COUNTRY NAME') }}
            </div>
            <div class="country-subtitle">
                {{ $conference->conference_date->format('Y') ?? date('Y') }}
            </div>
        </div>

        {{-- Conference Title Section --}}
        <div class="conference-title-section">
            <div class="conference-main-title">
                {{ $conference->title ?? 'Innovative Developments and Research' }}
            </div>
            <div class="conference-subtitle">
                {{ $country->conference_name ?? 'International Scientific Online Conference' }}
            </div>
        </div>

        {{-- Date & Location --}}
        <div class="date-location-section">
            <div class="conference-date">
                {{ $conference->conference_date ? $conference->conference_date->format('F d, Y') : date('F d, Y') }}
            </div>
            <div class="conference-location">
                {{ strtoupper($country->name_en ?? $country->name ?? '') }}
            </div>
        </div>

        {{-- Country Image (decorative) --}}
        @if(isset($country->cover_image) && $country->cover_image && file_exists(public_path($country->cover_image)))
            <div class="country-image-section">
                <img src="{{ public_path($country->cover_image) }}" class="country-image" alt="{{ $country->name }}">
                <div class="country-image-overlay"></div>
            </div>
        @endif

        {{-- Cover Footer --}}
        <div class="cover-footer">
            <table class="cover-footer-grid">
                <tr>
                    <td style="width: 30%; text-align: left;">
                        <div class="footer-logo-placeholder">Conference Logo</div>
                    </td>
                    <td style="width: 40%; text-align: center;">
                        <div class="footer-website">www.internationalscientificconferences.org</div>
                    </td>
                    <td style="width: 30%; text-align: right;">
                        <div class="footer-year">
                            {{ $conference->conference_date ? $conference->conference_date->format('Y') : date('Y') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Bottom National Colors Strip --}}
        <div class="cover-footer-strip"></div>
    </div>

    {{-- ============================================
    TABLE OF CONTENTS PAGE (if articles exist)
    ============================================ --}}
    @if(isset($articles) && count($articles) > 0)
        <div class="toc-page">
            <div class="page-header">
                <div class="header-accent-bar"></div>
                <div class="header-content">
                    <div class="header-left">
                        <span class="header-left-text">{{ $country->name_en ?? $country->name ?? '' }}
                            {{ $conference->conference_date ? $conference->conference_date->format('Y') : date('Y') }}</span>
                    </div>
                    <div class="header-center">
                        <span class="header-logo-mini">ISOC</span>
                    </div>
                    <div class="header-right">
                        <span class="header-right-text">Table of Contents</span>
                    </div>
                </div>
            </div>

            <div class="toc-header">
                <div class="toc-title">Contents</div>
            </div>

            <div class="toc-content">
                @foreach($articles as $index => $tocArticle)
                    <div class="toc-item">
                        <div class="toc-number">{{ $index + 1 }}.</div>
                        <div class="toc-article-info">
                            <div class="toc-article-title">{{ $tocArticle->title }}</div>
                            <div class="toc-article-author">{{ $tocArticle->author_name ?? $tocArticle->author_display_name }}
                            </div>
                        </div>
                        <div class="toc-page-number">{{ $tocArticle->page_range ?? ($index + 3) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="page-footer">
                <div class="footer-line"></div>
                <div class="footer-content">
                    <div class="footer-left">{{ $country->name ?? '' }} |
                        {{ $conference->conference_date ? $conference->conference_date->format('Y') : date('Y') }}
                    </div>
                    <div class="footer-center"><span class="page-number">ii</span></div>
                    <div class="footer-right">www.internationalscientificconferences.org</div>
                </div>
                <div class="footer-accent-bar"></div>
            </div>
        </div>
    @endif

    {{-- ============================================
    ARTICLE CONTENT PAGES
    ============================================ --}}
    @if(isset($article))
        <div class="inner-page">
            {{-- Page Header --}}
            <div class="page-header">
                <div class="header-accent-bar"></div>
                <div class="header-content">
                    <div class="header-left">
                        <span class="header-left-text">ARTIQLE |
                            {{ strtoupper($country->name_en ?? $country->name ?? '') }}</span>
                    </div>
                    <div class="header-center">
                        <span class="header-logo-mini">ISOC</span>
                    </div>
                    <div class="header-right">
                        <span class="header-right-text">{{ mb_substr($conference->title ?? '', 0, 40) }}</span>
                    </div>
                </div>
            </div>

            {{-- Left Accent Bar --}}
            <div class="left-accent-bar"></div>

            {{-- Page Watermark --}}
            <div class="page-watermark">{{ strtoupper($country->code ?? 'ISOC') }}</div>

            {{-- Content Area --}}
            <div class="content-area">
                {{-- Article Header --}}
                <div class="article-header">
                    <div class="article-type-badge">Research Article</div>
                    <h1 class="article-title">{{ $article->title }}</h1>
                    <div class="article-title-underline"></div>
                </div>

                {{-- Author Block --}}
                <div class="author-block">
                    <div class="author-name">{{ $article->author_name ?? $article->author_display_name }}</div>
                    @if($article->author_affiliation)
                        <div class="author-affiliation">{{ $article->author_affiliation }}</div>
                    @endif
                    @if(isset($article->author_email))
                        <div class="author-email">{{ $article->author_email }}</div>
                    @endif
                </div>

                {{-- Abstract --}}
                @if($article->abstract)
                    <div class="abstract-container">
                        <div class="abstract-label">Abstract</div>
                        <div class="abstract-text">{{ $article->abstract }}</div>
                    </div>
                @endif

                {{-- Keywords --}}
                @if(isset($article->keywords) && $article->keywords)
                    <div class="keywords-container">
                        <span class="keywords-label">Keywords:</span>
                        <span class="keywords-text">{{ $article->keywords }}</span>
                    </div>
                @endif

                {{-- Main Content --}}
                <div class="main-content">
                    @php
                        $mainContent = $article->content ?? '';
                        $mainContent = str_replace("\r\n", "\n", $mainContent);
                        $mainContent = str_replace("\r", "\n", $mainContent);
                        $mainContent = preg_replace('/\n\s*\n/', '{{PARA_BREAK}}', $mainContent);
                        $mainContent = str_replace("\n", ' ', $mainContent);
                        $mainContent = preg_replace('/ {2,}/', ' ', $mainContent);
                        $mainContent = str_replace('{{PARA_BREAK}}', "\n\n", $mainContent);
                    @endphp
                    {!! nl2br(e(trim($mainContent))) !!}
                </div>

                {{-- References (if available) --}}
                @if(isset($article->references) && $article->references)
                    <div class="references-section">
                        <div class="references-title">FOYDALANILGAN ADABIYOTLAR:</div>
                        @foreach(explode("\n", $article->references) as $reference)
                            @if(trim($reference))
                                <div class="reference-item">{{ $reference }}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Page Footer --}}
            <div class="page-footer">
                <div class="footer-line"></div>
                <div class="footer-content">
                    <div class="footer-left">{{ $country->name ?? '' }} |
                        {{ $conference->conference_date ? $conference->conference_date->format('Y') : date('Y') }}
                    </div>
                    <div class="footer-center"><span class="page-number">{{ $article->page_range ?? '1' }}</span></div>
                    <div class="footer-right">www.internationalscientificconferences.org</div>
                </div>
                <div class="footer-accent-bar"></div>
            </div>
        </div>
    @endif
</body>

</html>