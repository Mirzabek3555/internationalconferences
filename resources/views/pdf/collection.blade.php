<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $conference->title }} - Maqolalar to'plami</title>
    <style>
        @page {
            margin: 30px 40px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1e3a5f;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .title {
            font-size: 20px;
            margin: 15px 0;
            color: #1e3a5f;
        }

        .meta {
            color: #666;
            font-size: 14px;
        }

        .toc {
            margin: 30px 0;
        }

        .toc-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #1e3a5f;
        }

        .toc-item {
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
            display: flex;
            justify-content: space-between;
        }

        .toc-number {
            color: #1e3a5f;
            font-weight: bold;
            width: 30px;
        }

        .toc-article {
            flex: 1;
        }

        .toc-author {
            color: #666;
            font-style: italic;
        }

        .toc-pages {
            width: 60px;
            text-align: right;
            color: #1e3a5f;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">🎓 ARTIQLE</div>
        <div class="title">{{ $conference->title }}</div>
        <div class="meta">
            {{ $country->name }} ({{ $country->name_en }}) | {{ $conference->conference_date->format('F Y') }}
        </div>
    </div>

    <div class="toc">
        <div class="toc-title">📋 MUNDARIJA</div>

        @foreach($articles as $article)
            <div class="toc-item">
                <span class="toc-number">{{ $article->order_number }}.</span>
                <span class="toc-article">
                    {{ $article->title }}
                    <span class="toc-author"> — {{ $article->author_display_name }}</span>
                </span>
                <span class="toc-pages">{{ $article->page_range }}</span>
            </div>
        @endforeach
    </div>

    <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
        <p><strong>Jami maqolalar:</strong> {{ $articles->count() }}</p>
        <p><strong>Konferensiya sanasi:</strong> {{ $conference->conference_date->format('d.m.Y') }}</p>
        <p><strong>To'plam yaratilgan sana:</strong> {{ now()->format('d.m.Y H:i') }}</p>
    </div>

    <div class="footer">
        Artiqle - Ilmiy Maqolalar Platformasi | © {{ date('Y') }}
    </div>
</body>

</html>