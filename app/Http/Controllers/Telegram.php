<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Telegram\Commands;
use App\Jobs\TelegramWebHoockJob;
use App\Models\TelegramIncoming;
use Illuminate\Http\Request;

class Telegram
{
    /**
     * Приём входящих обращений
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request, $token)
    {
        $data = $request->all();

        $row = new TelegramIncoming;

        $row->token = $token;
        $row->chat_id = $data['message']['chat']['id'] ?? null;
        $row->from_id = $data['message']['from']['id'] ?? null;
        $row->username = $data['message']['from']['username'] ?? null;
        $row->request_data = encrypt($data);

        $row->save();

        TelegramWebHoockJob::dispatch($row);

        return response()->json([
            'message' => "Request accepted",
        ]);
    }

    /**
     * Поиск команд
     * 
     * @param  array $data
     * @return null
     */
    public static function run($data)
    {
        return (new Commands)->start($data);
    }
}
