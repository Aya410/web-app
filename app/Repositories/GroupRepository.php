<?php

namespace App\Repositories;
use App\Models\File;
use App\Models\Version;

use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\group_user;
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

    public function getFilesWithVersionsByGroupId(int $groupId)
    {
        // Retrieve files that belong to the specified group_id
        return File::where('group_id', $groupId)
            ->with(['versions' => function($query) {
         
                $query->select('file', 'number', 'id', 'file_id'); 
            }])
            ->get(['id', 'name']);
    }
    /*


    public function getPendingGroupsForUser($userId)
    {
        // Retrieve groups where the user_id is the authenticated user and request_join is null
        return group_user::where('user_id', $userId)
            ->whereNull('request_join')
            ->with(['group' => function($query) {
                $query->select('id', 'name', 'description'); // Select only necessary columns
            }])
            ->get()
            ->pluck('group'); // Get only the group details
    }*/

    public function getPendingGroupsForUser($userId)
    {
        return group_user::where('user_id', $userId)
            ->whereNull('request_join')
            ->with(['group' => function ($query) {
                $query->select('id', 'name', 'description', 'admin_id', 'created_at')
                    ->with(['admin.user' => function ($userQuery) {
                        $userQuery->select('id', 'name'); // Get only the user's name
                    }]);
            }])
            ->get()
            ->map(function ($groupUser) {
                $group = $groupUser->group;
                
                // Extract required fields including admin's name and formatted date
                return [
                    'id'=>$groupUser->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'admin_name' => $group->admin->user->name ?? 'Unknown',
                    'created_date' => $group->created_at->format('Y-m-d H:i:s'), // Format with date and time

                ];
            });
    }


    public function updateRequestJoin($groupUserId, $requestJoin)
    {
        $groupUser = group_user::findOrFail($groupUserId); 
        $groupUser->request_join = $requestJoin;
        $groupUser->save(); 
    
        return $groupUser; 
    }


    
}
