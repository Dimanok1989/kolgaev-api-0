<?php

use Illuminate\Support\Facades\Route;

/** Главная страница диска */
Route::get('/', 'Disk\Disk@index');

/** Файлы пользователя */
Route::get('files', 'Disk\Files@index');

/** Загрузка файла */
Route::put('upload', 'Disk\Upload');

/** Создание каталога */
Route::post('folder/create', 'Disk\Files@createFolder');

/** Выводит данные для просмотра фотокарточки */
Route::post('view/image', 'Disk\Views\Images@get');

/** Выводит фотокарточку */
Route::get('photo', 'Disk\Views\Images@photo');