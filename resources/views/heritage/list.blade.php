<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>里昂文化遺產地點</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }
        .breadcrumb {
            margin-bottom: 20px;
            color: #666;
        }
        .breadcrumb a {
            color: #0066cc;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .content-section {
            margin-bottom: 30px;
        }
        .content-section h2 {
            color: #444;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .item-list {
            list-style: none;
        }
        .item {
            padding: 15px;
            margin-bottom: 10px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .item:hover {
            background: #f0f0f0;
        }
        .item a {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .item a:hover {
            text-decoration: underline;
        }
        .item .summary {
            color: #666;
            font-size: 0.9em;
        }
        .item .meta {
            color: #999;
            font-size: 0.85em;
            margin-top: 5px;
        }
        .directory-icon::before {
            content: "📁 ";
        }
        .file-icon::before {
            content: "📄 ";
        }
        .no-items {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>里昂文化遺產地點</h1>

    @if($currentPath)
        <div class="breadcrumb">
            <a href="/">首頁</a> /
            @php
                $parts = explode('/', $currentPath);
                $accumulated = '';
            @endphp
            @foreach($parts as $index => $part)
                @php $accumulated .= ($index > 0 ? '/' : '') . $part; @endphp
                @if($index < count($parts) - 1)
                    <a href="/heritages/{{ $accumulated }}">{{ $part }}</a> /
                @else
                    {{ $part }}
                @endif
            @endforeach
        </div>
    @endif

    @if(count($directories) > 0)
        <div class="content-section">
            <h2>資料夾</h2>
            <ul class="item-list">
                @foreach($directories as $dir)
                    <li class="item">
                        <a href="/heritages/{{ $dir['path'] }}" class="directory-icon">
                            {{ $dir['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($files) > 0)
        <div class="content-section">
            <h2>文章</h2>
            <ul class="item-list">
                @foreach($files as $file)
                    <li class="item">
                        <a href="/heritages/{{ $file['path'] }}" class="file-icon">
                            {{ $file['title'] }}
                        </a>
                        @if($file['summary'])
                            <div class="summary">{{ $file['summary'] }}</div>
                        @endif
                        <div class="meta">
                            @if(isset($file['date']))
                                日期: {{ $file['date'] }}
                            @endif
                            @if(!empty($file['tags']))
                                | 標籤:
                                @foreach($file['tags'] as $tag)
                                    <a href="/tags/{{ $tag }}">{{ $tag }}</a>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($directories) == 0 && count($files) == 0)
        <p class="no-items">此資料夾沒有內容</p>
    @endif
</body>
</html>