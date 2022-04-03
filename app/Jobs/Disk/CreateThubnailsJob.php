<?php

namespace App\Jobs\Disk;

use App\Http\Controllers\Disk\Thumbs\Images;
use App\Http\Controllers\Disk\Thumbs\Videos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateThubnailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\DiskFile $file
     * @return void
     */
    public function __construct(
        public $file
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (in_array($this->file->mime_type, Images::mimeTypes())) {
            $thumb = new Images;
        } else if (Str::startsWith($this->file->mime_type, 'video/')) {
            $thumb = new Videos;
        }

        $thumb->create($this->file);
    }
}
