<?php

namespace App\Http\Controllers\Backend;

use DB;
use JWTAuth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LogoutRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\AuthBackendResource;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        $token = auth('api')->attempt($credentials);

        if (!$token) {
            $data = ['message' => 'Unauthorized'];
            return response()->json(['data' => $data], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user                       = new User;
        $user->name                 = $request->name;
        $user->email                = strtolower($request->email);
        $user->password             = bcrypt($request->password);
        $user->save();

        $data = ['message' => trans('auth.userRegistered')];
        return response()->json(['data' => $data], 200);
    }
    public function logout(LogoutRequest $request): JsonResponse
    {
        try {
            JWTAuth::invalidate($request->accessToken);
            $data = ['message' => trans('auth.userLoggedOut')];
            return response()->json(['data' => $data]);
        } catch (JWTException $exception) {
            $data = ['message' => trans('auth.userLoggedOut')];
            return response()->json(['data' => $data], 503);
        }
    }
    protected function respondWithToken($token): JsonResponse
    {
        $user = auth('api')->user();
        $user['token'] = $token;

        // uncomment the line below if you want to single user per single device
        // DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        // register accessToken JWT in DB till they fix it, by deleteing or expire old tokens
        DB::table('personal_access_tokens')
                        ->insert([
                            'tokenable_type' => 'Bearer', 
                            'tokenable_id'   => $user->id, 
                            'ip_address'     => request()->ip(),
                            'token'          => $token,
                            'last_used_at'   => Carbon::now()
                        ]);

        return response()->json([
            'data' => new AuthBackendResource($user)
        ]);
    }
}
