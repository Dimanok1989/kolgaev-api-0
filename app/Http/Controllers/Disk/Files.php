<?php

namespace App\Http\Controllers\Disk;

use App\Http\Controllers\Controller;
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

        return response()->json([
            'dir' => $request->dir,
        ]);
    }
}
