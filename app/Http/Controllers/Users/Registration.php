<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Registration extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Controllers\Users\RegistrationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(RegistrationRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auth::attempt([
        //     'email' => $request->email,
        //     'password' => $request->password
        // ]);

        return response()->json([
            'message' => "Регистрация успешно завершена"
        ]);
    }
}
