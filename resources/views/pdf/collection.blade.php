<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $conference->title }} - Maqolalar to'plami</title>
    <style>
        /* ─── Global ─────────────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }

        /* ─────────────────────────────────────────────────────────── */
        /* BET 1: COVER PAGE                                          */
        /* ─────────────────────────────────────────────────────────── */
        @page cover {
            margin: 0;
            size: A4 portrait;
        }

        .cover-page {
            width: 210mm;
            height: 297mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
        }

        /* Full-page background image */
        .cover-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 75%;
            z-index: 1;
        }

        .cover-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Gradient overlay for blending image into white */
        .cover-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 35%;
            background: linear-gradient(180deg,
                rgba(255,255,255,0) 0%,
                rgba(255,255,255,0.7) 50%,
                rgba(255,255,255,1) 85%,
                rgba(255,255,255,1) 100%);
            z-index: 2;
        }

        /* ICP Banner at top right */
        .icp-banner {
            position: absolute;
            top: -5px;
            right: 35px;
            width: 85px;
            background: #b22222;
            z-index: 10;
            text-align: center;
            padding: 15px 5px 25px;
        }

        .icp-banner-logo {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #fff;
            margin: 0 auto 6px;
            display: table;
        }

        .icp-banner-logo-inner {
            display: table-cell;
            vertical-align: middle;
            color: #004b87;
            font-weight: 900;
            font-size: 22px;
            letter-spacing: -1px;
        }

        .icp-banner-text {
            color: #fff;
            font-size: 6px;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }

        .icp-banner-triangle {
            position: absolute;
            bottom: -25px;
            left: 0;
            width: 0;
            height: 0;
            border-left: 42.5px solid #b22222;
            border-right: 42.5px solid #b22222;
            border-bottom: 25px solid transparent;
        }

        /* Content area below the image */
        .cover-content {
            position: absolute;
            top: 65%;
            left: 0;
            width: 100%;
            padding: 0 40px;
            z-index: 10;
        }

        .cover-country-name {
            text-align: right;
            color: #d61111;
            font-size: 32px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .cover-red-line {
            border-bottom: 3.5px solid #d61111;
            width: 100%;
            margin-bottom: 25px;
        }

        .cover-conference-title {
            text-align: center;
            color: #002d62;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.4;
            margin-bottom: 12px;
            padding: 0 15px;
        }

        .cover-conference-subtitle {
            text-align: center;
            color: #002d62;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Bottom Row table */
        .cover-footer {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            padding: 0 40px;
            z-index: 10;
        }

        .cover-footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cover-footer-table td {
            vertical-align: middle;
            width: 33.33%;
        }

        .cover-url {
            font-size: 12px;
            font-weight: 900;
            color: #002d62;
            text-transform: uppercase;
        }

        .cover-flag-td {
            text-align: center;
        }

        .cover-flag {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .cover-qr-td {
            text-align: right;
        }

        .cover-qr-td img {
            width: 65px;
            height: 65px;
        }

        /* ─────────────────────────────────────────────────────────── */
        /* BET 2: GENERAL INFO PAGE                                   */
        /* ─────────────────────────────────────────────────────────── */
        @page info {
            margin: 20mm;
            size: A4 portrait;
        }

        .info-page {
            position: relative;
        }


        /* TOC */
        .toc-section-title {
            font-size: 14px;
            font-weight: 800;
            color: #002d62;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            padding-bottom: 6px;
            border-bottom: 2px solid #c9a227;
        }

        .toc-table {
            width: 100%;
            border-collapse: collapse;
        }

        .toc-table thead tr {
            background: #002d62;
            color: #fff;
        }

        .toc-table thead th {
            padding: 7px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .toc-table tbody tr {
            border-bottom: 1px solid #eee;
        }

        .toc-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .toc-table tbody td {
            padding: 7px 10px;
            font-size: 10px;
            vertical-align: top;
        }

        .toc-num {
            width: 28px;
            font-weight: 700;
            color: #c9a227;
            text-align: center;
        }

        .toc-title-col { color: #111; line-height: 1.35; font-weight: bold; }

        .toc-author-col {
            width: 35%;
            color: #555;
            font-style: italic;
            font-size: 9px;
        }

        .toc-pages-col {
            width: 45px;
            text-align: right;
            color: #002d62;
            font-weight: 700;
            font-size: 9.5px;
            white-space: nowrap;
        }

        /* ─────────────────────────────────────────────────────────── */
        /* BET 3+: ARTICLES                                           */
        /* ─────────────────────────────────────────────────────────── */
        @page article {
            margin: 15mm 15mm 15mm 28mm;
            size: A4 portrait;
        }

        .article-page {
            /* Each article starts fresh */
        }

        /* Left sidebar for articles */
        .article-sidebar {
            position: fixed;
            left: -26mm;
            top: 0;
            bottom: 0;
            width: 26mm;
            background: #fff;
            border-right: 1px solid #f0f0f0;
            z-index: -1;
        }

        .sidebar-top-bar {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4mm;
            background: {{ $colors['primary'] ?? '#1a3a5f' }};
        }

        .sidebar-bottom-bar {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 4mm;
            background: {{ $colors['accent'] ?? '#c9a227' }};
        }

        .sidebar-brand {
            position: absolute;
            bottom: 45mm;
            left: 11mm;
            width: 160mm;
            white-space: nowrap;
            transform: rotate(-90deg);
            transform-origin: left bottom;
            font-size: 22pt;
            font-weight: 700;
            color: {{ $colors['primary'] ?? '#1a3a5f' }};
            text-transform: uppercase;
            letter-spacing: 4px;
            opacity: 0.18;
        }

        /* Article header */
        .article-header {
            text-align: center;
            margin-bottom: 7mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid {{ $colors['primary'] ?? '#1a3a5f' }};
        }

        .article-conf-name {
            font-size: 11pt;
            font-weight: 700;
            color: {{ $colors['primary'] ?? '#1a3a5f' }};
            text-transform: uppercase;
            margin-bottom: 1mm;
        }

        .article-conf-date {
            font-size: 9pt;
            color: #e74c3c;
            font-weight: 700;
        }

        /* Article body */
        .article-title {
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            margin-bottom: 3mm;
            line-height: 1.3;
            color: #000;
        }

        .article-author {
            text-align: center;
            font-size: 11pt;
            font-weight: 700;
            color: {{ $colors['secondary'] ?? '#2980b9' }};
            margin-bottom: 1mm;
        }

        .article-affiliation {
            text-align: center;
            font-size: 9.5pt;
            font-style: italic;
            color: #888;
            margin-bottom: 7mm;
        }

        .abstract-block {
            font-size: 9.5pt;
            background: #f8f9fa;
            border-left: 4px solid {{ $colors['accent'] ?? '#c9a227' }};
            padding: 4mm 5mm;
            margin-bottom: 7mm;
            text-align: justify;
        }

        .abstract-label {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8.5pt;
            color: {{ $colors['primary'] ?? '#1a3a5f' }};
            display: block;
            margin-bottom: 1mm;
        }

        .article-content {
            font-size: 10.5pt;
            text-align: justify;
            line-height: 1.55;
        }

        .article-content p {
            margin-bottom: 4mm;
            text-indent: 10mm;
            page-break-inside: avoid;
        }

        .article-content p:first-child {
            text-indent: 0;
        }

        .article-content h1,
        .article-content h2,
        .article-content h3 {
            color: {{ $colors['primary'] ?? '#1a3a5f' }};
            margin-top: 5mm;
            margin-bottom: 3mm;
            page-break-after: avoid;
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            margin: 5mm auto;
            display: block;
        }

        .article-references {
            margin-top: 8mm;
            padding-top: 5mm;
            border-top: 1px dashed #ccc;
            font-size: 9pt;
            color: #555;
        }

        /* Article page footer */
        .article-footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 2mm;
        }
    </style>
</head>

<body>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- BET 1: COVER PAGE (Rasm yo'q — sof CSS, hajm minimal)        --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="cover-page">

    @php
        // Davlat ranglari
        $primary   = $colors['primary']   ?? '#002d62';
        $secondary = $colors['secondary'] ?? '#d61111';
        $accent    = $colors['accent']    ?? '#c9a227';
    @endphp

    {{-- Yuqori rangli blok (cover rasmi o'rniga) --}}
    <div style="
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 68%;
        background: {{ $primary }};
        z-index: 1;
    "></div>

    {{-- Accent diagonal strip --}}
    <div style="
        position: absolute;
        top: 60%;
        left: 0; right: 0;
        height: 20mm;
        background: {{ $secondary }};
        z-index: 2;
    "></div>

    {{-- ISC Logo/Monogram (yuqori o'ng) --}}
    <div style="
        position: absolute;
        top: 20px; right: 30px;
        width: 70px; height: 70px;
        border-radius: 50%;
        background: #ffffff;
        z-index: 10;
        text-align: center;
        line-height: 70px;
        font-size: 18px;
        font-weight: 900;
        color: {{ $primary }};
    ">ISC</div>

    {{-- Davlat nomi --}}
    <div style="
        position: absolute;
        top: 55%;
        left: 0; right: 0;
        z-index: 10;
        text-align: right;
        padding: 0 40px;
        color: #ffffff;
        font-size: 34px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
    ">{{ strtoupper($country->name_en ?? $country->name) }}</div>

    {{-- Ajratuvchi chiziq --}}
    <div style="
        position: absolute;
        top: 70%;
        left: 40px; right: 40px;
        height: 3px;
        background: {{ $accent }};
        z-index: 10;
    "></div>

    {{-- Konferensiya sarlavhasi --}}
    <div style="
        position: absolute;
        top: 72%;
        left: 0; right: 0;
        z-index: 10;
        text-align: center;
        padding: 0 40px;
        color: {{ $primary }};
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        line-height: 1.4;
    ">{{ $conference->title }}</div>

    <div style="
        position: absolute;
        top: 82%;
        left: 0; right: 0;
        z-index: 10;
        text-align: center;
        color: #666;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    ">INTERNATIONAL ONLINE CONFERENCE</div>

    {{-- Footer: sayt manzili --}}
    <div style="
        position: absolute;
        bottom: 20px;
        left: 40px; right: 40px;
        z-index: 10;
        text-align: center;
        color: {{ $primary }};
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        border-top: 1px solid {{ $accent }};
        padding-top: 8px;
    ">WWW.INTERNATIONALSCIENTIFICCONFERENCES.ORG</div>

</div>

<div class="page-break"></div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- BET 2: GENERAL INFO PAGE                                     --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="info-page" style="padding: 20mm;">

    <div style="text-align: center;">
        <div style="color: red; font-family: 'Times New Roman', Times, serif; font-weight: bold; font-size: 14px; margin-bottom: 10px;">
            Date: {{ $conference->conference_date->format('jS F-Y') }}
        </div>

        <div style="font-family: 'Times New Roman', Times, serif; font-weight: bold; font-size: 13px; line-height: 1.4; margin-bottom: 25px;">
            “{{ $conference->title }}”. Collection of
            scientific papers on materials of the international scientific-practical conference
            {{ $conference->conference_date->format('d.m.Y') }}, Pub. "ISOC", {{ strtoupper($country->name_en ?? $country->name) }}, {{ $country->name_en ?? $country->name }}, {{ $articles->count() * 4 }} p.
        </div>

        <div style="font-family: 'Times New Roman', Times, serif; font-weight: bold; font-size: 14px; margin-bottom: 20px;">
            Editor:
        </div>

        <div style="font-family: 'Times New Roman', Times, serif; font-size: 13px; line-height: 1.3; margin-bottom: 15px;">
            <strong>Sven Behnke</strong><br>
            University of Bonn<br>
            Bonn, Germany<br>
            Email: <a href="mailto:behnkesven@cs.uni-bonn.de" style="color: blue; text-decoration: underline;">behnkesven@cs.uni-bonn.de</a>
        </div>

        <div style="font-family: 'Times New Roman', Times, serif; font-size: 13px; line-height: 1.3; margin-bottom: 15px;">
            <strong>Erdal Kayacan</strong><br>
            Current ISOC Editors Paderborn University<br>
            Paderborn, Germany<br>
            Email: <a href="mailto:erdal.kayacan@unit-paderborn.de" style="color: blue; text-decoration: underline;">erdal.kayacan@unit-paderborn.de</a>
        </div>

        <div style="font-family: 'Times New Roman', Times, serif; font-size: 13px; line-height: 1.3; margin-bottom: 30px;">
            <strong>Stefan Leutenegger</strong><br>
            Current ISOC Editors<br>
            Technical University of Munich<br>
            Munich, Germany<br>
            Email: <a href="mailto:stefan.leutenegger@tum.de" style="color: blue; text-decoration: underline;">stefan.leutenegger@tum.de</a>
        </div>
    </div>

    <div style="font-family: 'Times New Roman', Times, serif; text-align: justify; text-indent: 20px; font-size: 13px; line-height: 1.4; margin-bottom: 20px;">
        The collection of published scientific papers is a scientific and practical publication,
        which includes scientific articles from students, teachers, candidates of sciences, doctoral
        students, and independent researchers. The articles contain a study that reflects the
        processes and changes in the structure of modern science. The collection of scientific
        articles is intended for students, doctoral students, teachers, researchers, practitioners, and
        those interested in the development trends of modern science.
    </div>

    <div style="font-family: 'Times New Roman', Times, serif; text-align: justify; text-indent: 20px; font-weight: bold; font-size: 13px; line-height: 1.4; margin-bottom: 25px;">
        All materials contained in the book, published in the author's version. The
        editors do not make adjustments in scientific articles. Responsibility for the
        information published in the materials on display, are the authors.
    </div>

    <div style="font-family: 'Times New Roman', Times, serif; text-align: left; font-size: 13px; line-height: 1.4;">
        The electronic version of the collection is available online scientific publishing center<br>
        «ISOC» Site center: internationalscientificconferences.org
    </div>

</div>

<div class="page-break"></div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- BET 3+: MAQOLALAR                                            --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@foreach($articles as $i => $article)

<div class="article-page">

    {{-- Left sidebar --}}
    <div class="article-sidebar">
        <div class="sidebar-top-bar"></div>
        <div class="sidebar-brand">International Scientific Conferences</div>
        <div class="sidebar-bottom-bar"></div>
    </div>

    {{-- Article footer with page number --}}
    <div class="article-footer">
        {{ $conference->title }} | {{ $country->name_en ?? $country->name }} | {{ $conference->conference_date->format('Y') }}
        &nbsp;&nbsp;•&nbsp;&nbsp;
        www.internationalscientificconferences.org
    </div>

    {{-- Article header --}}
    <div class="article-header">
        <div class="article-conf-name">{{ $country->conference_name ?? 'INTERNATIONAL SCIENTIFIC CONFERENCES' }}</div>
        <div class="article-conf-date">
            {{ $conference->conference_date->format('jS F Y') }}
            &nbsp;|&nbsp; {{ $country->name_en ?? $country->name }}
        </div>
    </div>

    {{-- Article content --}}
    @php
        // DOCX dan konvertatsiya qilingan PDF bor bo'lsa — uni ko'rsatamiz
        $hasFormattedPdf = !empty($article->formatted_pdf_path)
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($article->formatted_pdf_path);
    @endphp

    <div class="article-title">{{ $article->title }}</div>

    <div class="article-author">
        {{ $article->author_name ?? $article->author_display_name }}
        @if($article->co_authors)
            , {{ $article->co_authors }}
        @endif
    </div>

    @if($article->author_affiliation)
        <div class="article-affiliation">{{ $article->author_affiliation }}</div>
    @endif

    @if($article->abstract)
        <div class="abstract-block">
            <span class="abstract-label">Abstract</span>
            {{ $article->abstract }}
            @if($article->keywords)
                <br><br>
                <strong>Keywords:</strong> {{ $article->keywords }}
            @endif
        </div>
    @endif

    @php
        $content = $article->content ?? '';
        if ($content) {
            // 1. Base64 rasmlarni olib tashlash (PDF hajmini keskin kamaytiradi)
            $content = preg_replace('/<img[^>]+src=["\']data:[^"\'>]+["\'][^>]*>/i', '', $content);
            // 2. <style> va <script> bloklari olib tashlash
            $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
            $content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);
            // 3. Tashqi rasm URL larini ham olib tashlash (remote images = slow + big)
            $content = preg_replace('/<img[^>]+>/i', '', $content);
            // 4. Inline style lar tufayli chiqadigan keraksiz bo'sh joylarni tozalash
            $content = preg_replace('/style="[^"]*"/i', '', $content);

            if (strpos($content, '<p') !== false) {
                // HTML paragraph lar bor — shu tartibda ishlatamiz
                $contentHtml = $content;
            } else {
                // Oddiy matn
                $content = str_replace(["\r\n", "\r"], "\n", $content);
                $content = preg_replace('/\n\s*\n/', '{{PARA}}', $content);
                $content = str_replace("\n", ' ', $content);
                $content = preg_replace('/ {2,}/', ' ', $content);
                $paragraphs = explode('{{PARA}}', $content);
                $contentHtml = '';
                foreach ($paragraphs as $p) {
                    $p = trim($p);
                    if (!empty($p)) {
                        $contentHtml .= '<p>' . e($p) . '</p>';
                    }
                }
            }
        } else {
            $contentHtml = '<p style="color:#aaa; font-style:italic;">[Maqola matni topilmadi]</p>';
        }
    @endphp

    <div class="article-content">
        {!! $contentHtml !!}
    </div>

    @if($article->references)
        <div class="article-references">
            <strong>FOYDALANILGAN ADABIYOTLAR:</strong><br>
            <div style="margin-top: 2mm;">
                {!! nl2br(e($article->references)) !!}
            </div>
        </div>
    @endif

</div>

{{-- Har bir maqoladan keyin yangi sahifa (oxirgisidan tashqari) --}}
@if(!$loop->last)
    <div class="page-break"></div>
@endif

@endforeach

<div class="page-break"></div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- OXIRGI BET: MUNDARIJA (TABLE OF CONTENTS)                      --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="toc-page" style="padding: 20mm;">
    <div class="toc-section-title">TABLE OF CONTENTS</div>
    <div style="text-align: center; color: red; font-family: 'Times New Roman', Times, serif; font-weight: bold; margin-bottom: 5px; font-size: 14px;">
        Date: {{ $conference->conference_date->format('jS F-Y') }}
    </div>

    <table style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', Times, serif; font-size: 12px;">
        <thead>
            <tr>
                <th colspan="2" style="background-color: #a394c6; color: #041E4F; text-align: center; padding: 4px; font-weight: bold; border: 1px solid #7eaac8; font-size: 14px; text-transform: uppercase;">
                    ARTICLES:
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $i => $article)
            <tr>
                <td style="text-align: center; border: 1px solid #7eaac8; padding: 6px; color: #000;">
                    <div style="font-weight: bold;">
                        {{ $article->author_name ?? $article->author_display_name }}
                        @if($article->co_authors)
                            <br>{!! nl2br(e($article->co_authors)) !!}
                        @endif
                    </div>
                    <div style="text-transform: uppercase; margin-top: 3px;">
                        {{ $article->title }}
                    </div>
                </td>
                <td style="text-align: center; vertical-align: top; border: 1px solid #7eaac8; width: 40px; font-weight: bold; padding-top: 6px; color: #000; font-size: 13px;">
                    {{ $article->page_range ?? '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>