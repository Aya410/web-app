<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\User;
class GroupRepository
{
    public function create(array $data): Group
    {
        return Group::create($data);
    }
    public function getAllUsers()
    {
        return User::all(); // Retrieve all users from the User model
    }
}
