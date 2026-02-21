<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $conference->title }} - Collection</title>
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
        }

        .cover {
            width: 100%;
            height: 297mm;
            position: relative;
            overflow: hidden;
        }

        /* Davlat ramzlari - to'liq sahifa rasmi */
        .country-symbols-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .country-symbols-background img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Qoraytiruvchi qatlam */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg,
                    rgba(0, 0, 0, 0.4) 0%,
                    rgba(0, 0, 0, 0.2) 30%,
                    rgba(0, 0, 0, 0.2) 70%,
                    rgba(0, 0, 0, 0.6) 100%);
            z-index: 2;
        }

        /* Header section */
        .header-section {
            position: relative;
            z-index: 10;
            padding: 30px;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .org-name {
            font-size: 11px;
            color: #666;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .country-name {
            font-size: 42px;
            font-weight: 800;
            color: #1a3a5f;
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-bottom: 10px;
            text-shadow: none;
        }

        .conference-type {
            font-size: 10px;
            color: #888;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        /* Flag display */
        .flag-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .flag-img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            border: 2px solid #fff;
        }

        /* Title section - markazda */
        .title-section {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            width: 85%;
            padding: 40px 50px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .main-title {
            font-size: 26px;
            font-weight: 700;
            color: #1a3a5f;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
        }

        .date-display {
            display: inline-block;
            background: linear-gradient(135deg, #c9a227 0%, #d4af37 100%);
            padding: 12px 35px;
            border-radius: 30px;
            color: #1a3a5f;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 5px 20px rgba(201, 162, 39, 0.4);
        }

        .date-icon {
            margin-right: 8px;
        }

        /* Stats section */
        .stats-section {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: #c9a227;
        }

        .stat-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 3px;
        }

        /* Bottom section */
        .bottom-section {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            padding: 25px 30px;
            z-index: 10;
        }

        /* Logo section in bottom */
        .logo-section {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-img {
            height: 50px;
            width: auto;
        }

        /* Info grid */
        .info-grid {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 2px solid #e0e0e0;
            border-bottom: 2px solid #e0e0e0;
        }

        .info-item {
            text-align: center;
            flex: 1;
        }

        .info-label {
            font-size: 8px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 12px;
            color: #1a3a5f;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding-top: 12px;
        }

        .footer-text {
            font-size: 9px;
            color: #999;
        }

        .footer-year {
            font-size: 11px;
            color: #1a3a5f;
            font-weight: 700;
            margin-top: 3px;
        }

        /* Website URL */
        .website-url {
            position: absolute;
            bottom: 140px;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 10;
        }

        .website-url span {
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 30px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            color: #1a3a5f;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        /* Decorative elements */
        .corner-decoration {
            position: absolute;
            z-index: 5;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.8) 0%, transparent 60%);
        }

        .corner-top-left {
            top: 0;
            left: 0;
            border-radius: 0 0 100% 0;
        }

        .corner-top-right {
            top: 0;
            right: 0;
            border-radius: 0 0 0 100%;
            background: linear-gradient(-135deg, rgba(201, 162, 39, 0.8) 0%, transparent 60%);
        }

        .corner-bottom-left {
            bottom: 130px;
            left: 0;
            border-radius: 0 100% 0 0;
            background: linear-gradient(45deg, rgba(201, 162, 39, 0.6) 0%, transparent 60%);
        }

        .corner-bottom-right {
            bottom: 130px;
            right: 0;
            border-radius: 100% 0 0 0;
            background: linear-gradient(-45deg, rgba(201, 162, 39, 0.6) 0%, transparent 60%);
        }

        /* QR Code Section */
        .qr-code-section {
            position: absolute;
            right: 30px;
            bottom: 150px;
            z-index: 20;
            text-align: center;
            background: rgba(255, 255, 255, 0.98);
            padding: 12px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .qr-code-section img,
        .qr-code-section svg {
            width: 80px;
            height: 80px;
        }

        .qr-label {
            font-size: 7px;
            color: #666;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <div class="cover">
        <!-- Davlat ramzlari - to'liq sahifa fon rasmi -->
        @php
            // Davlat nomidan fayl nomini yaratish (masalan: "United Kingdom" -> "united_kingdom", "South Korea" -> "south_korea")
            $countryNameForFile = strtolower(str_replace(' ', '_', $country->name_en ?? $country->name ?? 'default'));
            $countryImagePath = public_path('images/countries/' . $countryNameForFile . '.png');
            $hasCountryImage = file_exists($countryImagePath);

            // Agar cover_image mavjud bo'lsa
            $coverImagePath = $country->cover_image ? public_path($country->cover_image) : null;
            $hasCoverImage = $coverImagePath && file_exists($coverImagePath);

            // Watermark path (SVG)
            $watermarkPath = public_path('images/watermarks/' . $countryNameForFile . '.svg');
            $hasWatermark = file_exists($watermarkPath);
        @endphp

        @if($hasCountryImage)
            <div class="country-symbols-background">
                <img src="{{ $countryImagePath }}" alt="{{ $country->name }} National Symbols">
            </div>
        @elseif($hasCoverImage)
            <div class="country-symbols-background">
                <img src="{{ $coverImagePath }}" alt="{{ $country->name }}">
            </div>
        @else
            <div class="country-symbols-background" style="background: linear-gradient(180deg, #1a3a5f 0%, #0d2137 100%);">
            </div>
        @endif

        <!-- Overlay -->
        <div class="overlay"></div>

        <!-- Country-specific watermark (over title section) -->
        @if($hasWatermark)
            <div
                style="position: absolute; top: 35%; left: 50%; transform: translate(-50%, -50%); z-index: 8; opacity: 0.08; pointer-events: none;">
                <img src="{{ $watermarkPath }}" alt="" style="width: 250px; height: 250px;">
            </div>
        @endif


        <!-- Decorative corners -->
        <div class="corner-decoration corner-top-left"></div>
        <div class="corner-decoration corner-top-right"></div>
        <div class="corner-decoration corner-bottom-left"></div>
        <div class="corner-decoration corner-bottom-right"></div>

        <!-- Header Section -->
        <div class="header-section">
            <div class="org-name">International Scientific Online Conference</div>
            <div class="country-name">{{ strtoupper($country->name_en ?? $country->name) }}</div>
            <div class="conference-type">Scientific Articles Collection {{ $conference->conference_date->format('Y') }}
            </div>

            @if($country->flag_url)
                <div class="flag-container">
                    @php
                        $flagPath = Storage::disk('public')->exists($country->flag_url)
                            ? Storage::disk('public')->path($country->flag_url)
                            : public_path('storage/' . $country->flag_url);
                    @endphp
                    @if(file_exists($flagPath))
                        <img src="{{ $flagPath }}" class="flag-img" alt="Flag">
                        <img src="{{ $flagPath }}" class="flag-img" alt="Flag">
                    @endif
                </div>
            @endif
        </div>

        <!-- Title Section - Center -->
        <div class="title-section">
            <div class="main-title">{{ $conference->title }}</div>

            <div class="date-display">
                <span class="date-icon">📅</span>
                {{ $conference->conference_date->format('d F Y') }}
            </div>

            <div class="stats-section">
                <div class="stat-item">
                    <div class="stat-value">{{ $conference->articles->count() }}</div>
                    <div class="stat-label">Total Articles</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $conference->articles->where('status', 'published')->count() }}</div>
                    <div class="stat-label">Published</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $conference->conference_date->format('Y') }}</div>
                    <div class="stat-label">Year</div>
                </div>
            </div>
        </div>

        <!-- Website URL -->
        <div class="website-url">
            <span>www.internationalscientificconferences.org</span>
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
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->color(26, 58, 95)->generate('https://internationalscientificconferences.org') !!}
            @endif
            <div class="qr-label">Scan to visit website</div>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <!-- Logo -->
            <div class="logo-section">
                @if(file_exists(public_path('images/isoc_logo.png')))
                    <img src="{{ public_path('images/isoc_logo.png') }}" class="logo-img" alt="ISOC Logo">
                @else
                    <span style="font-size: 22px; font-weight: 700; color: #1a3a5f;">ARTIQLE</span>
                @endif
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Collection Date</div>
                    <div class="info-value">{{ $conference->conference_date->format('d.m.Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Country</div>
                    <div class="info-value">{{ $country->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Articles Count</div>
                    <div class="info-value">{{ $conference->articles->count() }} articles</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-text">International Scientific Online Conference | Artiqle Scientific Publishing
                </div>
                <div class="footer-year">{{ $country->name_en ?? $country->name }}
                    {{ $conference->conference_date->format('Y') }}</div>
            </div>
        </div>
    </div>
</body>

</html>