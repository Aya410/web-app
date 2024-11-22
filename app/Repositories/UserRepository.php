<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\group_user;
class UserRepository
{
    public function findByName(string $name)
    {
        // Perform the query to search for users by name
        return User::where('name', 'like', '%' . $name . '%')
            ->select('id', 'name', 'email') // Fetch only required columns
            ->get();
    }

    public function getUsersByGroupId($groupId)
    {
        return group_user::where('group_id', $groupId)
            ->where('request_join', 1) 
            ->join('users', 'group_users.user_id', '=', 'users.id')
            ->select('group_users.user_id', 'users.name')
            ->get();
    }
}
