<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Telegram\Commands\Number;
use Illuminate\Support\Str;

class Commands
{
    /**
     * Список комманд
     * 
     * @var array
     */
    protected $commands = [
        'number' => Number::class,
    ];

    /**
     * Запуск комнд
     * 
     * @param  array $data
     * @return array
     */
    public function start($data)
    {
        $text = $data['message']['text'] ?? "";
        $entities = $data['message']['entities'] ?? [];
        $chat_id = $data['message']['chat']['id'] ?? null;

        $commands = [];

        foreach ($entities as $row) {
            if ($row['type'] == "bot_command") {
                $commands[] = Str::replace("/", "", Str::substr($text, $row['offset'] ?? 0, $row['length'] ?? 0));
            }
        }

        foreach ($commands as $command) {
            if (isset($this->commands[$command])) {
                $results[] = [
                    'command' => $command,
                    'response' => (new $this->commands[$command])->run($text, $chat_id),
                ];
            }
        }

        return $results ?? [];
    }
}
