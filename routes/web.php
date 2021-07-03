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

//****** CSRF白名单示例 ******//
Route::post('/testCsrf', [\App\Http\Controllers\TestController::class, 'testCsrf']);
//****** CSRF白名单示例 ******//


//****** 控制器示例 ******//
//Route::get('/show/{id}', [\App\Http\Controllers\UserController::class, 'show']);
Route::get('/show/{id}', '\App\Http\Controllers\UserController@show');
Route::get('/profile/{id}', '\App\Http\Controllers\ShowProfileController');
Route::resource('photos', '\App\Http\Controllers\PhotoController'); // 资源路由 对应 增删改查
//****** 控制器示例 ******//


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
