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
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [App\Http\Controllers\UserController::class, 'login'])->name('login');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('signup', [App\Http\Controllers\UserController::class, 'signUp'])->name('signUp');
        Route::post('/update', [App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::get('logout',[App\Http\Controllers\UserController::class, 'logout'])->name('logout');
        Route::get('/users', [App\Http\Controllers\UserController::class, 'users'])->name('users');
        Route::get('/user', [App\Http\Controllers\UserController::class, 'user'])->name('user');
        Route::post('/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('delete');

    });
});