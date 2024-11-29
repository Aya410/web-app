<?php

namespace App\Services;
use App\Models\Admin;
use App\Models\group_user;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\NotificationController;

class GroupService
{
    protected $groupRepository;

    protected $notificationController;

    public function __construct(GroupRepository $groupRepository, NotificationController $notificationController)
    {
        $this->groupRepository = $groupRepository;
        $this->notificationController = $notificationController;
    }
    public function createGroup(array $data, string $token)
{
    $user = auth()->user();

    if (!$user) {
        throw new \Exception("User is not authenticated.");
    }

    $userId = $user->id;

    // Fetch or create Admin
    $admin = Admin::firstOrCreate(
        ['user_id' => $userId],
        ['user_id' => $userId]
    );
    if (!$admin->id) {
        throw new \Exception("Failed to create or retrieve Admin for user_id: {$userId}");
    }

    $data['admin_id'] = $admin->id;

    // Handle photo upload if provided
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

    // Create the group
    $group = $this->groupRepository->create($data);

    $notificationResults = null;
    // Send notifications to invited users and capture the response
    if (isset($data['user_ids'])) {
        $notificationResults = $this->notificationController->sendGroupInvitationNotifications($data['user_ids'], $token);
    }

    // Return the group and notification results
    return [
        'group' => $group,
        'notification_results' => $notificationResults
    ];
}

/*    
public function createGroup(array $data, string $token)
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

    // Handle photo upload
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

    // Create the group
    $group = $this->groupRepository->create($data);

    // Send notifications to invited users using the notification controller
    if (isset($data['user_ids'])) {
        $this->notificationController->sendGroupInvitationNotifications($data['user_ids'], $token);
    }

    return $group;
}*/

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