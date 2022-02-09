<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->post('/', function (Request $request) {
    return $request->user();
})->name('users');

Route::post('registration', 'Users\Registration')->name('user.registration');

Route::post('login', 'Users\Login')->name('user.login');

Route::post('logout', 'Users\Logout')->middleware('auth:api')->name('user.logout');
