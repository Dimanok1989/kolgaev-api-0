<?php

namespace App\Http\Controllers\Disk;

use App\Http\Controllers\Controller;
use App\Models\DiskDir;
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
            'main_dir' => $this->getUserMainDirId($request->user()->id),
        ]);
    }

    /**
     * Выводит идентификатор клавного каталога пользователя
     * 
     * @param int $id
     * @return int
     */
    public static function getUserMainDirId($id)
    {
        return DiskDir::whereUserId($id)->firstOrCreate([
            'user_id' => $id,
            'is_main' => true,
        ])->id;
    }
}
