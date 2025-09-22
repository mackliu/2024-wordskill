<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $basePath;

    public function __construct()
    {
        $this->basePath = public_path('content-pages');
    }

    /**
     * 首頁 - 顯示根目錄列表
     */
    public function index()
    {
        return $this->listDirectory();
    }

    /**
     * 處理 heritages 路徑 - 可能是目錄或檔案
     */
    public function handleHeritages($path = null)
    {
        if (!$path) {
            return $this->listDirectory();
        }

        $fullPath = $this->basePath . '/' . $path;

        // 檢查是否為檔案
        if (is_file($fullPath)) {
            return $this->showFile($path);
        }

        // 檢查是否為目錄
        if (is_dir($fullPath)) {
            return $this->listDirectory($path);
        }

        abort(404);
    }

    /**
     * 列出目錄內容
     */
    protected function listDirectory($subPath = '')
    {
        $path = $subPath ? $this->basePath . '/' . $subPath : $this->basePath;

        if (!is_dir($path)) {
            abort(404);
        }

        // 取得所有檔案和目錄
        $items = glob($path . '/*');

        $directories = [];
        $files = [];

        foreach ($items as $item) {
            $name = basename($item);

            // 忽略 images 資料夾
            if ($name === 'images') {
                continue;
            }

            if (is_dir($item)) {
                $directories[] = [
                    'name' => $name,
                    'path' => $subPath ? $subPath . '/' . $name : $name,
                    'type' => 'directory'
                ];
            } else {
                // 檢查檔案是否符合顯示條件
                $fileInfo = $this->parseFileName($name);
                if (!$fileInfo || !$this->shouldShowFile($item, $fileInfo)) {
                    continue;
                }

                // 讀取檔案取得 front-matter
                $content = file_get_contents($item);
                $parsed = $this->parseFrontMatter($content);

                $files[] = [
                    'name' => $name,
                    'path' => $subPath ? $subPath . '/' . $name : $name,
                    'type' => 'file',
                    'date' => $fileInfo['date'],
                    'title' => $parsed['meta']['title'] ?? $this->getTitleFromFileName($name),
                    'summary' => $parsed['meta']['summary'] ?? '',
                    'tags' => $this->parseTags($parsed['meta']['tags'] ?? '')
                ];
            }
        }

        // 排序：目錄按字母順序，檔案按反向字母順序
        sort($directories);
        rsort($files);

        return view('heritage.list', [
            'directories' => $directories,
            'files' => $files,
            'currentPath' => $subPath
        ]);
    }

    /**
     * 顯示單一檔案內容
     */
    protected function showFile($path)
    {
        $fullPath = $this->basePath . '/' . $path;

        if (!is_file($fullPath)) {
            abort(404);
        }

        $content = file_get_contents($fullPath);
        $parsed = $this->parseFrontMatter($content);

        // 取得檔案資訊
        $fileName = basename($path);
        $fileInfo = $this->parseFileName($fileName);

        // 決定標題
        $title = $parsed['meta']['title'] ??
                 $this->extractH1($parsed['body']) ??
                 $this->getTitleFromFileName($fileName);

        // 處理內容
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($extension === 'txt') {
            $parsed['body'] = $this->processTxtContent($parsed['body']);
        }

        // 處理圖片路徑
        $parsed['body'] = $this->fixImagePaths($parsed['body']);

        return view('heritage.show', [
            'title' => $title,
            'meta' => $parsed['meta'],
            'body' => $parsed['body'],
            'date' => $fileInfo['date'] ?? null,
            'tags' => $this->parseTags($parsed['meta']['tags'] ?? ''),
            'cover' => $parsed['meta']['cover'] ?? $this->getDefaultCover($fileName)
        ]);
    }

    /**
     * 解析 front-matter
     */
    protected function parseFrontMatter($content)
    {
        if (substr($content, 0, 3) !== '---') {
            return ['meta' => [], 'body' => $content];
        }

        $parts = explode('---', $content, 3);
        if (count($parts) < 3) {
            return ['meta' => [], 'body' => $content];
        }

        $meta = [];
        $lines = explode("\n", trim($parts[1]));

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $meta[trim($key)] = trim($value);
            }
        }

        return [
            'meta' => $meta,
            'body' => trim($parts[2])
        ];
    }

    /**
     * 解析檔名（取得日期等資訊）
     */
    protected function parseFileName($fileName)
    {
        // 檢查是否符合 YYYY-MM-DD- 格式
        if (!preg_match('/^(\d{4}-\d{2}-\d{2})-(.+)\.(html|txt)$/', $fileName, $matches)) {
            return null;
        }

        return [
            'date' => $matches[1],
            'slug' => $matches[2],
            'extension' => $matches[3]
        ];
    }

    /**
     * 檢查是否應該顯示檔案
     */
    protected function shouldShowFile($filePath, $fileInfo)
    {
        // 檢查日期是否在未來
        if ($fileInfo && $fileInfo['date'] > date('Y-m-d')) {
            return false;
        }

        // 檢查是否為草稿
        $content = file_get_contents($filePath);
        $parsed = $this->parseFrontMatter($content);

        if (isset($parsed['meta']['draft']) && $parsed['meta']['draft'] === 'true') {
            return false;
        }

        return true;
    }

    /**
     * 從檔名取得標題
     */
    protected function getTitleFromFileName($fileName)
    {
        $name = pathinfo($fileName, PATHINFO_FILENAME);

        // 移除日期部分
        $name = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $name);

        // 將連字號替換為空格，並轉為標題格式
        $name = str_replace('-', ' ', $name);

        return ucwords($name);
    }

    /**
     * 從 HTML 內容提取第一個 h1
     */
    protected function extractH1($content)
    {
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/i', $content, $matches)) {
            return strip_tags($matches[1]);
        }
        return null;
    }

    /**
     * 處理 .txt 檔案內容
     */
    protected function processTxtContent($content)
    {
        $lines = explode("\n", $content);
        $html = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // 檢查是否為圖片（單獨一行且有圖片副檔名）
            if (preg_match('/^[^\s]+\.(jpg|jpeg|png|gif|webp)$/i', $line)) {
                $html .= '<img src="/content-pages/images/' . $line . '" alt="' . $line . '">';
            } else {
                $html .= '<p>' . htmlspecialchars($line) . '</p>';
            }
        }

        return $html;
    }

    /**
     * 修正圖片路徑
     */
    protected function fixImagePaths($content)
    {
        // 將相對路徑轉為絕對路徑
        $content = preg_replace(
            '/src=["\'](?!http|\/)(.*?)["\']/',
            'src="/content-pages/images/$1"',
            $content
        );

        return $content;
    }

    /**
     * 解析標籤字串
     */
    protected function parseTags($tagsString)
    {
        if (empty($tagsString)) {
            return [];
        }

        $tags = explode(',', $tagsString);
        return array_map('trim', $tags);
    }

    /**
     * 取得預設封面圖片
     */
    protected function getDefaultCover($fileName)
    {
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        return $name . '.jpeg';
    }

    /**
     * 標籤搜尋
     */
    public function searchByTag($tag)
    {
        $allFiles = $this->getAllFiles($this->basePath);
        $matchedFiles = [];

        foreach ($allFiles as $file) {
            $content = file_get_contents($file);
            $parsed = $this->parseFrontMatter($content);

            $tags = $this->parseTags($parsed['meta']['tags'] ?? '');

            if (in_array($tag, $tags)) {
                $fileName = basename($file);
                $relativePath = str_replace($this->basePath . '/', '', $file);

                $matchedFiles[] = [
                    'name' => $fileName,
                    'path' => $relativePath,
                    'title' => $parsed['meta']['title'] ?? $this->getTitleFromFileName($fileName),
                    'summary' => $parsed['meta']['summary'] ?? ''
                ];
            }
        }

        return view('heritage.tag', [
            'tag' => $tag,
            'files' => $matchedFiles
        ]);
    }

    /**
     * 搜尋功能
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return view('heritage.search', ['results' => [], 'query' => '']);
        }

        // 支援 "/" 分隔多個關鍵字
        $keywords = explode('/', $query);
        $keywords = array_map('trim', $keywords);

        $allFiles = $this->getAllFiles($this->basePath);
        $results = [];

        foreach ($allFiles as $file) {
            $content = file_get_contents($file);
            $parsed = $this->parseFrontMatter($content);

            $title = $parsed['meta']['title'] ?? $this->getTitleFromFileName(basename($file));
            $fullContent = $title . ' ' . $parsed['body'];

            // 檢查是否包含任何關鍵字（OR 邏輯）
            foreach ($keywords as $keyword) {
                if (stripos($fullContent, $keyword) !== false) {
                    $fileName = basename($file);
                    $relativePath = str_replace($this->basePath . '/', '', $file);

                    $results[] = [
                        'name' => $fileName,
                        'path' => $relativePath,
                        'title' => $title,
                        'summary' => $parsed['meta']['summary'] ?? ''
                    ];
                    break;
                }
            }
        }

        return view('heritage.search', [
            'results' => $results,
            'query' => $query
        ]);
    }

    /**
     * 遞迴取得所有檔案
     */
    protected function getAllFiles($dir)
    {
        $files = [];
        $items = glob($dir . '/*');

        foreach ($items as $item) {
            if (is_dir($item) && basename($item) !== 'images') {
                $files = array_merge($files, $this->getAllFiles($item));
            } elseif (is_file($item)) {
                $fileInfo = $this->parseFileName(basename($item));
                if ($fileInfo && $this->shouldShowFile($item, $fileInfo)) {
                    $files[] = $item;
                }
            }
        }

        return $files;
    }
}