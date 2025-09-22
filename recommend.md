# 里昂遺產地點網站開發建議文件（精簡競賽版）

## 專案概述
4小時競賽專案，從 `public/content-pages/` 資料夾讀取檔案並呈現為網站。重點在功能完整性，不追求完美。

## 必要功能清單（按優先順序）

### 第1小時：基礎架構
1. **簡化現有 HomeController**
   - 直接改造現有的 `index()` 和 `handleHeritages()` 方法
   - 不需要 Service 層，直接在 Controller 處理

2. **基本路由** （已有部分完成）
   ```php
   Route::get('/', [HomeController::class, 'index']);
   Route::get('/heritages/{path?}', [HomeController::class, 'handleHeritages'])->where('path','.*');
   Route::get('/tags/{tag}', [HomeController::class, 'searchByTag']);
   ```

### 第2小時：核心功能
3. **解析 Front-matter**（必要）
   ```php
   // 簡單的字串分割即可
   function parseFrontMatter($content) {
       if (strpos($content, '---') !== 0) return ['meta' => [], 'body' => $content];
       $parts = explode('---', $content, 3);
       // 簡單解析 key: value 格式
   }
   ```

4. **檔案過濾**（必要）
   - 檢查日期格式（前11字元）
   - 比較日期是否未來
   - 檢查 draft 狀態

### 第3小時：內容呈現
5. **單一頁面顯示**
   - .html 直接輸出
   - .txt 簡單處理（每行變 `<p>`，單獨圖片行變 `<img>`）
   - 圖片路徑替換為 `/content-pages/images/`

6. **標題擷取**（按優先序）
   - front-matter title
   - 第一個 h1 標籤
   - 檔名處理

### 第4小時：進階功能與修正
7. **標籤功能**
   - 簡單的陣列過濾即可

8. **搜尋功能**（如果有時間）
   - 簡單的字串搜尋
   - 支援 "/" 分隔（用 explode 處理）

## 可以簡化或跳過的部分

### 簡化項目：
1. **不用 Service 層** - 直接在 Controller 處理所有邏輯
2. **不用快取** - 每次直接讀取檔案系統（檔案不多的話影響不大）
3. **不用 Vite/npm** - 直接在 blade 寫 inline CSS/JS
4. **不做響應式設計** - 只針對桌面版
5. **不處理錯誤狀況** - 假設檔案都存在且格式正確
6. **簡化搜尋** - 用最簡單的 strpos 或 str_contains

### 可跳過的視覺效果（如時間不夠）：
1. 封面圖片聚光燈效果（需要複雜 JS）
2. 圖片點擊放大（需要 JS modal）
3. 首字下沉（CSS 較複雜）
4. 側邊欄 sticky（簡單 CSS 但可省略）

## 最精簡實作方式

### HomeController.php 核心方法
```php
public function handleHeritages($path = null) {
    $basePath = public_path('content-pages');

    // 如果是檔案
    if ($path && is_file($basePath . '/' . $path)) {
        $content = file_get_contents($basePath . '/' . $path);
        $parsed = $this->parseFrontMatter($content);
        return view('page', $parsed);
    }

    // 如果是目錄或根目錄
    $files = glob($basePath . '/' . ($path ? $path . '/' : '') . '*');
    $items = $this->processFiles($files);
    return view('list', ['items' => $items]);
}

private function parseFrontMatter($content) {
    // 簡單分割
    if (substr($content, 0, 3) === '---') {
        $parts = explode('---', $content, 3);
        // 解析 YAML（簡單版）
        $lines = explode("\n", $parts[1]);
        $meta = [];
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $meta[trim($key)] = trim($value);
            }
        }
        return ['meta' => $meta, 'body' => $parts[2]];
    }
    return ['meta' => [], 'body' => $content];
}
```

### 最簡單的 View
```blade
{{-- list.blade.php --}}
@foreach($items as $item)
    <a href="/heritages/{{ $item['path'] }}">
        {{ $item['title'] ?? $item['filename'] }}
    </a>
@endforeach

{{-- page.blade.php --}}
<h1>{{ $meta['title'] ?? 'Untitled' }}</h1>
<div>{!! $body !!}</div>
```

## 時間分配建議

1. **30分鐘** - 修改路由和基本 Controller
2. **30分鐘** - 實作目錄掃描和檔案列表
3. **45分鐘** - Front-matter 解析
4. **45分鐘** - 檔案過濾（日期、draft）
5. **45分鐘** - 內容顯示（.html/.txt 處理）
6. **30分鐘** - 標籤功能
7. **15分鐘** - 基本樣式
8. **剩餘時間** - 除錯和補充功能

## 注意事項
- 先確保核心功能運作，再處理額外要求
- 使用 `dd()` 快速除錯
- 不要花太多時間在樣式上
- 專注於評分標準中的功能點