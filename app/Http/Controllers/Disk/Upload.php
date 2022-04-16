<?php

namespace App\Http\Controllers\Disk;

use App\Events\Disk\NewFile;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Disk\Thumbs\Images;
use App\Jobs\Disk\CreateThubnailsJob;
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

            $dir = Disk::getFolderIdFromPath($request->dir);
            $name = Files::getUniqueFileName(DiskFile::find($dir), $request->name ?: "Новый файл");

            $file = DiskFile::create([
                'user_id' => $request->user()->id,
                'dir' => date("Y/m/d/H"),
                'file_name' => Str::orderedUuid() . ($ext ? "." . $ext : ""),
                'name' => $name,
                'size' => $request->size,
                'ext' => $ext,
                'mime_type' => $request->type,
                'is_uploads' => true,
                'last_modified' => $request->date ?: now(),
            ]);

            $this->create($file);
        }

        $chunk = base64_decode($request->chunk);
        $path = $this->getPath(env("DRIVE_DIR", "drive"), $file->dir, $file->file_name);

        $put = $this->putChunk($path);
        $put->send($chunk);

        $size = Storage::size($path);
        $file->is_uploads = !($size >= $file->size);

        $file->save();

        if (!$file->is_uploads) {

            $dir = $dir ?? Disk::getFolderIdFromPath($request->dir);

            $this->attach($dir, $file->id);

            broadcast(new NewFile(
                (new Files)->serialize($file),
                $request->dir
            ));

            if (in_array($file->mime_type, Images::mimeTypes()) or Files::is_video($file->mime_type)) {
                CreateThubnailsJob::dispatch($file);
            }
        }

        return response()->json([
            'file' => $file,
            'size' => $size,
            'uploaded' => $file->is_uploads,
        ]);
    }

    /**
     * Записать отношение файла к каталогу
     * 
     * @param int $dir_id
     * @param int $file_id
     * @return null
     */
    public function attach($dir_id, $file_id)
    {
        if (!$dir = DiskFile::find($dir_id))
            return null;

        $dir->files()->attach($file_id);
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
        $path = $this->getPath(env("DRIVE_DIR", "drive"), $file->dir, $file->file_name);

        while (Storage::exists($path)) {

            $file->file_name = Str::orderedUuid() . ($file->ext ? "." . $file->ext : "");
            $path = $this->getPath(env("DRIVE_DIR", "drive"), $file->dir, $file->file_name);
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
