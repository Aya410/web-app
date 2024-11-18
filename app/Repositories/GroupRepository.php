<?php

namespace App\Repositories;
use App\Models\File;
use App\Models\Version;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

use App\Models\group_user;
use App\Models\User;

class GroupRepository
{
    public function create(array $data)
    {
        return Group::create($data);
    }


    public function getAllUsers()
    {
        return User::all(); // Retrieve all users from the User model
    }
/*
    public function getRequestedGroupsForUser()
    {
        $userId = Auth::id();

        return Group::whereHas('groupUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('request_join', 1);
            })
            ->select('id','name', 'description', 'photo')
            ->get();
    }*/
    public function getRequestedGroupsForUser()
    {
        $userId = Auth::id();
    
        return Group::whereHas('groupUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('request_join', 1);
            })
            ->select('id', 'name', 'description', 'photo')
            ->get()
            ->map(function ($group) {
                // Check if the photo exists and generate the full URL path
                if ($group->photo) {
                    // Prepend the 'pblic' URL to the photo path
                    $group->photo = url($group->photo);
                }
                return $group;
            });
    }
    
    

    public function createFile(array $data)
    {
        return File::create($data);
    }

    public function createVersion(array $data)
    {
        return Version::create($data);
    }
/*
    public function getFilesWithVersionsByGroupId(int $groupId)
    {
        // Retrieve files that belong to the specified group_id
        return File::where('group_id', $groupId)
            ->with(['versions' => function($query) {

                $query->select('file', 'number', 'id', 'file_id');
            }])
            ->get(['id', 'name']);
            
    }
*/
public function getFilesWithVersionsByGroupId(int $groupId)
{
    return File::where('group_id', $groupId)
        ->with(['versions' => function($query) {
            $query->select('file', 'number', 'id', 'file_id');
        }])
        ->select('id', 'name')  // Only select the 'id' and 'name' columns from the 'files' table
        ->get()
        ->map(function ($file) {
            // Map over the versions to generate full URLs
            $file->versions = $file->versions->map(function ($version) {
                if ($version->file) {
                    // Use the full URL for the file, assuming it's stored publicly in the 'files' folder
                    $version->file = url( $version->file);
                }
                return $version;
            });

            return $file;
        });
}


    public function getPendingGroupsForUser($userId)
    {
        return group_user::where('user_id', $userId)
            ->whereNull('request_join')
            ->with(['group' => function ($query) {
                $query->select('id', 'name', 'description','photo', 'admin_id', 'created_at')
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
                    'admin_name' => $group->admin->user->name,
                    'photo'=> url($group->photo),
                    'created_date' => $group->created_at->format('Y-m-d H:i:s'), // Format with date and time

                ];
            });
    }

    public function updateRequestJoin($groupUserId, $requestJoin)
    {
        
        $groupUser = group_user::findOrFail($groupUserId);
        
        if ($requestJoin == 0) {
            $groupUser->delete();
            return [
                'status' => 'success',
                'message' => 'Request deleted successfully'
            ];
        }
        
        $groupUser->request_join = $requestJoin;
        $groupUser->save();
        
        return $groupUser; 
    }
    


}
