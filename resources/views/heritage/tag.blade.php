<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>標籤: {{ $tag }} - 里昂文化遺產</title>
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
        .tag-label {
            background: #0066cc;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .results {
            margin-top: 30px;
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
        .no-results {
            color: #999;
            font-style: italic;
            margin-top: 30px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #0066cc;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="back-link">← 返回首頁</a>

    <h1>標籤: <span class="tag-label">{{ $tag }}</span></h1>

    @if(count($files) > 0)
        <div class="results">
            <p>找到 {{ count($files) }} 篇相關文章</p>
            <ul class="item-list">
                @foreach($files as $file)
                    <li class="item">
                        <a href="{{ url('/heritages/' . $file['path']) }}">
                            {{ $file['title'] }}
                        </a>
                        @if($file['summary'])
                            <div class="summary">{{ $file['summary'] }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="no-results">沒有找到標籤為 "{{ $tag }}" 的文章</p>
    @endif
</body>
</html>