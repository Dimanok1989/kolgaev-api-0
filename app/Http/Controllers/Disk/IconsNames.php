<?php

namespace App\Http\Controllers\Disk;

class IconsNames
{
    /**
     * Массив данных
     * 
     * @var array
     */
    protected static $icons = [
        '.jpg' => "image",
        '.jpeg' => "image",
        '.svg' => "image",
        '.png' => "image",
        '.bmp' => "image",
        '.mov' => "video",
        '.avi' => "video",
        '.mp4' => "video",
        '.webm' => "video",
        '.mkv' => "video",
        '.m4v' => "video",
        '.m2t' => "video",
        '.zip' => "zip",
        '.xz' => "zip",
        '.bz2' => "zip",
        '.rar' => "rar",
        '.txt' => "txt",
        '.rtf' => "docx",
        '.doc' => "docx",
        '.docx' => "docx",
        '.xls' => "xls",
        '.xlsx' => "xls",
        '.csv' => "xls",
        '.mp3' => "audio",
        '.wav' => "audio",
        '.ogg' => "audio",
        '.pdf' => "pdf",
        '.php' => "code",
        '.xml' => "code",
        '.vue' => "code",
        '.sql' => "code",
        '.js' => "js",
        '.css' => "css",
        '.html' => "html",
        '.exe' => "exe",
        '.msi' => "exe",
        '.7z' => "sevez",
    ];

    /**
     * Выводит наименование иконки
     * 
     * @param string $ext Расширение файла
     * @return string
     */
    public static function get($ext)
    {
        $ext = strtolower($ext);

        if (isset(self::$icons["." . $ext]))
            return self::$icons["." . $ext];

        if (isset(self::$icons[$ext]))
            return self::$icons[$ext];

        return "file";
    }
}
