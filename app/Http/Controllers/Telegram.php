<?php

namespace App\Http\Controllers;

use App\Models\TelegramIncoming;
use Illuminate\Http\Request;

class Telegram extends Controller
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
        $row->chat_id = $data['chat']['id'] ?? null;
        $row->from_id = $data['from']['id'] ?? null;
        $row->username = $data['from']['username'] ?? null;
        $row->request_data = encrypt($data);

        $row->save();

        return response()->json([
            'message' => "Request accepted",
        ]);
    }
}
