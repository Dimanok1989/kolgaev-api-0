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
        $row = new TelegramIncoming;

        $row->token = $token;
        $row->request_data = encrypt($request->all());

        $row->save();

        return response()->json([
            'message' => "Request accepted",
        ]);
    }
}
