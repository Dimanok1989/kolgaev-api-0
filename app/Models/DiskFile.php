<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'dir',
        'file_name',
        'name',
        'size',
        'ext',
        'mime_type',
        'is_dir',
        'is_hide',
        'is_uploads',
        'last_modified',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_dir' => 'boolean',
        'is_hide' => 'boolean',
        'is_uploads' => 'boolean',
        'last_modified' => 'datetime',
    ];

    /**
     * File-to-Directory Relationship
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(DiskFile::class, 'disk_file_to_dir', 'dir_id', 'file_id');
    }
}
