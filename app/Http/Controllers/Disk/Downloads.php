<?php

namespace App\Http\Controllers\Disk;

use App\Models\DiskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Downloads extends Disk
{
    /**
     * Подготовка файла для скачивания
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $id)
    {
        $request->id = $this->linkToDec($id);

        if (!$file = DiskFile::find($request->id))
            return response()->json(['message' => "Файл не найден или уже удалён"], 404);

        $token = base64_encode(json_encode([
            'user_id' => $request->user()->id,
            'token_id' => ($request->user()->token()->id ?? ""),
        ]));

        $url = asset("file/{$id}") . "?access=" . urlencode($token);

        return response()->json([
            'file' => $file,
            'url' => $url,
        ]);
    }

    /**
     * Вывод файла на скачивание
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function file(Request $request, $id)
    {
        $id = $this->linkToDec($id);

        if (!$file = DiskFile::find($id))
            return abort(404);

        $path = Upload::getPath(env("DRIVE_DIR", "drive"), $file->dir, $file->file_name);

        if ($file->user_id != $request->user()->id)
            abort(403);

        ini_set("memory_limit", "2G");

        return Storage::download($path, $file->name);
    }
}
