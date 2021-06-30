<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



//****** 秒杀示例 ******//
Route::get('/secKill/add', [\App\Http\Controllers\SecKillController::class, 'index']);
Route::get('/secKill/lists', [\App\Http\Controllers\SecKillController::class, 'getLists']);
Route::get('/secKill/del', [\App\Http\Controllers\SecKillController::class, 'del']);
//****** 秒杀示例 ******//

//****** 限流示例 ******//
Route::get('/interfaceLimit/index', [\App\Http\Controllers\InterfaceLimitController::class, 'index']);
//****** 限流示例 ******//

//****** 事件监听示例 ******//
Route::get('/test/index', [\App\Http\Controllers\TestController::class, 'index']);
//****** 事件监听示例 ******//

//****** 队列任务示例 ******//
Route::get('/test/testQueue', [\App\Http\Controllers\TestController::class, 'testQueue']);
//****** 队列任务示例 ******//

//****** 中间件示例 ******//
Route::post('/testMiddleware', [\App\Http\Controllers\TestController::class, 'testMiddleware'])->middleware('check.repeat');
//****** 中间件示例 ******//