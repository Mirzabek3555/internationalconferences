<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
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
            font-family: 'DejaVu Sans', sans-serif;
            width: 297mm;
            height: 210mm;
            background: #fff;
            position: relative;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            display: flex;
        }

        /* Left content section - approximately 65% */
        .left-section {
            width: 65%;
            padding: 25px 30px;
            position: relative;
            z-index: 10;
            background: #fff;
        }

        /* Watermark layer */
        .watermark-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .watermark-layer img {
            max-width: 70%;
            max-height: 70%;
            opacity: 0.06;
        }

        /* Right image section - approximately 35% */
        .right-section {
            width: 35%;
            position: relative;
            overflow: hidden;
        }

        /* Curved divider */
        .curve-divider {
            position: absolute;
            top: 0;
            left: -60px;
            width: 120px;
            height: 100%;
            background: #fff;
            z-index: 5;
            border-radius: 0 50% 50% 0;
        }

        /* Country images background */
        .country-images {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .country-image-main {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 60%;
        }

        .country-image-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .country-image-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 45%;
        }

        .country-image-bottom img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ISOC Logo section */
        .isoc-logo-section {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 20;
            text-align: right;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 15px;
            border-radius: 8px;
        }

        .isoc-logo-img {
            height: 50px;
            width: auto;
        }

        .isoc-text {
            font-size: 20px;
            font-weight: 800;
            color: #1a3a5f;
            letter-spacing: 2px;
        }

        .isoc-subtext {
            font-size: 7px;
            color: #666;
            letter-spacing: 0.5px;
        }

        /* Country header */
        .country-header {
            text-align: left;
            margin-bottom: 8px;
        }

        .country-name-top {
            font-size: 16px;
            font-weight: 700;
            color: #c41e3a;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        /* Certificate title */
        .certificate-title {
            margin: 10px 0 5px;
        }

        .cert-text {
            font-size: 52px;
            font-weight: 800;
            color: #c41e3a;
            letter-spacing: 4px;
            text-transform: uppercase;
            line-height: 1;
        }

        .cert-subtitle {
            font-size: 18px;
            font-weight: 400;
            color: #333;
            font-style: italic;
            margin-top: 3px;
        }

        /* Flags section */
        .flags-row {
            margin: 12px 0;
        }

        .flag-small {
            width: 35px;
            height: 22px;
            object-fit: cover;
            border-radius: 2px;
            margin-right: 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        /* Author section */
        .author-section {
            margin: 20px 0 15px;
            text-align: left;
        }

        .author-name {
            font-size: 26px;
            font-weight: 700;
            color: #1a3a5f;
            border-bottom: 2px solid #1a3a5f;
            display: inline-block;
            padding-bottom: 5px;
        }

        /* Conference info */
        .conference-info {
            margin: 15px 0;
            text-align: left;
            line-height: 1.6;
        }

        .info-line {
            font-size: 12px;
            color: #333;
        }

        .conference-name-text {
            font-size: 11px;
            color: #333;
            font-weight: 400;
        }

        .conference-name-highlight {
            color: #1a3a5f;
            font-weight: 600;
        }

        /* Article title */
        .article-title-section {
            margin: 10px 0;
            text-align: left;
        }

        .article-title-text {
            font-size: 14px;
            font-weight: 700;
            color: #c41e3a;
            text-transform: uppercase;
            line-height: 1.4;
        }

        /* Coat of Arms */
        .coat-of-arms {
            position: absolute;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
            width: 140px;
            opacity: 0.12;
            z-index: 0;
        }

        .coat-of-arms img {
            width: 100%;
            height: auto;
        }

        /* Conference stamp/seal */
        .conference-seal {
            position: absolute;
            right: 20px;
            bottom: 35px;
            z-index: 30;
            text-align: center;
        }

        .seal-circle {
            width: 70px;
            height: 70px;
            border: 3px solid #1a3a5f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
        }

        .seal-inner {
            width: 55px;
            height: 55px;
            border: 1px solid #1a3a5f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .seal-text {
            font-size: 6px;
            color: #1a3a5f;
            text-align: center;
            text-transform: uppercase;
            font-weight: 600;
            line-height: 1.2;
        }

        /* Footer section */
        .footer-section {
            position: absolute;
            bottom: 20px;
            left: 30px;
            right: 40%;
            z-index: 10;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: bottom;
            text-align: center;
            padding: 5px 10px;
        }

        .footer-label {
            font-size: 8px;
            color: #999;
            margin-bottom: 2px;
        }

        .footer-value {
            font-size: 11px;
            color: #333;
            font-weight: 600;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 80px;
            margin: 0 auto 3px;
        }

        .platform-logo {
            font-size: 8px;
            color: #666;
        }

        /* Certificate number */
        .cert-number {
            position: absolute;
            top: 15px;
            right: 35%;
            font-size: 9px;
            color: #666;
            z-index: 10;
        }

        /* Editor name */
        .editor-section {
            text-align: center;
        }

        .editor-name {
            font-size: 10px;
            color: #333;
            font-weight: 600;
        }

        .editor-title {
            font-size: 8px;
            color: #666;
        }

        /* Date section */
        .date-section {
            text-align: center;
        }

        .date-value {
            font-size: 13px;
            color: #333;
            font-weight: 700;
        }

        /* QR Code Section */
        .qr-code-section {
            position: absolute;
            left: 15px;
            bottom: 35px;
            z-index: 30;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .qr-code-section img {
            width: 60px;
            height: 60px;
        }

        .qr-label {
            font-size: 5px;
            color: #666;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    @php
        // Davlat rasmlari
        $countryNameForFile = strtolower(str_replace(' ', '_', $country->name_en ?? $country->name ?? 'default'));
        $countryImagePath = public_path('images/countries/' . $countryNameForFile . '.png');
        $hasCountryImage = file_exists($countryImagePath);

        // Flag path
        $flagPath = $country->flag_url ? storage_path('app/public/' . $country->flag_url) : null;
        $hasFlag = $flagPath && file_exists($flagPath);

        // Cover image
        $coverImagePath = $country->cover_image ? public_path($country->cover_image) : null;
        $hasCoverImage = $coverImagePath && file_exists($coverImagePath);

        // ISOC logo
        $isocLogoPath = public_path('images/isoc_logo.png');
        $hasIsocLogo = file_exists($isocLogoPath);

        // Watermark path (SVG)
        $watermarkPath = public_path('images/watermarks/' . $countryNameForFile . '.svg');
        $hasWatermark = file_exists($watermarkPath);
    @endphp

    <div class="certificate-container">
        <!-- Left Section -->
        <div class="left-section">
            <!-- Country-specific watermark background -->
            @if($hasWatermark)
                <div class="watermark-layer">
                    <img src="{{ $watermarkPath }}" alt="">
                </div>
            @endif

            <!-- Coat of Arms watermark (fallback) -->
            @if($hasCountryImage && !$hasWatermark)
                <div class="coat-of-arms">
                    <img src="{{ $countryImagePath }}" alt="">
                </div>
            @endif

            <!-- Country header -->
            <div class="country-header">
                <div class="country-name-top">{{ strtoupper($country->name_en ?? $country->name) }}</div>
            </div>

            <!-- Certificate Title -->
            <div class="certificate-title">
                <div class="cert-text">CERTIFICATE</div>
                <div class="cert-subtitle">of conference participant</div>
            </div>

            <!-- Flags -->
            @if($hasFlag)
                <div class="flags-row">
                    <img src="{{ $flagPath }}" class="flag-small" alt="Flag">
                    <img src="{{ $flagPath }}" class="flag-small" alt="Flag">
                </div>
            @endif

            <!-- Author Name -->
            <div class="author-section">
                <div class="author-name">{{ $article->author_name ?? $article->author_display_name }}</div>
            </div>

            <!-- Conference Info -->
            <div class="conference-info">
                <div class="info-line">for participation in the scientific-online conference</div>
                <div class="conference-name-text">
                    &lt;&lt;<span
                        class="conference-name-highlight">{{ strtoupper($country->conference_name ?? $conference->title) }}</span>&gt;&gt;
                    with an article entitled
                </div>
            </div>

            <!-- Article Title -->
            <div class="article-title-section">
                <div class="article-title-text">{{ strtoupper($article->title) }}</div>
            </div>

            <!-- Footer -->
            <div class="footer-section">
                <table class="footer-table">
                    <tr>
                        <td style="width: 25%;">
                            <div class="platform-logo">
                                Platform &<br>workflow by<br>
                                <strong>ARTIQLE</strong>
                            </div>
                        </td>
                        <td style="width: 25%;">
                            <div class="date-section">
                                <div class="date-value">{{ $certificate->issue_date->format('d.m.Y') }}</div>
                            </div>
                        </td>
                        <td style="width: 25%;">
                            <div class="signature-line"></div>
                            <div class="footer-label">Signature</div>
                        </td>
                        <td style="width: 25%;">
                            <div class="editor-section">
                                <div class="editor-name">Conference Director</div>
                                <div class="editor-title">CHIEF EDITOR</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <!-- Curved divider -->
            <div class="curve-divider"></div>

            <!-- Country Images -->
            <div class="country-images">
                @if($hasCountryImage)
                    <div class="country-image-main">
                        <img src="{{ $countryImagePath }}" alt="{{ $country->name }}">
                    </div>
                @elseif($hasCoverImage)
                    <div class="country-image-main">
                        <img src="{{ $coverImagePath }}" alt="{{ $country->name }}">
                    </div>
                @endif

                @if($hasCoverImage && $hasCountryImage)
                    <div class="country-image-bottom">
                        <img src="{{ $coverImagePath }}" alt="{{ $country->name }}">
                    </div>
                @elseif($hasCountryImage)
                    <div class="country-image-bottom">
                        <img src="{{ $countryImagePath }}" alt="{{ $country->name }}">
                    </div>
                @endif
            </div>

            <!-- ISOC Logo -->
            <div class="isoc-logo-section">
                @if($hasIsocLogo)
                    <img src="{{ $isocLogoPath }}" class="isoc-logo-img" alt="ISOC">
                @else
                    <div class="isoc-text">ISOC</div>
                @endif
                <div class="isoc-subtext">INTERNATIONAL<br>SCIENTIFIC<br>ONLINE<br>CONFERENCES</div>
            </div>

            <!-- Conference Seal -->
            <div class="conference-seal">
                <div class="seal-circle">
                    <div class="seal-inner">
                        <div class="seal-text">
                            INTERNATIONAL<br>
                            SCIENTIFIC<br>
                            ONLINE<br>
                            CONFERENCES
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-code-section">
            @php
                $qrCodePath = public_path('images/qr-code.png');
                $hasQrCode = file_exists($qrCodePath);
            @endphp
            @if($hasQrCode)
                <img src="{{ $qrCodePath }}" alt="QR Code">
            @else
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->color(26, 58, 95)->generate('https://internationalscientificconferences.org') !!}
            @endif
            <div class="qr-label">Scan to visit</div>
        </div>

        <!-- Certificate Number -->
        <div class="cert-number">№ {{ $certificate->certificate_number }}</div>
    </div>
</body>

</html>