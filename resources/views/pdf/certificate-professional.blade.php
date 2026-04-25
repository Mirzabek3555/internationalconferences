<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    @php
        // ===== COLOR SYSTEM =====
        $primary = $colors['primary'] ?? '#1a5276';
        $secondary = $colors['secondary'] ?? '#2980b9';
        $accent = $colors['accent'] ?? '#c9a227';

        // Lighter tints for subtle backgrounds
        if (!function_exists('hexToRgb')) {
            function hexToRgb($hex)
            {
                $hex = ltrim($hex, '#');
                return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
            }
        }
        $pRgb = hexToRgb($primary);
        $sRgb = hexToRgb($secondary);

        $primaryLight = "rgba({$pRgb[0]},{$pRgb[1]},{$pRgb[2]},0.06)";
        $primaryMedium = "rgba({$pRgb[0]},{$pRgb[1]},{$pRgb[2]},0.12)";
        $primaryStrong = "rgba({$pRgb[0]},{$pRgb[1]},{$pRgb[2]},0.20)";
        $secondaryLight = "rgba({$sRgb[0]},{$sRgb[1]},{$sRgb[2]},0.08)";

        // Country info
        $countryCode = strtoupper($country->code ?? 'GB');
        $codeMap = ['UZB' => 'UZ', 'TUR' => 'TR', 'KAZ' => 'KZ', 'USA' => 'US', 'GBR' => 'GB', 'DEU' => 'DE', 'FRA' => 'FR', 'ITA' => 'IT', 'RUS' => 'RU', 'JPN' => 'JP', 'CHN' => 'CN', 'KOR' => 'KR', 'POL' => 'PL', 'IND' => 'IN', 'BRA' => 'BR', 'CAN' => 'CA', 'TKM' => 'TM', 'AUS' => 'AU', 'ESP' => 'ES'];
        $cc = strlen($countryCode) === 3 ? ($codeMap[$countryCode] ?? substr($countryCode, 0, 2)) : $countryCode;

        $countryNameForFile = strtolower(str_replace(' ', '_', $country->name_en ?? $country->name ?? 'default'));
        $watermarkSvgPath = public_path("images/watermarks/{$countryNameForFile}.svg");
        $hasWatermarkSvg = file_exists($watermarkSvgPath);

        // ISC Logo
        $iscLogoPath = public_path('images/isc-globe.png');
        $hasIscLogo = file_exists($iscLogoPath);

        // Conference & article data
        $conferenceName = $country->conference_name ?? $conference->title ?? 'International Scientific Conference';
        $countryNameDisplay = strtoupper($country->name_en ?? $country->name ?? 'International');
        $authorName = $article->author_name ?? $article->author_display_name ?? 'Author Name';
        $articleTitle = $article->title ?? 'Article Title';
        $certNumber = $certificate->certificate_number ?? 'CERT-0000-000000';
        $issueDate = $certificate->issue_date ? $certificate->issue_date->format('F d, Y') : now()->format('F d, Y');
        $confDate = $conference->conference_date ? $conference->conference_date->format('F Y') : now()->format('F Y');

        // QR Code generation
        $verifyUrl = url("/certificate/verify/{$certNumber}");
        $qrBase64 = '';
        try {
            $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(150)
                ->margin(0)
                ->color(0, 0, 0)
                ->generate($verifyUrl);
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrImage);
        } catch (\Exception $e) {
            $qrBase64 = '';
        }

        // Watermark SVG inline
        $watermarkSvgContent = '';
        if ($hasWatermarkSvg) {
            $watermarkSvgContent = file_get_contents($watermarkSvgPath);
            // Change opacity to very subtle for certificate background
            $watermarkSvgContent = preg_replace('/opacity="[^"]*"/', 'opacity="0.04"', $watermarkSvgContent, 1);
        }
        $watermarkBase64 = $watermarkSvgContent
            ? 'data:image/svg+xml;base64,' . base64_encode($watermarkSvgContent)
            : '';

        // ISC logo base64
        $iscLogoBase64 = '';
        if ($hasIscLogo) {
            $iscLogoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($iscLogoPath));
        }
    @endphp

    <style>
        /* ===== RESET & PAGE ===== */
        @page {
            margin: 0;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            color: #2c3e50;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ===== OUTER FRAME ===== */
        .cert-frame {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 2.5pt solid
                {{ $accent }}
            ;
        }

        .cert-frame-inner {
            position: absolute;
            top: 3mm;
            left: 3mm;
            right: 3mm;
            bottom: 3mm;
            border: 0.5pt solid
                {{ $primary }}
            ;
        }

        /* ===== TOP DECORATIVE LINE ===== */
        .top-accent-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4mm;
            background: linear-gradient(90deg,
                    {{ $primary }}
                    ,
                    {{ $secondary }}
                    ,
                    {{ $primary }}
                );
        }

        /* ===== BOTTOM DECORATIVE LINE ===== */
        .bottom-accent-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4mm;
            background: linear-gradient(90deg,
                    {{ $secondary }}
                    ,
                    {{ $primary }}
                    ,
                    {{ $secondary }}
                );
        }

        /* ===== CORNER ORNAMENTS ===== */
        .corner-ornament {
            position: absolute;
            width: 20mm;
            height: 20mm;
        }

        .corner-tl {
            top: 14mm;
            left: 14mm;
            border-top: 2pt solid
                {{ $accent }}
            ;
            border-left: 2pt solid
                {{ $accent }}
            ;
        }

        .corner-tr {
            top: 14mm;
            right: 14mm;
            border-top: 2pt solid
                {{ $accent }}
            ;
            border-right: 2pt solid
                {{ $accent }}
            ;
        }

        .corner-bl {
            bottom: 14mm;
            left: 14mm;
            border-bottom: 2pt solid
                {{ $accent }}
            ;
            border-left: 2pt solid
                {{ $accent }}
            ;
        }

        .corner-br {
            bottom: 14mm;
            right: 14mm;
            border-bottom: 2pt solid
                {{ $accent }}
            ;
            border-right: 2pt solid
                {{ $accent }}
            ;
        }

        /* ===== WATERMARK ===== */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 120mm;
            height: 120mm;
            margin-top: -60mm;
            margin-left: -60mm;
            opacity: 0.04;
            z-index: 0;
        }

        .watermark img {
            width: 100%;
            height: 100%;
        }

        /* ===== LEFT SIDE ACCENT STRIPE ===== */
        .side-stripe-left {
            position: absolute;
            top: 12mm;
            bottom: 12mm;
            left: 12mm;
            width: 2mm;
            background: linear-gradient(180deg,
                    {{ $primary }}
                    0%,
                    {{ $accent }}
                    50%,
                    {{ $secondary }}
                    100%);
            opacity: 0.6;
        }

        .side-stripe-right {
            position: absolute;
            top: 12mm;
            bottom: 12mm;
            right: 12mm;
            width: 2mm;
            background: linear-gradient(180deg,
                    {{ $secondary }}
                    0%,
                    {{ $accent }}
                    50%,
                    {{ $primary }}
                    100%);
            opacity: 0.6;
        }

        /* ===== CONTENT CONTAINER ===== */
        .content-area {
            position: absolute;
            top: 20mm;
            left: 25mm;
            right: 25mm;
            bottom: 20mm;
            text-align: center;
            z-index: 1;
        }

        /* ===== HEADER: LOGO + ORGANIZATION ===== */
        .header-logo {
            margin-bottom: 3mm;
        }

        .header-logo img {
            height: 16mm;
            width: auto;
        }

        .org-name {
            font-size: 12pt;
            font-weight: 700;
            color:
                {{ $primary }}
            ;
            letter-spacing: 4pt;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }

        .org-subtitle {
            font-size: 7pt;
            color: #7f8c8d;
            letter-spacing: 2pt;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        /* ===== CONFERENCE NAME BANNER ===== */
        .conference-banner {
            margin: 4mm auto;
            padding: 2.5mm 15mm;
            border-top: 0.5pt solid
                {{ $primaryMedium }}
            ;
            border-bottom: 0.5pt solid
                {{ $primaryMedium }}
            ;
            display: inline-block;
        }

        .conference-banner-text {
            font-size: 8.5pt;
            font-weight: 600;
            color:
                {{ $secondary }}
            ;
            letter-spacing: 2pt;
            text-transform: uppercase;
        }

        /* ===== CERTIFICATE TITLE ===== */
        .cert-title-section {
            margin: 6mm 0 4mm;
        }

        .cert-title {
            font-size: 36pt;
            font-weight: 700;
            color:
                {{ $primary }}
            ;
            letter-spacing: 5pt;
            text-transform: uppercase;
            line-height: 1.0;
            margin-bottom: 2mm;
        }

        .cert-type {
            font-size: 14pt;
            font-weight: 600;
            color:
                {{ $accent }}
            ;
            letter-spacing: 6pt;
            text-transform: uppercase;
        }

        /* ===== DIVIDER ===== */
        .divider {
            width: 80mm;
            height: 0;
            border-top: 1pt solid
                {{ $accent }}
            ;
            margin: 5mm auto;
        }

        .divider-double {
            width: 60mm;
            margin: 4mm auto;
            border-top: 1.5pt double
                {{ $accent }}
            ;
        }

        /* ===== PRESENTED TO ===== */
        .presented-text {
            font-size: 9pt;
            color: #7f8c8d;
            letter-spacing: 2pt;
            text-transform: uppercase;
            margin-bottom: 3mm;
        }

        /* ===== AUTHOR NAME ===== */
        .author-name {
            font-size: 24pt;
            font-weight: 700;
            color: #1a1a2e;
            padding-bottom: 2mm;
            border-bottom: 1.5pt solid
                {{ $accent }}
            ;
            display: inline-block;
            padding-left: 12mm;
            padding-right: 12mm;
            margin-bottom: 4mm;
            letter-spacing: 1pt;
        }

        /* ===== ARTICLE INFO ===== */
        .article-info {
            margin: 3mm auto;
            max-width: 200mm;
        }

        .article-label {
            font-size: 7.5pt;
            color: #95a5a6;
            letter-spacing: 1.5pt;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }

        .article-title-text {
            font-size: 10pt;
            font-weight: 600;
            color: #34495e;
            font-style: italic;
            line-height: 1.5;
            max-width: 190mm;
            margin: 0 auto;
        }

        /* ===== CONFERENCE DETAILS ===== */
        .conf-details {
            margin: 3mm auto;
        }

        .conf-label {
            font-size: 7.5pt;
            color: #95a5a6;
            letter-spacing: 1.5pt;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }

        .conf-name-text {
            font-size: 9.5pt;
            font-weight: 600;
            color:
                {{ $primary }}
            ;
            line-height: 1.4;
        }

        .country-text {
            font-size: 8.5pt;
            color: #5d6d7e;
            margin-top: 1mm;
            letter-spacing: 1pt;
        }

        /* ===== FOOTER SECTION ===== */
        .footer-area {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: bottom;
            padding: 0 8mm;
        }

        .footer-left {
            width: 30%;
            text-align: left;
        }

        .footer-center {
            width: 40%;
            text-align: center;
        }

        .footer-right {
            width: 30%;
            text-align: right;
        }

        /* ===== QR CODE ===== */
        .qr-code-box {
            display: inline-block;
            padding: 1.5mm;
            border: 0.5pt solid #ddd;
            background: #fff;
        }

        .qr-code-box img {
            width: 18mm;
            height: 18mm;
        }

        .qr-label {
            font-size: 5pt;
            color: #aaa;
            text-align: center;
            margin-top: 1mm;
            letter-spacing: 0.5pt;
        }

        /* ===== SIGNATURE AREA ===== */
        .signature-block {
            text-align: center;
            margin: 0 auto;
        }

        .signature-line {
            width: 50mm;
            border-top: 0.75pt solid #34495e;
            margin: 0 auto 1.5mm;
        }

        .signature-title {
            font-size: 7pt;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1pt;
            font-weight: 600;
        }

        .signature-name {
            font-size: 6.5pt;
            color: #95a5a6;
            margin-top: 0.5mm;
        }

        /* ===== CERTIFICATE ID & DATE ===== */
        .cert-id {
            position: absolute;
            top: 22mm;
            right: 28mm;
            font-size: 7.5pt;
            color: #95a5a6;
            letter-spacing: 0.5pt;
            z-index: 2;
        }

        .cert-date-badge {
            position: absolute;
            top: 22mm;
            left: 28mm;
            z-index: 2;
        }

        .cert-date-value {
            font-size: 8pt;
            font-weight: 600;
            color:
                {{ $primary }}
            ;
        }

        .cert-date-label {
            font-size: 6pt;
            color: #aab;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }

        /* ===== SEAL ===== */
        .seal-wrapper {
            display: inline-block;
            width: 24mm;
            height: 24mm;
            border: 2pt solid
                {{ $primary }}
            ;
            border-radius: 50%;
            text-align: center;
            position: relative;
            background: rgba(255, 255, 255, 0.95);
        }

        .seal-inner-ring {
            position: absolute;
            top: 2mm;
            left: 2mm;
            right: 2mm;
            bottom: 2mm;
            border: 1pt solid
                {{ $primary }}
            ;
            border-radius: 50%;
        }

        .seal-text {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16mm;
            margin-left: -8mm;
            margin-top: -5mm;
            font-size: 5pt;
            color:
                {{ $primary }}
            ;
            text-transform: uppercase;
            font-weight: 700;
            line-height: 1.4;
            letter-spacing: 0.3pt;
        }

        /* ===== METADATA ROW ===== */
        .metadata-row {
            margin: 3mm auto;
        }

        .metadata-table {
            margin: 0 auto;
            border-collapse: collapse;
        }

        .metadata-table td {
            padding: 1mm 6mm;
            text-align: center;
        }

        .meta-label {
            font-size: 6pt;
            color: #aab;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }

        .meta-value {
            font-size: 8pt;
            color: #34495e;
            font-weight: 600;
        }

        .meta-divider {
            width: 0;
            border-left: 0.5pt solid #ddd;
            padding: 0 !important;
        }
    </style>
</head>

<body>
    <!-- Top accent bar -->
    <div class="top-accent-bar"></div>

    <!-- Bottom accent bar -->
    <div class="bottom-accent-bar"></div>

    <!-- Outer gold frame -->
    <div class="cert-frame">
        <div class="cert-frame-inner"></div>
    </div>

    <!-- Corner ornaments -->
    <div class="corner-ornament corner-tl"></div>
    <div class="corner-ornament corner-tr"></div>
    <div class="corner-ornament corner-bl"></div>
    <div class="corner-ornament corner-br"></div>

    <!-- Side stripes -->
    <div class="side-stripe-left"></div>
    <div class="side-stripe-right"></div>

    <!-- Watermark - country-specific SVG -->
    @if($watermarkBase64)
        <div class="watermark">
            <img src="{{ $watermarkBase64 }}" alt="">
        </div>
    @endif

    <!-- Certificate ID -->
    <div class="cert-id">№ {{ $certNumber }}</div>

    <!-- Date badge -->
    <div class="cert-date-badge">
        <div class="cert-date-value">{{ $issueDate }}</div>
        <div class="cert-date-label">Issue Date</div>
    </div>

    <!-- Main content -->
    <div class="content-area">
        <!-- Logo + Organization -->
        <div class="header-logo">
            @if($iscLogoBase64)
                <img src="{{ $iscLogoBase64 }}" alt="ISC">
            @endif
        </div>
        <div class="org-name">International Scientific Conferences</div>
        <div class="org-subtitle">Open Access · Peer-Reviewed · Scientific Online Conferences</div>

        <!-- Conference banner -->
        <div style="text-align: center;">
            <div class="conference-banner">
                <div class="conference-banner-text">{{ $countryNameDisplay }}</div>
            </div>
        </div>

        <!-- Certificate Title -->
        <div class="cert-title-section">
            <div class="cert-title">Certificate</div>
            <div class="cert-type">of Publication</div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Presented to -->
        <div class="presented-text">This is to certify that</div>

        <!-- Author Name -->
        <div class="author-name">{{ $authorName }}</div>

        <!-- Article info -->
        <div class="article-info">
            <div class="article-label">has published a scientific article entitled</div>
            <div class="article-title-text">"{{ $articleTitle }}"</div>
        </div>

        <!-- Conference details -->
        <div class="conf-details">
            <div class="conf-label">at the international scientific online conference</div>
            <div class="conf-name-text">"{{ $conferenceName }}"</div>
            <div class="country-text">{{ $countryNameDisplay }} · {{ $confDate }}</div>
        </div>

        <!-- Metadata row: Cert ID, Date, Country -->
        <div class="metadata-row">
            <table class="metadata-table">
                <tr>
                    <td>
                        <div class="meta-label">Certificate No.</div>
                        <div class="meta-value">{{ $certNumber }}</div>
                    </td>
                    <td class="meta-divider"></td>
                    <td>
                        <div class="meta-label">Country</div>
                        <div class="meta-value">{{ $country->name_en ?? $country->name }}</div>
                    </td>
                    <td class="meta-divider"></td>
                    <td>
                        <div class="meta-label">Date</div>
                        <div class="meta-value">{{ $issueDate }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer area -->
        <div class="footer-area">
            <table class="footer-table">
                <tr>
                    <!-- QR Code (left) -->
                    <td class="footer-left">
                        @if($qrBase64)
                            <div class="qr-code-box">
                                <img src="{{ $qrBase64 }}" alt="QR">
                            </div>
                            <div class="qr-label">Scan to verify</div>
                        @else
                            <div class="qr-code-box" style="width:18mm;height:18mm;display:inline-block;">
                                <div style="font-size:5pt;color:#ccc;text-align:center;padding-top:6mm;">QR CODE</div>
                            </div>
                            <div class="qr-label">Verification</div>
                        @endif
                    </td>

                    <!-- Signatures (center) -->
                    <td class="footer-center">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="width:50%;text-align:center;padding:0 5mm;">
                                    <div class="signature-block">
                                        <div class="signature-line"></div>
                                        <div class="signature-title">Conference Director</div>
                                    </div>
                                </td>
                                <td style="width:50%;text-align:center;padding:0 5mm;">
                                    <div class="signature-block">
                                        <div class="signature-line"></div>
                                        <div class="signature-title">Chief Editor</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- Seal (right) -->
                    <td class="footer-right">
                        <div class="seal-wrapper">
                            <div class="seal-inner-ring"></div>
                            <div class="seal-text">
                                International<br>
                                Scientific<br>
                                Conferences
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>