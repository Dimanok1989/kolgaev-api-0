<?php

use Illuminate\Support\Facades\Route;

/** Главная страница диска */
Route::post('/', 'Disk\Disk@index');
