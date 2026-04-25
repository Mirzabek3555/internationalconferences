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
            font-family: Georgia, 'Times New Roman', Times, serif;
            width: 210mm;
            height: 297mm;
            position: relative;
            background: #ffffff;
        }

        .cover {
            width: 210mm;
            height: 297mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
        }

        /* ============================
           YUQORI QISM: DAVLAT RASMI
           ============================ */
        .photo-section {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%; /* Rasm butun sahifani egallaydi */
            z-index: 1; /* Matn uning ustiga chiqadi */
            overflow: hidden;
        }

        .photo-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .photo-section-bg {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #6ba5ff 0%, #17428f 100%);
        }

        /* Rasmdan oq rangga gradient (Puppeteer qo'llab-quvvatlaydi, shuning uchun sof CSS) */
        .photo-fade {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            background: linear-gradient(to bottom, 
                rgba(255,255,255,0) 0%, 
                rgba(255,255,255,0) 30%, 
                rgba(255,255,255,0.7) 50%, 
                rgba(255,255,255,0.95) 75%, 
                rgba(255,255,255,1) 100%);
        }

        /* ============================
           ICP LOGO (Yuqori o'ng burchak)
           ============================ */
        .icp-logo-corner {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 10;
            width: 160px;
        }

        .icp-logo-corner img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* ============================
           DAVLAT NOMI (qizil, katta)
           ============================ */
        .country-name-section {
            position: absolute;
            bottom: 65mm;
            right: 0;
            left: 0;
            z-index: 11;
            padding-right: 15mm;
            text-align: right;
        }

        .country-name-wrapper {
            display: inline-block;
            text-align: center;
        }

        .country-name-text {
            font-size: 52px;
            font-weight: 900;
            color: #cc0000;
            line-height: 1.0;
            text-transform: uppercase;
        }

        .country-name-underline {
            height: 4px;
            background: #cc0000;
            width: 100%;
            margin-top: 3px;
        }

        /* ============================
           PASTKI MAZMUN VA FOOTER
           ============================ */
        .conference-title-block {
            position: absolute;
            bottom: 40mm;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 10;
        }

        .conference-main-title {
            font-size: 16px;
            font-weight: 900;
            color: #041E4F;
            text-transform: uppercase;
            line-height: 1.4;
            margin-bottom: 4px;
            padding: 0 5mm;
        }

        .conference-sub-title {
            font-size: 12px;
            font-weight: 900;
            color: #041E4F;
            text-transform: uppercase;
        }

        /* Footer flexbox layout */
        .footer-container {
            position: absolute;
            bottom: 12mm;
            left: 15mm;
            right: 15mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .footer-website {
            flex: 1;
            font-size: 10px;
            font-weight: 900;
            color: #041E4F;
            text-transform: uppercase;
            text-align: left;
        }

        .footer-flag-container {
            flex: 1;
            text-align: center;
        }

        .footer-flag-container img, .footer-flag-placeholder {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 3px 6px rgba(0,0,0,0.4);
            display: inline-block;
        }
        
        .footer-flag-placeholder {
            background: #cccccc;
            line-height: 55px;
            font-size: 22px;
        }

        .footer-qr-container {
            flex: 1;
            text-align: right;
        }

        .footer-qr-container img,
        .footer-qr-container svg {
            width: 120px;
            height: 120px;
            display: inline-block;
        }

    </style>
</head>

<body>
    <div class="cover">

        @php
            // Rasm izlash: endi to'g'ridan-to'g'ri sertifikatdagi kabi images/certificates/backgrounds papkasidan olinadi
            $rawCode = strtoupper($country->code ?? 'GB');
            $alpha3 = [
                'UZB' => 'UZ', 'GBR' => 'GB', 'USA' => 'US', 'DEU' => 'DE',
                'FRA' => 'FR', 'ITA' => 'IT', 'ESP' => 'ES', 'RUS' => 'RU',
                'JPN' => 'JP', 'CHN' => 'CN', 'KOR' => 'KR', 'TUR' => 'TR',
                'POL' => 'PL', 'KAZ' => 'KZ', 'IND' => 'IN', 'BRA' => 'BR',
                'CAN' => 'CA', 'TKM' => 'TM', 'AUS' => 'AU',
            ];
            $countryCode = strlen($rawCode) === 3 ? ($alpha3[$rawCode] ?? substr($rawCode, 0, 2)) : $rawCode;

            $bgDir = public_path('images/certificates/backgrounds');
            $bgMap = [
                'UZ' => 'registon_samarqand.png',
                'GB' => 'london_bridge.png',
                'DE' => 'reichstag_berlin.png',
                'RU' => 'russia_moscow.png',
                'FR' => 'france_paris.png',
                'TR' => 'turkey_istanbul.png',
                'JP' => 'japan_fuji.png',
                'CN' => 'china_wall.png',
                'US' => 'usa_washington.png',
                'KZ' => 'kazakhstan_astana.png',
                'KR' => 'korea_seoul.png',
                'IN' => 'india_tajmahal.png',
                'IT' => 'italy_rome.png',
                'ES' => 'spain_barcelona.png',
                'PL' => 'poland_krakow.png',
                'BR' => 'brazil_rio.png',
                'CA' => 'canada_niagara.png',
                'TM' => 'turkmenistan_ashgabat.png',
            ];

            $finalPhoto = null;
            if (isset($bgMap[$countryCode])) {
                $path = $bgDir . DIRECTORY_SEPARATOR . $bgMap[$countryCode];
                if (file_exists($path)) {
                    $finalPhoto = $path;
                }
            }

            // Agar davlat bo'yicha topilmasa, shu papkadagi birinchi rasmni olamiz (fallback)
            if (!$finalPhoto) {
                $files = glob($bgDir . DIRECTORY_SEPARATOR . '*.png');
                if (!empty($files)) {
                    $finalPhoto = $files[0];
                }
            }

            $finalPhotoBase64 = null;
            if ($finalPhoto && file_exists($finalPhoto)) {
                $mime = mime_content_type($finalPhoto) ?: 'image/png';
                $finalPhotoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($finalPhoto));
            }

            // Bayroq yo'li (Bazadagi haqiqiy bayroqdan olish)
            $flagPath = null;
            if ($country->flag_url) {
                $fp1 = Storage::disk('public')->exists($country->flag_url)
                    ? Storage::disk('public')->path($country->flag_url)
                    : public_path('storage/' . $country->flag_url);
                if (file_exists($fp1)) {
                    $flagPath = $fp1;
                }
            }
            // DomPDF / Puppeteer render muammolari yechimi: Base64
            $flagBase64 = null;
            if ($flagPath && file_exists($flagPath)) {
                $mime = mime_content_type($flagPath) ?: 'image/png';
                $flagBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($flagPath));
            } else {
                // Asl Davlat bayrog'ini onlayn tarzda ISO kod orqali yuklash
                $iso = strtolower($countryCode ?? 'gb');
                $flagApiUrl = "https://flagcdn.com/w160/{$iso}.png";
                $flagApiData = @file_get_contents($flagApiUrl);
                if ($flagApiData) {
                    $flagBase64 = 'data:image/png;base64,' . base64_encode($flagApiData);
                }
            }

            // QR kod sertifikatdagi usul orqali (api.qrserver.com PNG base64)
            $qrData = urlencode('https://internationalscientificconferences.org');
            // Try fetching with specific dark blue color to match the text
            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=".$qrData."&color=041E4F";
            $qrPngData = @file_get_contents($qrApiUrl);
            if (!$qrPngData) {
                // Fallback to default black if color flag fails
                $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=".$qrData;
                $qrPngData = @file_get_contents($qrApiUrl);
            }
            $certQrBase64 = $qrPngData ? 'data:image/png;base64,' . base64_encode($qrPngData) : null;

            // Fallback qoida (zaxira ucbhun mahalliy bg/qr)
            $qrCodePath = public_path('images/qr-code.png');
            $hasQrCode  = file_exists($qrCodePath);

            // ICP logo
            $icpLogoPath  = public_path('images/logo.png');
            $hasIcpLogo   = file_exists($icpLogoPath);
            $icpLogoBase64 = null;
            if ($hasIcpLogo) {
                $mime = mime_content_type($icpLogoPath) ?: 'image/png';
                $icpLogoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($icpLogoPath));
            }

            // Davlat inglizcha nomi
            $countryNameEn = strtoupper($country->name_en ?? $country->name ?? 'COUNTRY');

            // Konferensiya joyi (mavzusi)
            $confMainTitle = strtoupper($country->conference_name ?? 'PROSPECTS FOR INNOVATIVE TECHNOLOGIES IN SCIENCE AND EDUCATION');

            // Konferensiya turi
            $confSubTitle = 'INTERNATIONAL ONLINE CONFERENCE';
        @endphp

        {{-- FOTO QISMI --}}
        <div class="photo-section">
            @if($finalPhotoBase64)
                <img src="{{ $finalPhotoBase64 }}" alt="{{ $country->name }}">
            @else
                <div class="photo-section-bg"></div>
            @endif
            {{-- GRADIENT FADE --}}
            <div class="photo-fade"></div>
        </div>

        {{-- ICP LOGO (yuqori o'ng) --}}
        @if($icpLogoBase64)
        <div class="icp-logo-corner">
            <img src="{{ $icpLogoBase64 }}" alt="ICP Logo">
        </div>
        @endif

        {{-- DAVLAT NOMI --}}
        <div class="country-name-section">
            @php
                // Davlat nomini ikki qatorga bo'lish (2 so'z yoki undan ko'p bo'lsa)
                $nameParts = explode(' ', $countryNameEn);
                $nameLine1 = $nameParts[0] ?? $countryNameEn;
                $nameLine2 = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
            @endphp
            <div class="country-name-wrapper">
                <div class="country-name-text">
                    {{ $nameLine1 }}<br>
                    @if($nameLine2)
                        {{ $nameLine2 }}
                    @endif
                </div>
                <div class="country-name-underline"></div>
            </div>
        </div>

        {{-- PASTKI MAZMUN --}}
        {{-- Sarlavha --}}
        <div class="conference-title-block">
            <div class="conference-main-title">{{ $confMainTitle }}</div>
            <div class="conference-sub-title">{{ $confSubTitle }}</div>
        </div>

        {{-- PASTKI FOOTER QISMI --}}
        <div class="footer-container">
            {{-- Veb-sayt (Chap burchak) --}}
            <div class="footer-website">
                WWW.INTERNATIONALSCIENTIFICCONFERENCES.ORG
            </div>

            {{-- Bayroq (Markazda) --}}
            <div class="footer-flag-container">
                @if($flagBase64)
                    <img src="{{ $flagBase64 }}" alt="{{ $country->name }} flag">
                @elseif($flagPath)
                    <img src="{{ $flagPath }}" alt="{{ $country->name }} flag">
                @else
                    <div class="footer-flag-placeholder">🌍</div>
                @endif
            </div>

            {{-- QR kod (O'ng burchak) --}}
            <div class="footer-qr-container">
                @if($certQrBase64)
                    <img src="{{ $certQrBase64 }}" alt="QR Code">
                @elseif($hasQrCode)
                    <img src="{{ $qrCodePath }}" alt="QR Code">
                @endif
            </div>
        </div>

    </div>
</body>

</html>