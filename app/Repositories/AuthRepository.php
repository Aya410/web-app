<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthRepository
{

    public function register(array $info)
    {
        $info['password'] = Hash::make($info['password']);

        return User::create($info);
    }
}
