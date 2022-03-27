<?php

namespace App\Http\Controllers\Disk;

use App\Http\Controllers\Controller;
use App\Models\DiskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Upload extends Controller
{
    /**
     * Загрузка файла
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        if (!$file = DiskFile::find($request->id)) {

            $ext = pathinfo($request->name)['extension'] ?? null;

            $file = DiskFile::create([
                'user_id' => $request->user()->id,
                'dir' => "drive/" . date("Y/m/d/H"),
                'file_name' => Str::orderedUuid() . ($ext ? "." . $ext : ""),
                'name' => $request->name,
                'size' => $request->size,
                'ext' => $ext,
                'mime_type' => $request->type,
                'is_uploads' => true,
                'last_modified' => $request->date ?: now(),
            ]);

            $this->create($file);
        }

        $chunk = base64_decode($request->chunk);
        $path = $this->getPath($file->dir, $file->file_name);

        $put = $this->putChunk($path);
        $put->send($chunk);

        $size = Storage::size($path);
        $uploaded = $size >= $file->size;

        if ($uploaded)
            $file->is_uploads = false;

        $file->save();

        return response()->json([
            'file' => $file,
            'size' => $size,
            'uploaded' => $uploaded,
        ]);
    }

    /**
     * Генератор записи части файла
     * 
     * @param  string $path Путь до файла относительно каталога `/storage/app`
     * @return \Generator
     */
    public function putChunk($path)
    {
        $f = fopen(storage_path("app/{$path}"), 'a');

        while (true) {
            $line = yield;
            fwrite($f, $line);
        }
    }

    /**
     * Создание пустого файла
     * 
     * @param  \App\Models\DiskFile $file
     * @return bool
     */
    public function create(DiskFile $file)
    {
        $path = $this->getPath($file->dir, $file->file_name);

        while (Storage::exists($path)) {

            $file->file_name = Str::orderedUuid() . ($file->ext ? "." . $file->ext : "");
            $path = $this->getPath($file->dir, $file->file_name);
        }

        $file->save();

        return Storage::put($path, "");
    }

    /**
     * Возвращает путь до файла
     * 
     * @param  array $parts
     * @return string
     */
    public function getPath(...$parts)
    {
        return Str::replace("//", "/", implode("/", $parts));
    }
}
