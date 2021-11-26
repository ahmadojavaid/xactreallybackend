<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class Api
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token && $user = User::where('auth_token', $token)->first()) {

            auth()->login($user);
            return $next($request);

        } elseif ($token && $token == config('app.key')) {
            
            return $next($request);
        }

        return response()->json([
            'message' => 'Auth Token is missing or incorrect'
        ], 401);
    }
}
