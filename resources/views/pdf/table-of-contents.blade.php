<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $conference->title }} - Table of Contents</title>
    <style>
        @page {
            margin: 25mm 20mm;
            size: A4 portrait;
        }

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
            background: #fff;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #1a3a5f;
            margin-bottom: 25px;
        }

        .header-top {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }

        .logo-img {
            height: 50px;
            width: auto;
        }

        .header-title {
            font-size: 14px;
            color: #c41e3a;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 10px;
            color: #666;
            letter-spacing: 1px;
        }

        .conference-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a3a5f;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .conference-subtitle {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .conference-meta {
            margin-top: 10px;
            padding: 10px 20px;
            background: #f5f5f5;
            border-radius: 5px;
            display: inline-block;
        }

        .meta-item {
            font-size: 10px;
            color: #666;
        }

        .meta-value {
            font-weight: 600;
            color: #1a3a5f;
        }

        /* Table of Contents */
        .toc-title {
            font-size: 14px;
            font-weight: 700;
            color: #1a3a5f;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #c9a227;
        }

        .toc-section {
            margin-bottom: 10px;
        }

        .toc-section-title {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .toc-item {
            display: flex;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px dotted #ddd;
        }

        .toc-item:last-child {
            border-bottom: none;
        }

        .toc-number {
            width: 40px;
            flex-shrink: 0;
            font-weight: 700;
            color: #c9a227;
            font-size: 14px;
        }

        .toc-content {
            flex: 1;
            padding-right: 15px;
        }

        .toc-article-title {
            font-size: 11px;
            font-weight: 600;
            color: #1a3a5f;
            line-height: 1.4;
            margin-bottom: 3px;
        }

        .toc-author {
            font-size: 9px;
            color: #666;
            font-style: italic;
        }

        .toc-author-affiliation {
            font-size: 8px;
            color: #999;
        }

        .toc-pages {
            width: 50px;
            flex-shrink: 0;
            text-align: right;
            font-weight: 600;
            color: #1a3a5f;
            font-size: 10px;
        }

        /* Summary section */
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #1a3a5f 0%, #0d2137 100%);
            border-radius: 10px;
            color: #fff;
        }

        .summary-title {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #c9a227;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-grid {
            display: flex;
            justify-content: space-between;
        }

        .summary-item {
            text-align: center;
        }

        .summary-value {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
        }

        .summary-label {
            font-size: 9px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #999;
            padding: 10px;
            border-top: 1px solid #eee;
        }

        /* Chief editor section */
        .editor-section {
            margin-top: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #c9a227;
        }

        .editor-title {
            font-size: 10px;
            font-weight: 700;
            color: #1a3a5f;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .editor-info {
            font-size: 9px;
            color: #555;
            line-height: 1.6;
        }

        .editor-name {
            font-weight: 600;
            color: #1a3a5f;
        }

        /* Languages section */
        .languages-section {
            margin-top: 20px;
            padding: 10px 15px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }

        .languages-title {
            font-size: 9px;
            font-weight: 600;
            color: #1a3a5f;
            margin-bottom: 5px;
        }

        .languages-list {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-top">
            @if(file_exists(public_path('images/isoc_logo.png')))
                <img src="{{ public_path('images/isoc_logo.png') }}" class="logo-img" alt="ISOC Logo">
            @endif
        </div>
        <div class="header-title">{{ strtoupper($country->name_en) }} DEVELOPMENTS AND RESEARCH IN EDUCATION</div>
        <div class="header-subtitle">International scientific online conference</div>

        <div class="conference-title">{{ $conference->title }}</div>
        <div class="conference-subtitle">International scientific online conference</div>

        <div class="conference-meta">
            <span class="meta-item">Part <span class="meta-value">{{ $articles->count() }}</span></span>
            &nbsp;|&nbsp;
            <span class="meta-item">{{ $conference->conference_date->format('F Y') }}</span>
            &nbsp;|&nbsp;
            <span class="meta-item">Collection of Scientific Works</span>
        </div>
    </div>

    <div class="toc-title">📋 Table of Contents</div>

    <div class="toc-section">
        @foreach($articles as $index => $article)
            <div class="toc-item">
                <div class="toc-number">{{ $article->order_number }}.</div>
                <div class="toc-content">
                    <div class="toc-article-title">{{ $article->title }}</div>
                    <div class="toc-author">{{ $article->author_display_name }}</div>
                    @if($article->author_affiliation)
                        <div class="toc-author-affiliation">{{ $article->author_affiliation }}</div>
                    @endif
                </div>
                <div class="toc-pages">{{ $article->page_range }}</div>
            </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-title">📊 Summary</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $articles->count() }}</div>
                <div class="summary-label">Total Articles</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $conference->conference_date->format('d.m.Y') }}</div>
                <div class="summary-label">Conference Date</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ now()->format('d.m.Y') }}</div>
                <div class="summary-label">Collection Created</div>
            </div>
        </div>
    </div>

    <!-- Editor Section -->
    <div class="editor-section">
        <div class="editor-title">Chief Editor</div>
        <div class="editor-info">
            The collection contains of scientific research of scientists, graduate students and students who took part
            in the International Scientific online conference "{{ $conference->title }}", Which took place in
            {{ $country->name_en }} on {{ $conference->conference_date->format('F d, Y') }}.
        </div>
    </div>

    <!-- Languages -->
    <div class="languages-section">
        <div class="languages-title">Languages of publication process:</div>
        <div class="languages-list">English, Russian, Uzbek, Arabic, Latin, Spanish, Turkish, Tajik, Cyrillic...</div>
    </div>

    <div class="footer">
        ISOC - International Scientific Online Conference | © {{ date('Y') }} | www.isoc.uz
    </div>
</body>

</html>