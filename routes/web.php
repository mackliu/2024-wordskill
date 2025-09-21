<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;

/* Route::get('/', function () {
    return view('welcome');
}); */

//將首頁的索引列表請求導到HomeController的index方法
Route::get('/', [HomeController::class, 'index']);

// Single route to handle all heritages content
Route::get('/01_module_c/{path?}', [Controller::class, 'handleHeritages'])->where('path', '.*');
