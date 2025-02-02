<?php

namespace App\Repositories\Api\V1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }
}
