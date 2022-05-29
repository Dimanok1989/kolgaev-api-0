<?php

namespace App\Http\Controllers\Disk;

use App\Http\Controllers\Controller;
use App\Models\DiskFile;
use App\Models\DiskMainDir;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Disk extends Controller
{
    /**
     * Загрузка данных файлообменника
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json([
            'main_dir' => $this->decToLink($this->getUserMainDirId($request->user()->id)),
        ]);
    }

    /**
     * Определеяет идентификатор каталога по текстовому идентификатору
     * 
     * @param  string|null $id
     * @return int
     */
    public static function getFolderId($id = null)
    {
        $ind = $id ? parent::linkToDec($id) : null;

        return $ind ?: self::getUserMainDirId(optional(request()->user())->id);
    }

    /**
     * Определяет идентификатор каталога по пути
     * 
     * @param  string $path
     * @return int
     */
    public static function getFolderIdFromPath($path)
    {
        $path = self::getPathFolder($path);

        return self::getFolderId(
            end($path)
        );
    }

    /**
     * Определяет идентификаторы каталогов в пути
     * 
     * @param  string $path
     * @return array
     */
    public static function getPathFolder($path)
    {
        return explode("/", $path);
    }

    /**
     * Выводит идентификатор главного каталога пользователя
     * 
     * @param  null|int $id
     * @return null|int
     */
    public static function getUserMainDirId($id = null)
    {
        $id = $id ?: optional(request()->user())->id;

        if (!$id)
            return null;

        if ($row = DiskMainDir::whereUserId($id)->first())
            return $row->disk_file_id;

        $dir = DiskFile::create([
            'user_id' => $id,
            'name' => "system_dir_user_" . $id,
            'is_dir' => true,
            'is_hide' => true,
        ]);

        DiskMainDir::create([
            'user_id' => $id,
            'disk_file_id' => $dir->id,
        ]);

        return $dir->id;
    }

    /**
     * Формирует хлебные крошки
     * 
     * @param  string|array $path
     * @return array
     */
    public function getBreadCrumbs($path)
    {
        if (is_string($path))
            $path = $this->getPathFolder($path);

        foreach ($path as &$dir)
            if ($dir = $this->getRowBreadCrumbs($this->getFolderId($dir)))
                $paths[] = $dir;

        return $paths ?? [];
    }

    /**
     * Формирует массив хлебной крошки
     * 
     * @param  int|null $id
     * @return array|null
     */
    public function getRowBreadCrumbs($id = null)
    {
        if (!$row = DiskFile::find($id))
            return null;

        if (Str::of($row->name)->is('system_dir_user_*'))
            $row->name = "Файлы";

        $row->link = $this->decToLink($row->id);

        return $row->only(
            'id',
            'name',
            'link'
        );
    }
}
