<?php

namespace App\Http\Controllers\Disk;

use App\Http\Controllers\Controller;
use App\Models\DiskFile;
use Illuminate\Http\Request;

class Files extends Controller
{
    /**
     * Список файлов пользователя
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->dir = $request->dir ?: Disk::getUserMainDirId($request->user()->id);

        $dir = DiskFile::find($request->dir);

        if (!($dir->is_dir ?? null))
            return response()->json(['message' => "Каталг с файлами не найден или был удален"], 404);

        $files = $dir->files()
            ->orderBy('is_dir', 'DESC')
            ->get();

        return response()->json([
            'dir' => encrypt($request->dir),
            'files' => $files,
            'page' => $request->page ?: 1,
        ]);
    }
}
