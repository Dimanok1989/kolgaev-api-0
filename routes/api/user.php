<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'User\Data')->middleware('auth:api')->name('user');

Route::post('registration', 'User\Registration')->name('user.registration');

Route::post('login', 'User\Login')->name('user.login');

Route::post('logout', 'User\Logout')->middleware('auth:api')->name('user.logout');
