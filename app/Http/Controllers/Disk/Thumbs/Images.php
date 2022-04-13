<?php

namespace App\Http\Controllers\Disk\Thumbs;

use App\Events\Disk\UpdateFile;
use App\Http\Controllers\Disk\Files;
use App\Http\Controllers\Disk\Thumbs;
use App\Models\DiskFile;

class Images extends Thumbs
{
    /**
     * Типы файлов которые будут преобразованы
     * 
     * @var array
     */
    protected static $mime_types = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    /**
     * Выводит список типов для конвертации
     * 
     * @return array
     */
    public static function mimeTypes()
    {
        return self::$mime_types;
    }

    /**
     * Создает миниатюры файла
     * 
     * @param \App\Models\DiskFile|null $row
     * @return boolean
     */
    public function create($row = null)
    {
        $row = $row ?: DiskFile::whereIn('mime_type', $this->mimeTypes())
            ->where([
                ['is_uploads', 0],
                ['thumb_at', null],
            ])
            ->first();

        if (!$row)
            return false;

        $thumbs = $this->getThumbsPaths($row);

        $this->resize($thumbs->full_path_file, $thumbs->full_path_litle, "litle");
        $this->resize($thumbs->full_path_file, $thumbs->full_path_middle, "middle");

        $row->thumb_litle = $thumbs->litle_name;
        $row->thumb_middle = $thumbs->middle_name;
        $row->thumb_at = now();

        $row->save();

        broadcast(new UpdateFile(
            (new Files)->serialize($row)
        ));

        return $row;
    }
}
