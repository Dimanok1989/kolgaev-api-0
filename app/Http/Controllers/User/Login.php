<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    /**
     * User authorization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
