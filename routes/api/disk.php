<?php

use Illuminate\Support\Facades\Route;

/** Главная страница диска */
Route::get('/', 'Disk\Disk@index');

/** Файлы пользователя */
Route::get('files', 'Disk\Files@index');

/** Выводит информацию о файле */
Route::get('file', 'Disk\Files@get');

/** Смена имени файла */
Route::post('file/rename', 'Disk\Files@rename');

/** Удаление файла */
Route::delete('file/delete', 'Disk\Files@delete');

/** Загрузка файла */
Route::put('upload', 'Disk\Upload');

/** Создание каталога */
Route::post('folder/create', 'Disk\Files@createFolder');

/** Выводит данные для просмотра фотокарточки */
Route::post('view/image', 'Disk\Views\Images@get');

/** Выводит фотокарточку */
Route::get('photo', 'Disk\Views\Images@photo');