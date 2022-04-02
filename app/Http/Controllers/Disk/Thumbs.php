<?php

namespace App\Http\Controllers\Disk;

use App\Models\DiskFile;
use Illuminate\Http\Request;
use Intervention\Image\Image;
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
     * Типы файлов которые будут преобразованы
     * 
     * @var array
     */
    protected static $mime_types = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    /**
     * Ширина миниатюры
     * 
     * @var int
     */
    protected $litle = 100;

    /**
     * Ширина картинки для просмотра
     * 
     * @var int
     */
    protected $middle = 1920;

    /**
     * Выводит список типов для конвертации
     * 
     * @return array
     */
    public static function mimeTypes()
    {
        return self::$mime_types;
    }

    /**
     * Создает миниатюры файла
     * 
     * @param \App\Models\DiskFile|null $row
     * @return boolean
     */
    public function create($row = null)
    {
        $row = $row ?: DiskFile::whereIn('mime_type', $this->mimeTypes())
            ->where([
                ['is_uploads', 0],
                ['thumb_at', null],
            ])
            ->first();

        if (!$row)
            return false;

        $path = env("DRIVE_DIR", "drive") . "/" . $row->dir;
        Storage::makeDirectory($path . "/thumbs");

        $row->thumb_litle = $this->getThumbName($row->ext);

        while (Storage::exists("{$path}/thumbs/{$row->thumb_litle}"))
            $row->thumb_litle = $this->getThumbName($row->ext);

        $row->thumb_middle = $this->getThumbName($row->ext);

        while (Storage::exists("{$path}/thumbs/{$row->thumb_middle}"))
            $row->thumb_middle = $this->getThumbName($row->ext);

        $image = \Intervention\Image\Facades\Image::make(Storage::path($path . "/" . $row->file_name));

        $params = $this->getParams($image);

        $thumb_litle = Storage::path("{$path}/thumbs/{$row->thumb_litle}");

        $litle = $image->resize($params['litle']['whdth'], $params['litle']['height'], function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $litle->save($thumb_litle, 60);

        $thumb_middle = Storage::path("{$path}/thumbs/{$row->thumb_middle}");

        $middle = $image->resize($params['middle']['whdth'], $params['middle']['height'], function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $middle->save($thumb_middle, 60);

        $row->thumb_at = now();
        $row->save();

        return $row;
    }

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
     * Определяет параметры для миниатюр
     * 
     * @param  \Intervention\Image\Image $image
     * @return array
     */
    public function getParams(Image $image)
    {
        $exif = $image->exif('COMPUTED');

        $w = $exif['Width'] ?? null;
        $h = $exif['Height'] ?? null;

        $params['litle']['whdth'] = null;
        $params['litle']['height'] = null;
        $params['middle']['whdth'] = null;
        $params['middle']['height'] = null;

        /** Определение ширины и высоты */
        if ($w !== null && $h !== null) {

            if ($w >= $h) {
                $params['litle']['whdth'] = $this->litle;
                $params['middle']['whdth'] = $this->middle;
            } else {
                $params['litle']['height'] = $this->litle;
                $params['middle']['height'] = $this->middle;
            }
        } else {
            $params['litle']['whdth'] = $this->litle;
            $params['middle']['whdth'] = $this->middle;
        }

        return $params;
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
