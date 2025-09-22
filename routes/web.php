<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// 首頁 - 顯示 content-pages 根目錄列表
Route::get('/', [HomeController::class, 'index']);

// 處理所有 heritages 路徑（包括子資料夾和檔案）
Route::get('/heritages/{path?}', [HomeController::class, 'handleHeritages'])
    ->where('path', '.*');

// 標籤查詢
Route::get('/tags/{tag}', [HomeController::class, 'searchByTag']);

// 搜尋功能（選做）
Route::get('/search', [HomeController::class, 'search']);