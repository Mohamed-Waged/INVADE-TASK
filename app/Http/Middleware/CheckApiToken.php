<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use JWTAuth;
use App\Models\User;
use ResponseHelper;

class CheckApiToken
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
        if (!empty(trim($request->header('Authorization')))){
            $isExists = User::where('id' , auth('api')->id())->exists();
            $tokenNotExpire = DB::table('personal_access_tokens')
                                        ->where('tokenable_type', 'Bearer')
                                        ->where('tokenable_id', auth('api')->id())
                                        ->where('token', JWTAuth::getToken())
                                        ->first();
            if ($tokenNotExpire){
                return $next($request);
            }
        }
        
        $data = ['message' => trans('auth.unauthorized')];
        return response()->json(['data' => $data], 401);
    }
}
