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

Route::get('file/{id}', 'Disk\Downloads@file')
    ->middleware(\App\Http\Middleware\CheckAccessFileToken::class);

Route::get('login', function () {
    return abort(404);
})->name('login');

/** Вывод миниатюр */
Route::get('thumbs/{a?}/{b?}/{c?}/{d?}/{e?}', 'Disk\Thumbs@view');

Route::get('/ip', function (\Illuminate\Http\Request $request) {
    return response([
        'ip' => $request->ip(),
        'remote_addr' => $request->server('REMOTE_ADDR'),
        'headers' => $request->header(),
    ]);
});
