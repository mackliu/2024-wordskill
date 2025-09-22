# CLAUDE.md

此文件為 Claude Code (claude.ai/code) 在此專案中工作時提供指引。

## 專案概述

這是一個 Laravel 9.52.20 應用程式，設計用來管理和顯示 `public/content-pages` 目錄中的內容。應用程式會讀取並解析目錄結構，建立可導覽的內容頁面階層。

## 常用開發指令

### 伺服器與開發
```bash
# 啟動開發伺服器
php artisan serve

# 啟動 Vite 開發伺服器（處理前端資源）
npm run dev

# 建置正式環境資源
npm run build
```

### 資料庫操作
```bash
# 執行資料庫遷移
php artisan migrate

# 執行遷移並填充種子資料
php artisan migrate --seed

# 回滾資料庫遷移
php artisan migrate:rollback

# 重新建立資料庫（刪除所有資料表並重新執行遷移）
php artisan migrate:fresh
```

### 測試
```bash
# 執行所有測試
php artisan test

# 執行特定測試檔案
php artisan test tests/Feature/ExampleTest.php

# 平行執行測試
php artisan test --parallel
```

### 程式碼品質
```bash
# 使用 Laravel Pint 格式化程式碼
./vendor/bin/pint

# 清除應用程式快取
php artisan cache:clear

# 清除設定快取
php artisan config:clear

# 清除路由快取
php artisan route:clear
```

## 架構概述

### 內容管理系統
此應用程式實作了一個自訂的內容管理系統：
- 從 `public/content-pages/` 目錄讀取內容
- 使用 `RecursiveDirectoryIterator` 遞迴解析目錄結構
- 將目錄、檔案和圖片分別儲存至不同的陣列
- 透過 `HomeController` 提供階層式導覽

### 核心元件

**HomeController** (`app/Http/Controllers/HomeController.php`):
- `index()`: 初始化並解析內容目錄結構
- `listDirectoriesAndFiles()`: 列出指定層級的目錄和檔案
- `handleHeritages()`: 將請求路由至適當的內容處理器
- `parsePage()`: 從內容檔案中解析前置資料（front matter）

**路由** (`routes/web.php`):
- `/` - 首頁，顯示根目錄列表
- `/tags/{tag}` - 標籤搜尋功能
- `/heritages/{path?}` - 處理所有 heritage 內容，支援萬用字元路徑

### 目錄結構處理
應用程式維護三個主要資料結構：
- `$directories[]` - 所有目錄路徑（排除 'images' 資料夾）
- `$files[]` - 所有檔案路徑
- `$images[]` - 來自 'images' 目錄的圖片檔案

檔案以反向時間順序顯示（最新的在前），目錄則按字母順序排序。

## 開發注意事項

- 程式碼中使用中文註解
- 除錯語句（`dd()`）已存在但在正式環境中已註解掉
- 基礎內容路徑為 `public/content-pages/`
- 前置資料解析支援內容檔案中的 YAML 格式元資料