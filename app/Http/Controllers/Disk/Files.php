<?php

namespace App\Http\Controllers\Disk;

use App\Events\Disk\UpdateFile;
use App\Http\Controllers\Disk\Thumbs\Images;
use App\Models\DiskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SplFileInfo;

class Files extends Disk
{
    /**
     * Список файлов пользователя
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $dir_id = $this->getFolderIdFromPath($request->dir ?: "");

        $dir = DiskFile::find($dir_id);

        if (!($dir->is_dir ?? null))
            return response()->json(['message' => "Каталог с файлами не найден или был удален"], 404);

        $files = $dir->files()
            ->orderBy('is_dir', 'DESC')
            ->orderBy('name')
            ->get()
            ->map(function ($row) {
                return $this->serialize($row);
            });

        return response()->json([
            'dir' => $dir_id,
            'files' => $files,
            'page' => $request->page ?: 1,
            'breadcrumbs' => $request->dir ? $this->getBreadCrumbs($request->dir) : [],
        ]);
    }

    /**
     * Выводит информацию о файле
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @todo Дополнить проверку права доступа к файлу
     */
    public function get(Request $request)
    {
        if (!$row = DiskFile::find($request->id))
            return response()->json(['message' => "Файл не найден или уже удалён"], 400);

        return response()->json([
            'row' => $this->serialize($row),
        ]);
    }

    /**
     * Формирование строки файла
     * 
     * @param  \App\Models\DiskFile $row
     * @return array
     */
    public function serialize(DiskFile $row)
    {
        $dir = $row->dir;

        if ($row->dir)
            $row->dir = env("DRIVE_DIR", "drive") . "/" . $row->dir;

        if ($row->thumb_litle) {

            $row->thumb_litle_url = env("APP_URL") . "/thumbs/{$dir}/{$row->thumb_litle}";

            $img = Storage::path("{$row->dir}/thumbs/{$row->thumb_litle}");

            $imageSize = getimagesize($img);
            $imageData = base64_encode(file_get_contents($img));

            $row->thumb_litle_url = "data:{$imageSize['mime']};base64,{$imageData}";
        }

        $row->icon = $row->is_dir ? "folder" : IconsNames::get($row->ext);

        $row->link = $this->decToLink($row->id);

        $row->is_video = $this->is_video($row->mime_type);
        $row->is_image = $this->is_image($row->mime_type);

        // $row->name_full = $row->name;

        // if ($row->ext) {
        //     $row->name = Str::replaceLast(".{$row->ext}", "", $row->name_full);
        // }

        return $row->toArray();
    }

    /**
     * Определяет по mime-типу является файл видеороликом
     * 
     * @param  string $mime_type
     * @return boolean
     */
    public static function is_video($mime_type)
    {
        return (bool) Str::startsWith((string) $mime_type, 'video/');
    }

    /**
     * Определяет по mime-типу является файл изображением
     * 
     * @param  string $mime_type
     * @return boolean
     */
    public static function is_image($mime_type)
    {
        return (bool) in_array($mime_type, Images::mimeTypes());
    }

    /**
     * Создание каталога
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @todo Добавить проверка общего доступа
     */
    public function createFolder(Request $request)
    {
        $dir_id = $this->getFolderIdFromPath($request->dir ?: "");

        if (!$dir = DiskFile::find($dir_id))
            return response()->json(['message' => "Каталог не найден или уже удален"], 400);

        if ($dir->user_id != $request->user()->id)
            return response()->json(['message' => "Доступ к каталогу ограничен"], 403);

        $name = $this->getUniqueFileName($dir, $request->name ?: "Новая папка", true);

        $file = DiskFile::create([
            'user_id' => $request->user()->id,
            'name' => $name,
            'is_dir' => true,
        ]);

        $dir->files()->attach($file->id);

        return response()->json([
            'file' => $this->serialize($file),
        ]);
    }

    /**
     * Проверяет и выдает уникальное имя файла в каталоге
     * 
     * @param  \App\Modles\DiskFile $dir
     * @param  string $name
     * @param  bool $is_dir
     * @return string
     */
    public static function getUniqueFileName(DiskFile $dir, $name, $is_dir = false)
    {
        $count = 1;

        $extension = (new SplFileInfo($name))->getExtension();
        $basename = $is_dir ? $name : Str::of($name)->replace(".{$extension}", "");

        while ((bool) $dir->files()->where('name', $name)->count()) {

            $name = "{$basename} ({$count})";

            if (!$is_dir and (bool) $extension)
                $name .= "." . $extension;

            $count++;
        }

        return $name;
    }

    /**
     * Смена имени файла
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(Request $request)
    {
        if (!$request->name)
            return response()->json(['message' => "Не указано имя файла"], 400);

        if (!$row = DiskFile::find($request->id))
            return response()->json(['message' => "Файл не найден или уже удалён"], 400);

        $row->name = $row->ext ? Str::finish($request->name, ".{$row->ext}") : $request->name;
        $row->save();

        $row = $this->serialize($row);

        broadcast(new UpdateFile($row))->toOthers();

        return response()->json([
            'row' => $row,
        ]);
    }
}
