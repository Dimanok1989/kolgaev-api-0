<?php

namespace App\Http\Controllers\Telegram;

use Kolgaev\TelegramBot\Telegram;

class Handler extends Telegram
{
    /**
     * Инициализация объекта
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct(env("TELEGRAM_BOT_TOKEN_FOR_DATA"));
    }

    /**
     * Вызов пустого метода
     */
    public function __call($name, $arguments)
    {
        return null;
    }
}
