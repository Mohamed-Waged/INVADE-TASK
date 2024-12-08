<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!auth()->guard('api')->check()) {
            $data = ['message' => 'Unauthorized.'];
            return response()->json(['data' => $data], 401);
        }

        return $next($request);
    }
}
