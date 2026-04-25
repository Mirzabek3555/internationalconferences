<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * { margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background: #fff;
        }

        /* ==========================================
           OUTER WRAPPER — full A4 landscape
           ========================================== */
        .cert-page {
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        /* ==========================================
           TWO-COLUMN TABLE LAYOUT
           ========================================== */
        .cert-table {
            width: 297mm;
            height: 210mm;
            border-collapse: collapse;
        }

        /* LEFT CELL — davlat ranglari paneli */
        .cell-left {
            width: 105mm;
            height: 210mm;
            vertical-align: top;
            padding: 0;
            border-right: 6px solid; /* davlat accent rangi bilan beriladi */
        }

        /* Ichki kontainer (left panel) */
        .left-inner {
            width: 105mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
        }

        /* Davlat rangi shakllar: uch ustun stripe */
        .stripe-top {
            height: 4mm;
            width: 105mm;
        }
        .stripe-bottom {
            height: 4mm;
            width: 105mm;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        /* Logo area */
        .left-logo-area {
            text-align: center;
            padding-top: 12mm;
        }
        .left-logo-area img {
            width: 40px;
            height: 40px;
        }
        .left-logo-text {
            font-size: 6.5pt;
            font-weight: bold;
            color: #fff;
            letter-spacing: 2px;
            margin-top: 2mm;
            display: block;
        }

        /* Chap panel body */
        .left-body {
            padding: 8mm 6mm;
            text-align: center;
        }

        /* Davlat nomi vertikal (chapda) */
        .country-vert {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: rgba(255,255,255,0.9);
            text-transform: uppercase;
            margin-top: 6mm;
        }

        /* Flag rasm */
        .flag-img {
            width: 52px;
            height: 34px;
            border: 2px solid rgba(255,255,255,0.6);
            margin-top: 6mm;
        }

        /* Davlat nomi tagida */
        .country-name {
            font-size: 6pt;
            color: rgba(255,255,255,0.85);
            font-weight: bold;
            letter-spacing: 2.5px;
            margin-top: 3mm;
            text-transform: uppercase;
        }

        /* ==========================================
           RIGHT CELL
           ========================================== */
        .cell-right {
            width: 192mm;
            height: 210mm;
            vertical-align: top;
            padding: 0;
            background: #fff;
        }

        /* Yuqori rang tasma */
        .right-top-bar {
            height: 8px;
            width: 100%;
        }

        /* Asosiy kontent */
        .right-content {
            padding: 5mm 10mm 4mm 14mm;
        }

        /* CERTIFICATE sarlavha bloki */
        .cert-header {
            padding: 4mm 10mm 4mm 12mm;
            margin-bottom: 4mm;
            position: relative;
        }

        /* Chap diagonal aksent barchiq */
        .cert-header-accent {
            width: 14px;
            height: 100%;
            position: absolute;
            left: -6px;
            top: 0;
            bottom: 0;
        }

        .cert-h1 {
            font-size: 26pt;
            font-weight: bold;
            letter-spacing: 5px;
            color: #fff;
            line-height: 1;
        }
        .cert-h2 {
            font-size: 8.5pt;
            letter-spacing: 2.5px;
            color: rgba(255,255,255,0.85);
            margin-top: 1mm;
        }
        .cert-h3 {
            font-size: 6pt;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.7);
            margin-top: 2mm;
        }

        /* Oluvchi ismi */
        .recipient-name {
            font-size: 17pt;
            font-weight: bold;
            color: #1a1a2e;
            text-align: center;
            letter-spacing: 0.5px;
            line-height: 1.2;
            margin: 3mm 0 1mm;
        }
        .name-underline {
            height: 2px;
            margin: 1mm auto 3mm;
        }

        /* Maqola sarlavhasi */
        .paper-label {
            font-size: 6pt;
            letter-spacing: 1px;
            color: #aaa;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 1.5mm;
        }
        .paper-title {
            font-size: 9pt;
            font-weight: bold;
            font-style: italic;
            color: #222;
            line-height: 1.35;
            text-align: center;
            margin-bottom: 3mm;
        }

        /* Konferensiya matn */
        .conf-text {
            font-size: 7pt;
            color: #666;
            line-height: 1.55;
            text-align: center;
            margin-bottom: 3mm;
        }

        /* Pastki footer */
        .footer-bar {
            border-top: 1px solid #eee;
            padding-top: 2mm;
            margin-top: 1mm;
        }

        /* Footer 3 ustun (float) */
        .foot-left {
            float: left;
            width: 32%;
        }
        .foot-mid {
            float: left;
            width: 36%;
            text-align: center;
        }
        .foot-right {
            float: left;
            width: 32%;
            text-align: right;
        }
        .foot-clear { clear: both; }

        .date-val {
            font-size: 7.5pt;
            font-weight: bold;
            color: #333;
        }
        .cert-num-txt {
            font-size: 5.5pt;
            color: #ccc;
            margin-top: 1mm;
            letter-spacing: 0.5px;
        }
        .seal-img {
            width: 34px;
            height: 34px;
        }
        .sig-img {
            height: 20px;
            display: block;
            margin: 0 0 1mm auto;
        }
        .sig-rule {
            width: 50mm;
            height: 1px;
            margin: 0 0 1mm auto;
            display: block;
        }
        .sig-title {
            font-size: 6pt;
            font-weight: bold;
            color: #555;
        }
        .sig-name {
            font-size: 6pt;
            color: #c0392b;
        }

        /* Pastki rang tasma */
        .right-bottom-bar {
            height: 4px;
            width: 100%;
            clear: both;
            margin-top: 2mm;
        }
    </style>
</head>
<body>
@php
    $primary   = $colors['primary']   ?? '#1a5276';
    $secondary = $colors['secondary'] ?? '#2980b9';
    $accent    = $colors['accent']    ?? '#c9a227';

    $countryCode = strtoupper($country->code ?? 'GB');
    $authorName  = $article->author_name ?? 'Author Name';
    $articleTitle = $article->title ?? 'Article Title';
    $certNumber  = $certificate->certificate_number ?? 'CERT-000000';
    $issueDate   = ($certificate->issue_date instanceof \Carbon\Carbon)
        ? $certificate->issue_date->format('d/m/Y')
        : (is_string($certificate->issue_date) ? $certificate->issue_date : date('d/m/Y'));

    $bgImage   = $assets['background']    ?? '';
    $openAccess = $assets['open_access']  ?? '';
    $signature  = $assets['signature']    ?? '';

    $countryName = $country->name_en ?? $country->name ?? '';
    $confTitle   = $conference->title ?? 'International Scientific Conference';

    // Muallif ismini 2 qatorga
    $nameParts = explode(' ', mb_strtoupper(trim($authorName)));
    $half = (int)ceil(count($nameParts) / 2);
    $nameLine1 = implode(' ', array_slice($nameParts, 0, $half));
    $nameLine2 = implode(' ', array_slice($nameParts, $half));

    // Sarlavhani qisqartirish
    $shortTitle = mb_strlen($articleTitle) > 130
        ? mb_substr($articleTitle, 0, 127) . '...'
        : $articleTitle;

    // Logo base64
    $logoPath = public_path('images/isc-globe.png');
    if (!file_exists($logoPath)) $logoPath = public_path('images/logo.png');
    $logoB64 = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : '';

    // Bayroq base64
    $flagExts = ['png', 'jpg', 'jpeg'];
    $flagB64 = '';
    foreach ($flagExts as $ext) {
        $fp = public_path("images/flags/{$countryCode}.{$ext}");
        if (file_exists($fp)) {
            $flagB64 = "data:image/{$ext};base64," . base64_encode(file_get_contents($fp));
            break;
        }
    }

    // Davlat kodiga qarab chap panel ustunlarining ranglari
    // DomPDF gradientni qo'llab-quvvatlamaydi, shuning uchun solid ranglar ishlatiladi
    // 3 xil blok: primary + secondary + accent
    $panelColors = [
        'UZ' => [$primary,   $secondary, $accent],
        'GB' => ['#012169',  '#c8102e',  '#ffffff'],
        'RU' => ['#0039a6',  '#ffffff',  '#d52b1e'],
        'DE' => ['#000000',  '#dd0000',  '#ffcc00'],
        'FR' => ['#0055a4',  '#ffffff',  '#ef4135'],
        'TR' => ['#e30a17',  '#ffffff',  '#e30a17'],
        'KZ' => ['#00afca',  '#006994',  '#ffc61e'],
        'US' => ['#3c3b6e',  '#b22234',  '#ffffff'],
        'CN' => ['#de2910',  '#8b0000',  '#ffde00'],
        'JP' => ['#bc002d',  '#1a1a2e',  '#bc002d'],
        'IN' => ['#ff9933',  '#138808',  '#000080'],
        'KR' => ['#0047a0',  '#cd2e3a',  '#0047a0'],
        'AZ' => ['#0092bc',  '#e4002b',  '#00af66'],
        'TJ' => ['#cc0000',  '#006600',  '#ffffff'],
        'KG' => ['#e8112d',  '#fecc00',  '#e8112d'],
        'TM' => ['#00843d',  '#d22630',  '#00843d'],
    ];

    [$pc1, $pc2, $pc3] = $panelColors[$countryCode] ?? [$primary, $secondary, $accent];

    // Chap panel asosiy foniga primary rang
    $leftBg = $primary;

    // Matn kontrast (oq yoki qora)
    $r = hexdec(substr(ltrim($leftBg, '#'), 0, 2));
    $g = hexdec(substr(ltrim($leftBg, '#'), 2, 2));
    $b = hexdec(substr(ltrim($leftBg, '#'), 4, 2));
    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
    $txtColor = $brightness > 150 ? '#1a1a1a' : '#ffffff';
@endphp

<div class="cert-page">
<table class="cert-table" cellpadding="0" cellspacing="0">
<tr>

    <!-- ========== LEFT CELL ========== -->
    <td class="cell-left" style="background-color: {{ $pc1 }}; border-right-color: {{ $accent }};">

        <!-- Yuqori rang stripe -->
        <div class="stripe-top" style="background-color: {{ $pc2 }};"></div>

        <!-- Logo va sayt nomi -->
        <div class="left-logo-area">
            @if($logoB64)
            <img src="{{ $logoB64 }}" alt="Logo">
            @endif
            <span class="left-logo-text" style="color: {{ $txtColor }};">ARTIQLE.UZ</span>
        </div>

        <!-- Markaziy bo'lim -->
        <div class="left-body">
            <div style="height: 1px; background-color: rgba(255,255,255,0.3); margin: 4mm 0;"></div>

            <!-- Rang tasmalari (bayroq simulyatsiyasi) -->
            <table width="80%" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                <tr>
                    <td style="height:6px; background-color: {{ $pc1 }};"></td>
                </tr>
                <tr>
                    <td style="height:6px; background-color: {{ $pc2 != '#ffffff' ? $pc2 : 'rgba(255,255,255,0.3)' }};"></td>
                </tr>
                <tr>
                    <td style="height:6px; background-color: {{ $pc3 }};"></td>
                </tr>
            </table>

            <!-- Bayroq rasmi (agar mavjud) -->
            @if($flagB64)
            <br>
            <img class="flag-img" src="{{ $flagB64 }}" alt="{{ $countryName }}">
            @endif

            <div class="country-name" style="color: {{ $txtColor }};">
                {{ mb_strtoupper($countryName) }}
            </div>

            <div style="height: 1px; background-color: rgba(255,255,255,0.3); margin: 5mm 0;"></div>

            <div style="font-size:6pt; color: {{ $txtColor }}; opacity: 0.7; letter-spacing: 1px;">
                INTERNATIONAL<br>SCIENTIFIC<br>CONFERENCES
            </div>
        </div>

        <!-- Pastki rang stripe -->
        <div class="stripe-bottom" style="background-color: {{ $pc3 }};"></div>

    </td>
    <!-- ========== END LEFT CELL ========== -->


    <!-- ========== RIGHT CELL ========== -->
    <td class="cell-right">

        <!-- Yuqori rang tasma -->
        <div class="right-top-bar" style="background-color: {{ $primary }};"></div>

        <div class="right-content">

            <!-- CERTIFICATE sarlavhasi -->
            <div class="cert-header" style="background-color: {{ $primary }};">
                <div class="cert-h1">CERTIFICATE</div>
                <div class="cert-h2">OF APPRECIATION</div>
                <div class="cert-h3">THIS CERTIFICATE IS AWARDED TO</div>
                <!-- Chap aksent barchiq -->
                <div class="cert-header-accent" style="background-color: {{ $accent }};"></div>
            </div>

            <!-- Oluvchi ismi -->
            <div class="recipient-name">
                {{ $nameLine1 }}@if($nameLine2 && $nameLine2 !== $nameLine1)<br>{{ $nameLine2 }}@endif
            </div>
            <div class="name-underline" style="background-color: {{ $primary }}; width: 70mm;"></div>

            <!-- Maqola sarlavhasi -->
            <div class="paper-label">FOR PARTICIPATION AND PUBLICATION OF THE PAPER ENTITLED</div>
            <div class="paper-title">{{ strtoupper($shortTitle) }}</div>

            <!-- Konferensiya tavsif -->
            <div class="conf-text">
                In an International Conference on <b>{{ $confTitle }}</b>,
                Published online with <b>International conferences of practice</b> publications,
                Hosted online from <b>{{ $countryName }}</b>
            </div>

            <!-- Footer -->
            <div class="footer-bar">
                <div class="foot-left">
                    <div class="date-val">Date: {{ $issueDate }}</div>
                    <div class="cert-num-txt">№ {{ $certNumber }}</div>
                </div>
                <div class="foot-mid">
                    @if($openAccess)
                    <img class="seal-img" src="{{ $openAccess }}" alt="Open Access">
                    @else
                    <div style="width:34px;height:34px;border-radius:17px;border:2px solid {{ $accent }};
                         margin:0 auto;padding-top:8px;text-align:center;">
                        <span style="font-size:4.5pt;color:{{ $accent }};font-weight:bold;line-height:1.3;">OPEN<br>ACCESS</span>
                    </div>
                    @endif
                </div>
                <div class="foot-right">
                    @if($signature)
                    <img class="sig-img" src="{{ $signature }}" alt="Signature">
                    @endif
                    <span class="sig-rule" style="background-color: {{ $primary }};"></span>
                    <div class="sig-title">Chief editor</div>
                    <div class="sig-name">Sven Behnke</div>
                </div>
                <div class="foot-clear"></div>
            </div>

        </div>

        <!-- Pastki rang tasma -->
        <div class="right-bottom-bar" style="background-color: {{ $secondary }};"></div>

    </td>
    <!-- ========== END RIGHT CELL ========== -->

</tr>
</table>
</div>
</body>
</html>
