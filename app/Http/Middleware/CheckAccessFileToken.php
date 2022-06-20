<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Token;

class CheckAccessFileToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $access = optional(
            json_decode(base64_decode($request->access))
        );

        if (!$token = Token::whereId($access->token_id)->whereUserId($access->user_id)->first())
            abort(404);

        $request->setUserResolver(function () use ($token) {
            return User::find($token->user_id);
        });

        return $next($request);
    }
}
