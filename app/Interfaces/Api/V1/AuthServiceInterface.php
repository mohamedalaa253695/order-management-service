<?php

namespace App\Interfaces\Api\V1;

interface AuthServiceInterface
{
    public function register(array $data);
    public function login(array $credentials);
    public function logout($user);
}
