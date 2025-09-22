<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listing Page Layout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            background: #f5f5f5;
        }
        .header {
            background: #000;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 2em;
            font-weight: bold;
        }
        .container {
            display: flex;
            gap: 40px;
        }
        .main-content {
            flex: 1;
        }
        .sidebar {
            width: 300px;
        }
        .search-box {
            margin-bottom: 30px;
        }
        .search-box h3 {
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        .search-box form {
            display: flex;
            gap: 5px;
        }
        .search-box input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .search-box button {
            padding: 8px 15px;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
        .search-box button:hover {
            background: #555;
        }
        .breadcrumb {
            margin-bottom: 20px;
            color: #666;
            font-size: 0.9em;
        }
        .breadcrumb a {
            color: #0000ff;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .item-list {
            list-style: none;
        }
        .item {
            margin-bottom: 25px;
        }
        .item a.title {
            color: #0000ff;
            text-decoration: none;
            font-size: 1.1em;
            display: block;
            margin-bottom: 5px;
        }
        .item a.title:hover {
            text-decoration: underline;
        }
        .item .summary {
            color: #333;
            font-size: 0.95em;
            line-height: 1.5;
        }
        .directory-item {
            margin-bottom: 15px;
        }
        .directory-item a {
            color: #0000ff;
            text-decoration: none;
            font-size: 1.1em;
        }
        .directory-item a:hover {
            text-decoration: underline;
        }
        .no-items {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listing Page Layout</h1>
    </div>

    <div class="container">
        <div class="main-content">
            @if($currentPath)
                <div class="breadcrumb">
                    <a href="/">Home</a> /
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
                <ul class="item-list">
                    @foreach($directories as $dir)
                        <li class="directory-item">
                            <a href="/heritages/{{ $dir['path'] }}">
                                {{ $dir['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <ul class="item-list">
                @foreach($files as $file)
                    <li class="item">
                        <a href="/heritages/{{ $file['path'] }}" class="title">
                            {{ $file['title'] }}
                        </a>
                        @if($file['summary'])
                            <div class="summary">{{ $file['summary'] }}</div>
                        @else
                            <div class="summary">{{ substr(strip_tags($file['title']), 0, 150) }}...</div>
                        @endif
                    </li>
                @endforeach
            </ul>

            @if(count($directories) == 0 && count($files) == 0)
                <p class="no-items">No content in this folder</p>
            @endif
        </div>

        <div class="sidebar">
            <div class="search-box">
                <h3>Search</h3>
                <form action="/search" method="GET">
                    <input type="text" name="q" placeholder="KEYWORD" />
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>