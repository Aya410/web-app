<?php

namespace App\Repositories;
use App\Models\File;
use App\Models\Version;

use Illuminate\Support\Facades\Auth;
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

    public function getRequestedGroupsForUser()
    {
        $userId = Auth::id();

        return Group::whereHas('groupUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('request_join', 1);
            })
            ->select('id','name', 'description')
            ->get();
    }


    public function createFile(array $data): File
    {
        return File::create($data);
    }

    public function createVersion(array $data): Version
    {
        return Version::create($data);
    }
}
