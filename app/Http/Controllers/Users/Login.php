<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = $request->only('email', 'password');

        if (!Auth::attempt($data))
            return response()->json(['message' => "Ошибка авторизации"], 401);

        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'token' => $user->createToken(config('app.name'))->accessToken,
        ]);        
    }
}
