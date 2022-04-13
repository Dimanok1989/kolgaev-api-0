<?php

namespace App\Http\Controllers\Disk\Views;

use App\Http\Controllers\Controller;
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
        if (!$this->dir = DiskFile::find($this->linkToDec($request->folder)))
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
            'prev' => $this->getPrevId($row->id),
            'next' => $this->getNextId($row->id),
        ]);
    }

    /**
     * Определяет индентификатор следующего фото
     * 
     * @param  int $id
     * @return null|string
     */
    public function getNextId($id)
    {
        $row = $this->dir->files()
            ->where('id', '>', $id)
            ->orderBy('name')
            ->first();

        return isset($row->id) ? $this->decToLink($row->id) : null;
    }

    /**
     * Определяет индентификатор предыдущего фото
     * 
     * @param  int $id
     * @return null|string
     */
    public function getPrevId($id)
    {
        $row = $this->dir->files()
            ->where('id', '<', $id)
            ->orderBy('name', "DESC")
            ->first();

        return isset($row->id) ? $this->decToLink($row->id) : null;
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
