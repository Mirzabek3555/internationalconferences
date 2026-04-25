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
            font-family: 'Times New Roman', Times, serif; 
            background: #fff;
        }

        .date {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .toc-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 14px; 
            page-break-inside: auto;
        }

        .toc-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .toc-header {
            background-color: #a394c6;
            color: #041E4F;
            text-align: center;
            font-weight: bold;
            border: 1px solid #7eaac8;
            font-size: 16px;
            padding: 5px;
            text-transform: uppercase;
        }

        .toc-cell {
            border: 1px solid #7eaac8;
            padding: 8px;
            text-align: center;
            color: #000;
        }

        .toc-page {
            border: 1px solid #7eaac8;
            padding: 8px;
            text-align: center;
            vertical-align: top;
            width: 50px;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="date">
        Date: {{ $conference->conference_date->format('jS F-Y') }}
    </div>
    
    <table class="toc-table">
        <thead>
            <tr>
                <th colspan="2" class="toc-header">
                    ARTICLES:
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr>
                <td class="toc-cell">
                    <div style="font-weight: bold;">
                        {{ $article->author_name ?? $article->author_display_name }}
                        @if($article->co_authors)
                            <br>{!! nl2br(e($article->co_authors)) !!}
                        @endif
                    </div>
                    <div style="text-transform: uppercase; margin-top: 5px;">
                        {{ $article->title }}
                    </div>
                </td>
                <td class="toc-page">
                    {{ $article->page_range ?? '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>