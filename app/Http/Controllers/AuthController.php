<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['register','login']),
        ];
    }

    public function __construct(
        public UserService $userService
    ){}

    public function register(RegisterUserRequest $request)
    {
        $user = $this->userService->create($request->validated());
        return $this->sendResponse(Response::HTTP_CREATED, ['success'=>true,'message'=>'User Registered Successfully', 'user' => new UserResource($user)]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    
    public function login(LoginUserRequest $request)
    {
        $token = $this->userService->login(...$request->validated());
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'Customer logged in successfully', 'data' => $this->prepareToken($token)]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'User Retrieved Successfully', 'user' => new UserResource($user)]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            auth()->logout(true);
            return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'Successfully logged out']);
        } catch (JWTException $e) {
            Log::error($request->fullUrl(), [$e->getMessage()]);
            return $this->sendResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = auth()->refresh();
            return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'Token successfully refreshed', 'data' => $this->prepareToken($token)]);
        } catch (JWTException $e) {
            Log::error($request->fullUrl(), [$e->getMessage()]);
            return $this->sendResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
        ];
    }
}
