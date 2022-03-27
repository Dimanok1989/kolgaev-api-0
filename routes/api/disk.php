<?php

use Illuminate\Support\Facades\Route;

/** Главная страница диска */
Route::get('/', 'Disk\Disk@index');

/** Файлы пользователя */
Route::get('files', 'Disk\Files@index');

/** Загрузка файла */
Route::put('upload', 'Disk\Upload');