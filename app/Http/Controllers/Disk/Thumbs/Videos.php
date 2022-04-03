<?php

namespace App\Http\Controllers\Disk\Thumbs;

use App\Events\Disk\UpdateFile;
use App\Http\Controllers\Disk\Files;
use App\Http\Controllers\Disk\Thumbs;
use App\Models\DiskFile;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;

class Videos extends Thumbs
{
    /**
     * Создает миниатюры файла
     * 
     * @param \App\Models\DiskFile|null $row
     * @return boolean
     */
    public function create($row = null)
    {
        $row = $row ?: DiskFile::where('mime_type', 'LIKE', 'video/%')
            ->where([
                ['is_uploads', 0],
                ['thumb_at', null],
            ])
            ->first();

        if (!$row)
            return false;

        $thumbs = $this->getThumbsPaths($row, "jpg");

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => env('FFMPEG_BIN'), // the path to the FFMpeg binary
            'ffprobe.binaries' => env('FFPROBE_BIN'), // the path to the FFProbe binary
            'timeout'          => 3600, // the timeout for the underlying process
            'ffmpeg.threads'   => 12,   // the number of threads that FFMpeg should use
        ]);

        $video = $ffmpeg->open($thumbs->full_path_file);

        $duration = (float) $video->getFFProbe()
            ->streams($thumbs->full_path_file)
            ->videos()
            ->first()
            ->get('duration');

        $video->frame(TimeCode::fromSeconds($duration > 3 ? 3 : $duration / 2))
            ->save($thumbs->full_path_middle);

        $this->resize($thumbs->full_path_middle, $thumbs->full_path_litle);

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
