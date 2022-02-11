<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Registration extends Controller
{
    /**
     * Registers a new user.
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

        return response()->json([
            'message' => "Регистрация успешно завершена"
        ]);
    }
}
