<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>搜尋結果 - 里昂文化遺產</title>
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
        .search-form {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .search-form input[type="text"] {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .search-form button {
            padding: 10px 20px;
            font-size: 16px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .search-form button:hover {
            background: #0052a3;
        }
        .search-info {
            color: #666;
            margin-bottom: 20px;
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
        .help-text {
            color: #888;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <a href="/" class="back-link">← 返回首頁</a>

    <h1>搜尋文章</h1>

    <div class="search-form">
        <form action="/search" method="GET">
            <input type="text" name="q" value="{{ $query }}" placeholder="輸入搜尋關鍵字...">
            <button type="submit">搜尋</button>
            <div class="help-text">提示: 使用 "/" 分隔多個關鍵字進行 OR 搜尋，例如: lyon/heritage</div>
        </form>
    </div>

    @if($query)
        <div class="search-info">
            搜尋關鍵字: <strong>{{ $query }}</strong>
        </div>

        @if(count($results) > 0)
            <div class="results">
                <p>找到 {{ count($results) }} 個結果</p>
                <ul class="item-list">
                    @foreach($results as $result)
                        <li class="item">
                            <a href="/heritages/{{ $result['path'] }}">
                                {{ $result['title'] }}
                            </a>
                            @if($result['summary'])
                                <div class="summary">{{ $result['summary'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="no-results">沒有找到符合 "{{ $query }}" 的結果</p>
        @endif
    @endif
</body>
</html>