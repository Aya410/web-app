<?php

namespace App\Services;
use App\Models\Admin;
use App\Models\group_user;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;
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
    $user = auth()->user();
    if (!$user) {
        throw new \Exception("User is not authenticated.");
    }
    $userId = $user->id;

    $admin = Admin::firstOrCreate(
        ['user_id' => $userId],
        ['user_id' => $userId]
    );
    if (!$admin->id) {
        throw new \Exception("Failed to create or retrieve Admin for user_id: {$userId}");
    }

    $data['admin_id'] = $admin->id;

    if (isset($data['photo']) && $data['photo']) {
        
        $image = $data['photo'];
        $imageExtension = $image->getClientOriginalExtension();
        
        $imageName = time() . '.' . $imageExtension;
        
  
        $imagePath = 'picture_files';
        
        $image->move(public_path($imagePath), $imageName);
        
    
        $relativePath = $imagePath . '/' . $imageName;
        $fullUrl = url($relativePath);  
  
        $data['photo'] = $fullUrl;
    }
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

     public function getPendingGroupsForUser()
     {
         // Get the authenticated user's ID
         $userId = Auth::id();
 
         // Use the repository to fetch groups
         return $this->groupRepository->getPendingGroupsForUser($userId);
     }

     public function updateRequestJoin($groupUserId, $requestJoin)
{
    return $this->groupRepository->updateRequestJoin($groupUserId, $requestJoin);
}
 
 public function getGroupNamesByAdminId($adminId)
 {
     
     $groups = $this->groupRepository->getGroupsByAdminId($adminId);

     
     return $groups;
 }

}    