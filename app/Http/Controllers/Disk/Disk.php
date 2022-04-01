<?php

namespace App\Http\Controllers\Disk;

use App\Http\Controllers\Controller;
use App\Models\DiskFile;
use App\Models\DiskMainDir;
use Illuminate\Http\Request;

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
     * Выводит идентификатор клавного каталога пользователя
     * 
     * @param int $id
     * @return int
     */
    public static function getUserMainDirId($id)
    {
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
}
