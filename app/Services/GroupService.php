<?php

namespace App\Services;
use App\Models\Admin;
use App\Models\group_user;
use App\Repositories\GroupRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GroupService
{
    protected $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }
    public function createGroup(array $data)
    {
        // Ensure the user is authenticated and get the user ID
        $user = auth()->user();
        if (!$user) {
            throw new \Exception("User is not authenticated.");
        }
        $userId = $user->id;
    
        // Create or retrieve the admin entry based on the authenticated user ID
        $admin = Admin::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );
    
        // Confirm that admin was created/retrieved and has an ID
        if (!$admin->id) {
            throw new \Exception("Failed to create or retrieve Admin for user_id: {$userId}");
        }
    
        // Add the admin_id to the data array for group creation
        $data['admin_id'] = $admin->id;
    
        // Handle file upload if a photo is provided
        if (isset($data['photo']) && $data['photo'] ) {
            $filePath = $data['photo']->store('photos', 'public'); // Save file to 'storage/app/public/photos'
            $data['photo'] = $filePath;
        }
    
        // Use the repository to create the group with all required data
        return $this->groupRepository->create($data);
    }


    public function getAllUsers()
    {
        return $this->groupRepository->getAllUsers(); // Use the repository to get all users
    }

   
     // Add users to the group
     public function addUsersToGroup(int $groupId, array $userIds)
     {
         foreach ($userIds as $userId) {
             group_user::create([
                 'group_id' => $groupId,
                 'user_id' => $userId,
                // 'request_join' => true, // Assuming you want to track this in the pivot table
             ]);
         }
     }



     public function getRequestedGroupsForUser()
     {
         return $this->groupRepository->getRequestedGroupsForUser();
     }



}    