<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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

Route::post('/', 'Main\Start')->middleware('auth:api')->name('app');


/** Обработка хуков телеграм бота */
Route::any('/tlg{token}', 'Telegram@webhook');

/** Авторизация на канале широковещания */
Route::middleware('auth:api')->post('/broadcasting/auth', function (Request $request) {
    return Broadcast::auth($request);
})->name('app.broadcasting.auth');
