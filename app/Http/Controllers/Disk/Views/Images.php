<?php

namespace App\Http\Controllers\Disk\Views;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Disk\Disk;
use App\Http\Controllers\Disk\Thumbs\Images as ThumbsImages;
use App\Models\DiskFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Images extends Controller
{
    /**
     * Проверка доступа к фотокарточке
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\App\Models\DiskFile
     */
    public function check(Request $request)
    {
        $folder_id = is_string($request->input('folder')) ? $this->linkToDec($request->input('folder')) : null;

        if ($folder_id === 0)
            $folder_id = Disk::getUserMainDirId($request->user()->id);

        if (!$this->dir = DiskFile::find($folder_id))
            return response()->json(['message' => "Каталог с фотокарточкой не найден"], 404);

        if (!$row = $this->dir->files()->where('id', $this->linkToDec($request->id))->first())
            return response()->json(['message' => "Фотокарточка не найдена или удалена"], 404);

        if ($row->user_id != $request->user()->id)
            return response()->json(['message' => "Доступ к фотокарточке ограничен"], 400);

        return $row;
    }

    /**
     * Выводит данные для просмотра фотокарточки
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $row = $this->check($request);

        if ($row instanceof JsonResponse)
            return $row;

        return response()->json([
            'id' => $request->id,
            'folder' => $request->folder,
            'prev' => $this->getPrevId($row->name),
            'next' => $this->getNextId($row->name),
        ]);
    }

    /**
     * Определяет индентификатор следующего фото
     * 
     * @param  string $name
     * @return null|string
     */
    public function getNextId($name)
    {
        $this->next = $row = $this->dir->files()
            ->select('id')
            ->where('name', '>=', $name)
            ->whereIn('mime_type', ThumbsImages::mimeTypes())
            ->where('thumb_at', '!=', null)
            ->orderBy('name')
            ->limit(2)
            ->get();

        return isset($row[1]->id) ? $this->decToLink($row[1]->id) : null;
    }

    /**
     * Определяет индентификатор предыдущего фото
     * 
     * @param  string $name
     * @return null|string
     */
    public function getPrevId($name)
    {
        $row = $this->dir->files()
            ->select('id')
            ->where('name', '<=', $name)
            ->whereIn('mime_type', ThumbsImages::mimeTypes())
            ->where('thumb_at', '!=', null)
            ->orderBy('name', "DESC")
            ->limit(2)
            ->get();

        return isset($row[1]->id) ? $this->decToLink($row[1]->id) : null;
    }

    /**
     * Выводит данные для просмотра фотокарточки
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function photo(Request $request)
    {
        $row = $this->check($request);

        if ($row instanceof JsonResponse)
            return $row;

        $path = env("DRIVE_DIR", "drive") . "/" . $row->dir . "/thumbs/" . $row->thumb_middle;

        if (!Storage::exists($path))
            return response()->json(['message' => "Запрашиваемый файл фотокарточки физически отсутствует на сервере"], 404);

        return response()->file(Storage::path($path));
    }
}
