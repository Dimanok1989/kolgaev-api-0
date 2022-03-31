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
        $request->dir = $request->dir ? $this->linkToDec($request->dir) : null;
        $request->dir = $request->dir ?: Disk::getUserMainDirId($request->user()->id);

        $dir = DiskFile::find($request->dir);

        if (!($dir->is_dir ?? null))
            return response()->json(['message' => "Каталг с файлами не найден или был удален"], 404);

        $files = $dir->files()
            ->orderBy('is_dir', 'DESC')
            ->get()
            ->map(function ($row) {
                return $this->serialize($row);
            });

        return response()->json([
            'dir' => $request->dir,
            'files' => $files,
            'page' => $request->page ?: 1,
        ]);
    }

    /**
     * Формирование строки файла
     * 
     * @param \App\Models\DiskFile $row
     * @return \App\Models\DiskFile $row
     */
    public function serialize(DiskFile $row)
    {
        $row->icon = $row->is_dir ? "folder" : IconsNames::get($row->ext);

        $row->link = $this->decToLink($row->id);

        return $row;
    }
}
