<?php

namespace App\Http\Controllers\Disk;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Необходимо создать символьную сылку на каталог с миниатюрами
 * 
 * Для Windows
 * mklink /D "[...]\storage\app\public\thumbs" "[...]\storage\app\drive\thumbs\litle"
 * 
 * Для Linux
 * ln -s [...]\storage\app\drive\thumbs\litle [...]\storage\app\public\thumbs
 * 
 * [...] - Каталог с проектом
 */
class Thumbs
{
    /**
     * Формирует наименование файла миниатюры
     * 
     * @param null|string $ext
     * @return string
     */
    public static function getThumbName($ext = null)
    {
        return Str::uuid() . ($ext ? Str::of($ext)->start('.') : "");
    }

    /**
     * Просмотр миниатюры
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  array $params
     * @return mixed
     */
    public function view(Request $request, ...$params)
    {
        $count = count($params) - 1;

        foreach ($params as $key => $path) {

            if ($count == $key)
                $paths[] = "thumbs";

            $paths[] = $path;
        }

        $path = env("DRIVE_DIR", "drive") . "/" . implode("/", $paths ?? []);

        if (!Storage::exists($path))
            return abort(404);

        return response()->file(Storage::path($path));
    }
}
