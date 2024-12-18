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
    
    public function getFilesWithVersionsByGroupId(int $groupId)
    {
        
        $files = File::where('group_id', $groupId)
            ->where('request_join', 1)
            ->select('id', 'name')  
            ->with(['versions' => function ($query) {
                $query->select('id', 'file_id', 'file', 'number') 
                    ->orderBy('number', 'desc'); 
            }])
            ->get();

        $files->each(function ($file) {
            if ($file->versions->isNotEmpty()) {
                $latestVersion = $file->versions->first(); 
                $latestVersion->file = $latestVersion->file ? url($latestVersion->file) : null; 
                $file->latest_version = $latestVersion; 
            } else {
                $file->latest_version = null;  
            }
            unset($file->versions); 
        });
    
        return $files;  
    }
    
  /*
public function getFilesWithVersionsByGroupId(int $groupId)
{
    
    $file = File::where('group_id', $groupId)
        ->where('request_join', 1)
        ->whereHas('versions') // Include only files with at least one version
        ->select('id', 'name') // Select only the necessary fields
        ->with(['versions' => function ($query) {
            $query->select('id', 'file_id', 'file', 'number') // Select required version fields
                ->orderBy('number', 'desc') // Fetch the latest version
                ->take(1); // Limit to one version
        }])
        ->first(); // Fetch only one file

    // If a file is found, process the latest version
    if ($file && $file->versions->isNotEmpty()) {
        $latestVersion = $file->versions->first();
        $latestVersion->file = $latestVersion->file ? url($latestVersion->file) : null;
        $file->latest_version = $latestVersion; 
        unset($file->versions); 
    }

    return $file;
}*/


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

 public function getByFileId($fileId)
    {
        return Version::select([
                'versions.id',
                'versions.file_id',
                'versions.user_id',
                'versions.number',
                'versions.time',
                'versions.file',
                'versions.created_at',
                'users.name as user_name', // Include user name
                'files.name as file_name',
            ])
            ->join('users', 'users.id', '=', 'versions.user_id')
            ->join('files', 'files.id', '=', 'versions.file_id') 
            ->where('versions.file_id', $fileId)
            ->get()
            ->map(function ($version) {
                return [
                    'id' => $version->id,
                    'file_id' => $version->file_id,
                    'user_id' => $version->user_id,
                    'number' => $version->number,
                    'time' => $version->time, // Already a datetime
                    'file' => url($version->file), 
                    'file_name' => $version->file_name,
                    'user_name' => $version->user_name,
                    'created_date' => $version->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

public function getByFileIduser($fileId)
{
    return Version::select([
            'versions.id',
            'versions.file_id',
            'versions.number',
            'versions.time',
            'versions.file',
            'versions.created_at',
            'files.name as file_name', // Include file name
        ])
        ->join('files', 'files.id', '=', 'versions.file_id') // Join with the files table
        ->where('versions.file_id', $fileId)
        ->get()
        ->map(function ($version) {
            return [
                'id' => $version->id,
                'file_id' => $version->file_id,
                'file_name' => $version->file_name, // Add file name to the output
                'number' => $version->number,
                'time' => $version->time, // Already a datetime
                'file' => url($version->file),
                'created_date' => $version->created_at->format('Y-m-d H:i:s'),
            ];
        });
}

    public function fetchFilesByGroupId($groupId)
    {
        return File::where('group_id', $groupId)
            ->where('request_join', 1)
            ->with(['versions' => function ($query) {
                $query->select('id', 'file_id', 'number', 'file')
                      ->orderBy('number', 'desc');
            }])
            ->get()
            ->map(function ($file) {
                $file->versions = $file->versions->map(function ($version) {
                    if ($version->file) {
                        $version->file = url($version->file);
                    }
                    return $version;
                });
    
                return $file;
            });
    }

       // Fetch groups created by the admin (matching admin_id)
       public function getGroupsByAdminId($adminId)
       {
           return Group::where('admin_id', $adminId)->get();
       }

}
