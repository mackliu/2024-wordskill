# WSC2024 里昂文化遺產網站 - Laravel 解題教材

## 前言
本教材專為有 PHP 及網頁程式基礎但對檔案操作不熟悉的高一學生設計，透過實際的國際技能競賽題目，學習如何使用 Laravel 框架處理檔案系統操作，建立一個動態內容管理網站。

---

## 一、試題說明

### 題目背景
WorldSkills 2024 網頁技術模組 C 要求建立一個里昂文化遺產網站。網站需要讀取存放在 `public/content-pages` 目錄中的靜態檔案（.html 和 .txt 格式），並將這些檔案動態呈現為網頁內容。

### 主要功能需求

1. **目錄與檔案列表**
   - 顯示 content-pages 目錄下的所有子資料夾和檔案
   - 支援巢狀子資料夾的瀏覽
   - 子資料夾按字母順序排列，檔案按反向字母順序排列（最新的在前）

2. **檔案過濾機制**
   - 隱藏未來日期的檔案（日期在今天之後）
   - 隱藏草稿狀態的檔案（front-matter 中 draft: "true"）
   - 隱藏沒有正確日期格式的檔案（檔名不符合 YYYY-MM-DD- 格式）

3. **內容頁面呈現**
   - 解析 front-matter（前置資料）
   - 自動處理 .html 和 .txt 兩種格式
   - 處理圖片路徑對應

4. **標籤與搜尋功能**
   - 根據標籤篩選文章
   - 全文搜尋功能（支援 "/" 分隔多個關鍵字的 OR 搜尋）

5. **URL 路由設計**
   - `/` - 首頁，顯示根目錄內容
   - `/heritages/{path}` - 顯示特定檔案或資料夾
   - `/tags/{tag}` - 顯示含有特定標籤的所有文章
   - `/search` - 搜尋功能
   - URL 中不顯示副檔名

### 技術要求掌握
要成功解決這道題目，需要掌握以下技術：
- PHP 檔案系統操作函式
- 目錄遞迴遍歷
- 文字檔案讀取與解析
- 正規表達式（Regular Expression）
- Laravel 路由系統
- Laravel 視圖（Blade）模板

---

## 二、技術點與函式說明

### 1. PHP 檔案系統函式

#### `glob()` - 尋找符合模式的檔案路徑
```php
// 取得指定目錄下的所有項目
$items = glob($path . '/*');

// 範例：取得所有 .txt 檔案
$textFiles = glob('/path/to/directory/*.txt');

// 範例：取得所有子目錄和檔案
$allItems = glob('/path/to/directory/*');
```

#### `is_dir()` 與 `is_file()` - 判斷路徑類型
```php
$path = '/path/to/something';

if (is_dir($path)) {
    echo "這是一個目錄";
} elseif (is_file($path)) {
    echo "這是一個檔案";
} else {
    echo "路徑不存在";
}
```

#### `file_get_contents()` - 讀取檔案內容
```php
// 讀取整個檔案內容為字串
$content = file_get_contents('/path/to/file.txt');

// 檢查檔案是否讀取成功
if ($content === false) {
    echo "無法讀取檔案";
}
```

#### `basename()` 與 `pathinfo()` - 路徑資訊處理
```php
$filePath = '/path/to/2024-09-01-example-page.html';

// 取得檔案名稱
$fileName = basename($filePath);
// 結果: 2024-09-01-example-page.html

// 取得檔案資訊
$info = pathinfo($filePath);
// $info['dirname']   = /path/to
// $info['basename']  = 2024-09-01-example-page.html
// $info['filename']  = 2024-09-01-example-page
// $info['extension'] = html
```

### 2. 字串處理函式

#### `substr()` - 截取字串
```php
$text = "Hello World";
$part = substr($text, 0, 5);  // "Hello"

// 檢查檔案內容是否以 '---' 開頭（front-matter）
if (substr($content, 0, 3) === '---') {
    echo "檔案包含 front-matter";
}
```

#### `explode()` - 分割字串
```php
// 以換行符號分割內容
$lines = explode("\n", $content);

// 以逗號分割標籤
$tags = explode(',', 'php, laravel, web');
// 結果: ['php', ' laravel', ' web']
```

#### `str_replace()` - 字串替換
```php
// 將連字號替換為空格
$title = str_replace('-', ' ', 'hello-world');
// 結果: "hello world"

// 移除路徑中的基礎路徑部分
$relativePath = str_replace($basePath . '/', '', $fullPath);
```

### 3. 正規表達式

#### `preg_match()` - 模式匹配
```php
// 檢查檔名是否符合日期格式
$pattern = '/^(\d{4}-\d{2}-\d{2})-(.+)\.(html|txt)$/';
if (preg_match($pattern, $fileName, $matches)) {
    $date = $matches[1];      // 2024-09-01
    $slug = $matches[2];      // example-page
    $extension = $matches[3]; // html
}

// 檢查是否為圖片檔案
if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $fileName)) {
    echo "這是圖片檔案";
}
```

#### `preg_replace_callback()` - 使用回呼函式替換
```php
// 替換圖片路徑
$content = preg_replace_callback(
    '/src=["\'](?!http|\/)(.*?)["\']/',
    function($matches) {
        return 'src="' . url('/public/content-pages/images/' . $matches[1]) . '"';
    },
    $content
);
```

### 4. Laravel 特定函式

#### `public_path()` - 取得 public 目錄路徑
```php
// 取得 public 目錄的絕對路徑
$publicPath = public_path();
// 結果: /var/www/html/public

// 取得 public 下特定檔案的路徑
$contentPath = public_path('content-pages');
```

#### `url()` - 產生完整 URL
```php
// 產生完整的 URL
$imageUrl = url('/public/content-pages/images/photo.jpg');
// 結果: http://localhost/01_module_c/public/content-pages/images/photo.jpg
```

#### `view()` - 返回視圖
```php
// 返回視圖並傳遞資料
return view('heritage.list', [
    'directories' => $directories,
    'files' => $files,
    'currentPath' => $path
]);
```

### 5. 陣列處理函式

#### `array_map()` - 對陣列每個元素套用函式
```php
// 移除標籤前後的空格
$tags = explode(',', 'php, laravel, web');
$tags = array_map('trim', $tags);
// 結果: ['php', 'laravel', 'web']
```

#### `sort()` 與 `rsort()` - 陣列排序
```php
// 正向字母排序（A-Z）
sort($directories);

// 反向字母排序（Z-A）
rsort($files);
```

#### `in_array()` - 檢查元素是否在陣列中
```php
$tags = ['php', 'laravel', 'web'];
if (in_array('laravel', $tags)) {
    echo "包含 laravel 標籤";
}
```

---

## 三、核心程式碼說明

### 1. 目錄內容列表功能

```php
protected function listDirectory($subPath = '')
{
    // 建立完整路徑
    // 如果有子路徑，將基礎路徑與子路徑組合
    // 否則只使用基礎路徑
    $path = $subPath ? $this->basePath . '/' . $subPath : $this->basePath;

    // 檢查路徑是否為有效目錄
    // 如果不是目錄，返回 404 錯誤
    if (!is_dir($path)) {
        abort(404);
    }

    // 使用 glob() 函式取得目錄下所有項目
    // /* 表示匹配所有檔案和資料夾
    $items = glob($path . '/*');

    // 初始化兩個陣列來分別儲存目錄和檔案
    $directories = [];
    $files = [];

    // 遍歷所有項目
    foreach ($items as $item) {
        // 使用 basename() 取得項目名稱（不含路徑）
        $name = basename($item);

        // 忽略 images 資料夾
        // 因為 images 資料夾專門存放圖片，不需要列出
        if ($name === 'images') {
            continue;
        }

        // 判斷項目類型
        if (is_dir($item)) {
            // 如果是目錄，加入目錄陣列
            $directories[] = [
                'name' => $name,
                // 建立相對路徑，用於 URL
                'path' => $subPath ? $subPath . '/' . $name : $name,
                'type' => 'directory'
            ];
        } else {
            // 如果是檔案，需要進行額外檢查

            // 解析檔名，檢查是否符合日期格式
            $fileInfo = $this->parseFileName($name);

            // 如果檔名格式不正確，或檔案不應該顯示，跳過
            if (!$fileInfo || !$this->shouldShowFile($item, $fileInfo)) {
                continue;
            }

            // 讀取檔案內容
            $content = file_get_contents($item);

            // 解析 front-matter（前置資料）
            $parsed = $this->parseFrontMatter($content);

            // 移除檔名中的副檔名，用於 URL
            $pathWithoutExt = pathinfo($name, PATHINFO_FILENAME);

            // 將檔案資訊加入陣列
            $files[] = [
                'name' => $name,
                // URL 路徑不包含副檔名
                'path' => $subPath ? $subPath . '/' . $pathWithoutExt : $pathWithoutExt,
                'type' => 'file',
                'date' => $fileInfo['date'],
                // 優先使用 front-matter 中的標題，否則從檔名生成
                'title' => $parsed['meta']['title'] ?? $this->getTitleFromFileName($name),
                'summary' => $parsed['meta']['summary'] ?? '',
                'tags' => $this->parseTags($parsed['meta']['tags'] ?? '')
            ];
        }
    }

    // 排序：目錄按字母順序（A-Z）
    sort($directories);

    // 排序：檔案按反向字母順序（Z-A），最新的在前
    rsort($files);

    // 返回視圖並傳遞資料
    return view('heritage.list', [
        'directories' => $directories,
        'files' => $files,
        'currentPath' => $subPath
    ]);
}
```

### 2. 檔名解析與驗證

```php
protected function parseFileName($fileName)
{
    // 定義正規表達式模式
    // ^(\d{4}-\d{2}-\d{2})- : 匹配開頭的日期格式 YYYY-MM-DD-
    // (.+) : 匹配檔名主體（一個或多個任意字元）
    // \.(html|txt)$ : 匹配結尾的副檔名 .html 或 .txt
    if (!preg_match('/^(\d{4}-\d{2}-\d{2})-(.+)\.(html|txt)$/', $fileName, $matches)) {
        // 如果不符合格式，返回 null
        return null;
    }

    // 返回解析結果
    return [
        'date' => $matches[1],      // 日期部分，如 "2024-09-01"
        'slug' => $matches[2],      // 檔名主體，如 "example-page"
        'extension' => $matches[3]  // 副檔名，如 "html" 或 "txt"
    ];
}

protected function shouldShowFile($filePath, $fileInfo)
{
    // 檢查日期是否在未來
    // date('Y-m-d') 取得今天的日期
    // 如果檔案日期大於今天，不顯示
    if ($fileInfo && $fileInfo['date'] > date('Y-m-d')) {
        return false;
    }

    // 讀取檔案內容以檢查 front-matter
    $content = file_get_contents($filePath);
    $parsed = $this->parseFrontMatter($content);

    // 檢查是否為草稿
    // 如果 front-matter 中 draft 設為 "true"，不顯示
    if (isset($parsed['meta']['draft']) && $parsed['meta']['draft'] === 'true') {
        return false;
    }

    // 通過所有檢查，可以顯示
    return true;
}
```

### 3. Front-matter 解析

```php
protected function parseFrontMatter($content)
{
    // 檢查內容是否以 '---' 開頭
    // front-matter 必須在檔案最開頭
    if (substr($content, 0, 3) !== '---') {
        // 沒有 front-matter，返回空的 meta 和完整內容
        return ['meta' => [], 'body' => $content];
    }

    // 使用 '---' 分割內容
    // 第二個參數 3 表示最多分割成 3 部分
    $parts = explode('---', $content, 3);

    // 檢查分割結果
    // 正確的格式應該有 3 部分：空字串、front-matter、主要內容
    if (count($parts) < 3) {
        return ['meta' => [], 'body' => $content];
    }

    // 初始化 meta 資料陣列
    $meta = [];

    // 分割 front-matter 為多行
    // trim() 移除前後空白
    $lines = explode("\n", trim($parts[1]));

    // 解析每一行
    foreach ($lines as $line) {
        // 尋找冒號位置
        if (strpos($line, ':') !== false) {
            // 以第一個冒號分割為 key 和 value
            list($key, $value) = explode(':', $line, 2);
            // 移除前後空白並儲存
            $meta[trim($key)] = trim($value);
        }
    }

    // 返回解析結果
    return [
        'meta' => $meta,           // front-matter 資料
        'body' => trim($parts[2])  // 主要內容
    ];
}
```

### 4. 處理無副檔名的 URL

```php
public function handleHeritages($path = null)
{
    // 如果沒有路徑，顯示根目錄
    if (!$path) {
        return $this->listDirectory();
    }

    // 建立完整路徑
    $fullPath = $this->basePath . '/' . $path;

    // 首先檢查是否為檔案（可能已包含副檔名）
    if (is_file($fullPath)) {
        return $this->showFile($path);
    }

    // 如果不是檔案，嘗試加上常見副檔名
    // 因為 URL 中不顯示副檔名，需要自動補上
    $extensions = ['.html', '.txt'];
    foreach ($extensions as $ext) {
        $fileWithExt = $fullPath . $ext;
        // 檢查加上副檔名後的檔案是否存在
        if (is_file($fileWithExt)) {
            // 找到檔案，顯示內容
            return $this->showFile($path . $ext);
        }
    }

    // 如果不是檔案，檢查是否為目錄
    if (is_dir($fullPath)) {
        return $this->listDirectory($path);
    }

    // 都不是，返回 404 錯誤
    abort(404);
}
```

### 5. 處理不同格式的內容

```php
protected function processTxtContent($content)
{
    // 將內容分割為多行
    $lines = explode("\n", $content);
    $html = '';

    // 處理每一行
    foreach ($lines as $line) {
        // 移除前後空白
        $line = trim($line);

        // 跳過空行
        if (empty($line)) {
            continue;
        }

        // 檢查是否為圖片
        // 圖片的特徵：單獨一行，沒有空格，以圖片副檔名結尾
        if (preg_match('/^[^\s]+\.(jpg|jpeg|png|gif|webp)$/i', $line)) {
            // 轉換為 img 標籤
            // 使用 url() 函式產生完整路徑
            $html .= '<img src="' . url('/public/content-pages/images/' . $line) . '" alt="' . $line . '">';
        } else {
            // 一般文字轉換為段落
            // htmlspecialchars() 避免 XSS 攻擊
            $html .= '<p>' . htmlspecialchars($line) . '</p>';
        }
    }

    return $html;
}

protected function fixImagePaths($content)
{
    // 使用正規表達式替換圖片路徑
    // 模式說明：
    // src=["'] : 匹配 src=" 或 src='
    // (?!http|\/) : 負向前瞻，排除已經是絕對路徑的情況
    // (.*?) : 非貪婪匹配圖片檔名
    // ["'] : 匹配結束引號
    $content = preg_replace_callback(
        '/src=["\'](?!http|\/)(.*?)["\']/',
        function($matches) {
            // $matches[1] 包含圖片檔名
            // 將相對路徑轉換為完整 URL
            return 'src="' . url('/public/content-pages/images/' . $matches[1]) . '"';
        },
        $content
    );

    return $content;
}
```

### 6. 搜尋功能實作

```php
public function search(Request $request)
{
    // 取得搜尋查詢字串
    $query = $request->get('q', '');

    // 如果沒有查詢內容，顯示空結果
    if (empty($query)) {
        return view('heritage.search', ['results' => [], 'query' => '']);
    }

    // 支援 "/" 分隔多個關鍵字
    // 例如："lyon/heritage" 會搜尋包含 "lyon" 或 "heritage" 的內容
    $keywords = explode('/', $query);
    // 移除每個關鍵字前後的空格
    $keywords = array_map('trim', $keywords);

    // 取得所有檔案
    $allFiles = $this->getAllFiles($this->basePath);
    $results = [];

    // 搜尋每個檔案
    foreach ($allFiles as $file) {
        // 讀取檔案內容
        $content = file_get_contents($file);
        $parsed = $this->parseFrontMatter($content);

        // 取得標題
        $title = $parsed['meta']['title'] ?? $this->getTitleFromFileName(basename($file));
        // 合併標題和內容進行搜尋
        $fullContent = $title . ' ' . $parsed['body'];

        // 檢查是否包含任何關鍵字（OR 邏輯）
        foreach ($keywords as $keyword) {
            // stripos() 進行不區分大小寫的搜尋
            if (stripos($fullContent, $keyword) !== false) {
                $fileName = basename($file);
                $relativePath = str_replace($this->basePath . '/', '', $file);
                // 移除路徑中的副檔名
                $relativePathWithoutExt = preg_replace('/\.(html|txt)$/', '', $relativePath);

                // 加入搜尋結果
                $results[] = [
                    'name' => $fileName,
                    'path' => $relativePathWithoutExt,
                    'title' => $title,
                    'summary' => $parsed['meta']['summary'] ?? ''
                ];
                // 找到一個關鍵字就足夠，跳出迴圈
                break;
            }
        }
    }

    // 返回搜尋結果視圖
    return view('heritage.search', [
        'results' => $results,
        'query' => $query
    ]);
}
```

### 7. 遞迴取得所有檔案

```php
protected function getAllFiles($dir)
{
    $files = [];
    // 取得目錄下所有項目
    $items = glob($dir . '/*');

    foreach ($items as $item) {
        // 如果是目錄且不是 images，遞迴處理
        if (is_dir($item) && basename($item) !== 'images') {
            // 遞迴呼叫，取得子目錄中的檔案
            $files = array_merge($files, $this->getAllFiles($item));
        } elseif (is_file($item)) {
            // 檢查檔案是否應該顯示
            $fileInfo = $this->parseFileName(basename($item));
            if ($fileInfo && $this->shouldShowFile($item, $fileInfo)) {
                $files[] = $item;
            }
        }
    }

    return $files;
}
```

---

## 四、學習重點總結

### 1. 檔案系統操作的關鍵概念

- **路徑處理**：始終要區分絕對路徑和相對路徑，使用 Laravel 的輔助函式可以避免路徑錯誤
- **檔案類型判斷**：在處理之前先判斷是目錄還是檔案，避免錯誤操作
- **錯誤處理**：檔案操作可能失敗，要做好錯誤檢查（如 `file_get_contents()` 可能返回 `false`）

### 2. 字串解析技巧

- **正規表達式**：強大但複雜，需要多練習才能熟練運用
- **分段處理**：將複雜的解析任務分解為多個步驟，每步處理一部分
- **資料驗證**：永遠不要假設輸入資料的格式正確，要進行驗證

### 3. Laravel 框架運用

- **MVC 架構**：Controller 處理邏輯，View 負責顯示，保持關注點分離
- **路由設計**：使用萬用字元路由 `{path?}` 處理動態路徑
- **輔助函式**：善用 Laravel 提供的輔助函式，如 `url()`、`public_path()` 等

### 4. 效能考量

- **快取策略**：對於不常變動的檔案內容，可以考慮加入快取機制
- **延遲載入**：只在需要時才讀取檔案內容，避免一次載入過多資料
- **適當的資料結構**：選擇合適的資料結構（陣列、物件）來儲存和處理資料

### 5. 實務開發技巧

- **程式碼註解**：為複雜的邏輯加上清楚的註解，方便日後維護
- **函式拆分**：將複雜功能拆分為多個小函式，提高程式碼可讀性和重用性
- **錯誤處理**：適當使用 `abort(404)` 等錯誤處理機制，提供良好的使用者體驗

---

## 五、練習建議

1. **基礎練習**
   - 嘗試讀取一個目錄下的所有檔案
   - 練習解析簡單的文字檔案
   - 實作檔案排序功能

2. **進階練習**
   - 實作檔案上傳功能
   - 加入檔案快取機制
   - 實作更複雜的搜尋功能（如模糊搜尋）

3. **擴展功能**
   - 加入分頁功能
   - 實作檔案版本控制
   - 加入使用者權限管理

---

## 結語

這個題目雖然看似簡單，但涵蓋了網頁開發中檔案處理的多個重要概念。透過實作這個專案，不僅能學習到 PHP 的檔案操作，更能理解如何在 Laravel 框架中組織和實現一個完整的功能。

關鍵在於理解每個函式的用途，並能夠將它們組合起來解決實際問題。希望這份教材能幫助你掌握檔案操作的核心概念，為未來的網頁開發學習打下堅實的基礎。

---

### 參考資源
- [PHP 官方文件 - 檔案系統函式](https://www.php.net/manual/zh/ref.filesystem.php)
- [Laravel 官方文件](https://laravel.com/docs)
- [正規表達式教學](https://regexone.com/)
- [WorldSkills 官方網站](https://worldskills.org/)