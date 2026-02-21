<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $certificate->certificate_number }}</title>
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
            background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certificate {
            width: 95%;
            height: 90%;
            background: white;
            border-radius: 20px;
            padding: 40px;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .border-decoration {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid #c9a227;
            border-radius: 15px;
        }

        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
        }

        .corner-tl {
            top: 10px;
            left: 10px;
            border-top: 4px solid #c9a227;
            border-left: 4px solid #c9a227;
        }

        .corner-tr {
            top: 10px;
            right: 10px;
            border-top: 4px solid #c9a227;
            border-right: 4px solid #c9a227;
        }

        .corner-bl {
            bottom: 10px;
            left: 10px;
            border-bottom: 4px solid #c9a227;
            border-left: 4px solid #c9a227;
        }

        .corner-br {
            bottom: 10px;
            right: 10px;
            border-bottom: 4px solid #c9a227;
            border-right: 4px solid #c9a227;
        }

        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 5px;
        }

        .title {
            font-size: 42px;
            font-weight: bold;
            color: #c9a227;
            margin: 20px 0;
            letter-spacing: 8px;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .recipient {
            font-size: 32px;
            font-weight: bold;
            color: #1e3a5f;
            margin: 20px 0;
            border-bottom: 2px solid #c9a227;
            display: inline-block;
            padding: 0 40px 10px;
        }

        .article-title {
            font-size: 18px;
            color: #333;
            margin: 20px 40px;
            font-style: italic;
        }

        .conference {
            font-size: 16px;
            color: #666;
            margin: 15px 0;
        }

        .footer {
            position: absolute;
            bottom: 50px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 80px;
        }

        .footer-item {
            text-align: center;
        }

        .footer-label {
            font-size: 12px;
            color: #999;
        }

        .footer-value {
            font-size: 14px;
            color: #333;
            margin-top: 5px;
        }

        .certificate-number {
            position: absolute;
            bottom: 30px;
            right: 80px;
            font-size: 12px;
            color: #999;
        }

        .flag {
            width: 80px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            margin: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="certificate">
        <div class="border-decoration"></div>
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="content">
            <div class="header">
                <div class="logo">🎓 ARTIQLE</div>
                <p style="color:#666;font-size:14px;">Ilmiy Maqolalar Platformasi</p>
            </div>

            <div class="title">SERTIFIKAT</div>
            <div class="subtitle">Bu sertifikat quyidagi shaxsga beriladi:</div>

            <div class="recipient">{{ $author->name }}</div>

            <div class="article-title">"{{ $article->title }}"</div>

            <div class="conference">
                <strong>{{ $conference->title }}</strong><br>
                {{ $country->name }} ({{ $country->name_en }})
            </div>

            <div class="footer">
                <div class="footer-item">
                    <div class="footer-label">Konferensiya sanasi</div>
                    <div class="footer-value">{{ $conference->conference_date->format('d.m.Y') }}</div>
                </div>
                <div class="footer-item">
                    <div class="footer-label">Sahifalar</div>
                    <div class="footer-value">{{ $article->page_range }}</div>
                </div>
                <div class="footer-item">
                    <div class="footer-label">Berilgan sana</div>
                    <div class="footer-value">{{ $certificate->issue_date->format('d.m.Y') }}</div>
                </div>
            </div>

            <div class="certificate-number">№ {{ $certificate->certificate_number }}</div>
        </div>
    </div>
</body>

</html>