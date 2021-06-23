<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//****** 秒杀示例 ******//
Route::get('/secKill/add', [\App\Http\Controllers\SecKillController::class, 'index']);
Route::get('/secKill/lists', [\App\Http\Controllers\SecKillController::class, 'getLists']);
Route::get('/secKill/del', [\App\Http\Controllers\SecKillController::class, 'del']);
//****** 秒杀示例 ******//

//****** 限流示例 ******//
Route::get('/interfaceLimit/index', [\App\Http\Controllers\InterfaceLimitController::class, 'index']);
//****** 限流示例 ******//


