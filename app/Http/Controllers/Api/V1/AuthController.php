<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Interfaces\Api\V1\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResource
     */
    public function register(RegisterRequest $request): JsonResource
    {
        $user = $this->authService->register($request->validated());
        return ResponseHelper::returnCreatedResource(new UserResource($user), 'User registered successfully');
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());
//        dd($token);
        return ResponseHelper::returnData(['token' => $token], 'Login successful');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return ResponseHelper::returnSuccessMessage('Successfully logged out');
    }

}
