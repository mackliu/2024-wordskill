<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - 里昂文化遺產</title>

    <!-- Social Media Meta Tags -->
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $meta['summary'] ?? '' }}">
    <meta property="og:image" content="{{ url('/public/content-pages/images/' . $cover) }}">
    <meta property="og:type" content="article">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        /* Cover Image */
        .cover-container {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            background: #333;
        }
        .cover-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Title Section */
        .title-section {
            padding: 30px;
            background: #f5f5f5;
            text-align: center;
        }
        .title-section h1 {
            font-size: 2.5em;
            color: #333;
            font-variant-ligatures: common-ligatures;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
            padding: 30px;
        }

        /* Aside Information */
        aside {
            width: 250px;
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        .aside-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .aside-box h3 {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #444;
        }
        .aside-box .date {
            color: #666;
            margin-bottom: 10px;
        }
        .aside-box .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .aside-box .tags a {
            background: #e0e0e0;
            padding: 3px 8px;
            border-radius: 3px;
            text-decoration: none;
            color: #333;
            font-size: 0.9em;
        }
        .aside-box .tags a:hover {
            background: #d0d0d0;
        }
        .draft-notice {
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
        }
        .main-content img {
            width: 100%;
            height: auto;
            margin: 20px 0;
            cursor: pointer;
        }
        .main-content p {
            margin-bottom: 15px;
            text-align: justify;
        }

        /* First letter drop cap */
        .main-content p:first-of-type:first-letter {
            float: left;
            font-size: 3.5em;
            line-height: 0.8;
            margin-right: 5px;
            margin-top: 5px;
            font-weight: bold;
            color: #333;
        }

        /* Navigation */
        .nav-bar {
            background: #333;
            padding: 10px 30px;
        }
        .nav-bar a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }
        .nav-bar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="{{ url('/') }}">首頁</a>
        <a href="{{ url('/heritages') }}">所有文章</a>
    </nav>

    @if(isset($cover))
        <div class="cover-container">
            <img src="{{ url('/public/content-pages/images/' . $cover) }}" alt="{{ $title }}" class="cover-image">
        </div>
    @endif

    <div class="title-section">
        <h1>{{ $title }}</h1>
    </div>

    <div class="container">
        <aside>
            <div class="aside-box">
                @if(isset($meta['draft']) && $meta['draft'] === 'true')
                    <div class="draft-notice">
                        📝 草稿
                    </div>
                @endif

                @if($date)
                    <div class="date">
                        <strong>日期:</strong> {{ $date }}
                    </div>
                @endif

                @if(!empty($tags))
                    <h3>標籤</h3>
                    <div class="tags">
                        @foreach($tags as $tag)
                            <a href="{{ url('/tags/' . $tag) }}">{{ $tag }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </aside>

        <main class="main-content">
            {!! $body !!}
        </main>
    </div>

    <script>
        // 簡單的圖片點擊放大功能
        document.querySelectorAll('.main-content img').forEach(img => {
            img.addEventListener('click', function() {
                if (this.style.transform === 'scale(1.5)') {
                    this.style.transform = 'scale(1)';
                    this.style.zIndex = '1';
                    this.style.position = 'relative';
                } else {
                    this.style.transform = 'scale(1.5)';
                    this.style.zIndex = '1000';
                    this.style.position = 'relative';
                    this.style.transition = 'transform 0.3s';
                }
            });
        });

        // 滾動時關閉放大的圖片
        window.addEventListener('scroll', function() {
            document.querySelectorAll('.main-content img').forEach(img => {
                img.style.transform = 'scale(1)';
                img.style.zIndex = '1';
            });
        });
    </script>
</body>
</html>