<?php

namespace App\Http\Controllers\Disk;

use App\Models\DiskFile;
use Intervention\Image\Facades\Image as FacadesImage;
use Intervention\Image\Image;
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
     * Наименование каталога с миниатюрами
     * 
     * @var string
     */
    protected $thumb = "thumbs";

    /**
     * Возвращает путь до каталога с файлом
     * 
     * @param  string $dir
     * @return string
     */
    public function getDirPath($dir)
    {
        return env("DRIVE_DIR", "drive") . "/" . $dir;
    }

    /**
     * Проверяет и выводить пути до файлов миниатюр
     * 
     * @param  \App\Models\DiskFile $row
     * @param  null|string $extension Расширение файла миниатюры
     * @return object
     */
    public function getThumbsPaths(DiskFile $row, $extension = null)
    {
        $path = $this->getDirPath($row->dir);
        $path_thumbs = $this->implode($path, $this->thumb);

        $litle_name = $this->getThumbName($extension ?: $row->ext);
        $litle_path = $this->implode($path_thumbs, $litle_name);

        while (Storage::exists($litle_path)) {
            $litle_name = $this->getThumbName($extension ?: $row->ext);
            $litle_path = $this->implode($path_thumbs, $litle_name);
        }

        $middle_name = $this->getThumbName($extension ?: $row->ext);
        $middle_path = $this->implode($path_thumbs, $middle_name);

        while (Storage::exists($middle_path)) {
            $middle_name = $this->getThumbName($extension ?: $row->ext);
            $middle_path = $this->implode($path_thumbs, $middle_name);
        }

        Storage::makeDirectory($path_thumbs);

        return (object) [
            'path' => $path,
            'path_file' => $this->implode($path, $row->file_name),
            'path_thumbs' => $path_thumbs,
            'litle_name' => $litle_name,
            'litle_path' => $litle_path,
            'middle_name' => $middle_name,
            'middle_path' => $middle_path,
            'full_path_file' => Storage::path($this->implode($path, $row->file_name)),
            'full_path_litle' => Storage::path($litle_path),
            'full_path_middle' => Storage::path($middle_path),
        ];
    }

    /**
     * Преобразует массив в путь
     * 
     * @param  array $paths
     * @return string
     */
    public function implode(...$paths)
    {
        return implode("/", $paths);
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

    /**
     * Преобразовывает изображение по параметрам
     * 
     * @param  \Intervention\Image\Image|string $image  Исходное изображение
     * @param  string $path  Путь конечного изображения
     * @param  null|int $type  Тип миниатюры (`litle`, `middle`)
     * @param  int $quality  Качество сжатия
     * @return \Intervention\Image\Image
     */
    public function resize($image, $path, $type = "litle", $quality = 60)
    {
        $image = $image instanceof Image ? $image : FacadesImage::make($image);

        $params = $this->getImageParams($image);

        $width = $params[$type]['width'] ?? null;
        $height = $params[$type]['height'] ?? null;

        $thumb = $image->resize($width, $height ?? null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $thumb->save($path, $quality);
    }

    /**
     * Определяет параметры для миниатюр
     * 
     * @param  \Intervention\Image\Image $image
     * @return array
     */
    public function getImageParams(Image $image)
    {
        $exif = $image->exif('COMPUTED');

        $w = $exif['Width'] ?? null;
        $h = $exif['Height'] ?? null;

        $params['litle']['width'] = null;
        $params['litle']['height'] = null;
        $params['middle']['width'] = null;
        $params['middle']['height'] = null;

        /** Определение ширины и высоты */
        if ($w !== null && $h !== null) {

            if ($w >= $h) {
                $params['litle']['width'] = $this->litle;
                $params['middle']['width'] = $this->middle > $w ? $w : $this->middle;
            } else {
                $params['litle']['height'] = $this->litle;
                $params['middle']['height'] = $this->middle > $h ? $h : $this->middle;
            }
        } else {
            $params['litle']['width'] = $this->litle;
            $params['middle']['Width'] = $this->middle;
        }

        return $params;
    }
}
