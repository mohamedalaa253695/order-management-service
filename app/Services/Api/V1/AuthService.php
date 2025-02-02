<?php

namespace App\Services\Api\V1;

use App\Interfaces\Api\V1\AuthServiceInterface;
use App\Repositories\Api\V1\AuthRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class AuthService implements AuthServiceInterface
{
    protected AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        return $this->authRepository->createUser($data);
    }

    /**
     * @throws AuthenticationException
     */
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid credentials');
        }

        $user = Auth::user();
//        dd($user->createToken('Personal Access Token')->accessToken);
        return $user->createToken('Personal Access Token')->accessToken;
    }

    public function logout($user)
    {
        $user->token()->revoke();
    }
}
