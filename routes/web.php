<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;

/* Route::get('/', function () {
    return view('welcome');
}); */

//將首頁的索引列表請求導到HomeController的index方法
Route::get('/', [HomeController::class, 'index']);
//搜尋標籤的路由
Route::get('/tags/{tag}', [HomeController::class, 'searchByTag']);

//單一路由處理所有的heritages內容請求,包括子目錄列表
Route::get('/heritages/{path?}', [HomeController::class, 'handleHeritages'])->where('path','.*');

// Single route to handle all heritages content
//Route::get('/01_module_c/{path?}', [Controller::class, 'handleHeritages'])->where('path', '.*');
