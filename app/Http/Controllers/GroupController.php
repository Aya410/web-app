<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Services\GroupService;
use App\Http\Requests\FileRequest;
use App\Http\Requests\GroupRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\FileUploadRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateRequestJoinRequest;

class GroupController extends Controller
{
 protected $groupService;
 protected $fileService;

 public function __construct(GroupService $groupService, FileService $fileService)
 {
 $this->groupService = $groupService;
 $this->fileService = $fileService;
 }

 public function store(GroupRequest $request)
 {
     // Get the validated data from the request
 $validatedData = $request->validated();

 // Create the group using the validated data
 $group = $this->groupService->createGroup($validatedData);

 // Add users to the group
 if (isset($validatedData['user_ids'])) {
         $this->groupService->addUsersToGroup($group->id, $validatedData['user_ids']);
 }

 // Return the created group as a response with a status code of 201 (Created)
 return response()->json($group, 201);
 }

 // Show all users for admin group to select
 public function showAllUsers()
 {
     $users = $this->groupService->getAllUsers();
 return response()->json($users, 200);
 }

 public function getMyGroups()
 {
     $groups = $this->groupService->getRequestedGroupsForUser();
 return response()->json($groups, 200);
 }

 public function store_file(FileUploadRequest $request)
 {
     // Retrieve validated data
 $groupId = $request->input('group_id');
 $file = $request->file('file');

 // Upload file and create version
 $createdFile = $this->fileService->uploadFile($file, $groupId);

 return response()->json([
         'message' => 'File uploaded successfully',
 'file' => $createdFile,
 ], 201);
 }

 public function getFilesByGroupId(FileRequest $request)
 {
     // Retrieve the validated group_id
 $groupId = $request->input('group_id');
 $files = $this->fileService->getFilesWithVersionsByGroupId($groupId);

 return response()->json($files, 200);
 }


 public function getPendingGroupsForAuthUser()
 {
     // Fetch pending groups from the service
 $groups = $this->groupService->getPendingGroupsForUser();

 // Return the groups as JSON response
 return response()->json($groups, 200);
 }



 public function updateRequestJoin(UpdateRequestJoinRequest $request)
{
     // Retrieve the validated input data
 $data = $request->validated();
 $groupUserId = $data['group_user_id']; // The ID of the group_user
 $requestJoin = $data['request_join']; // The request_join value (boolean)

 // Pass the validated data to the service layer
 $updatedGroupUser = $this->groupService->updateRequestJoin($groupUserId, $requestJoin);

 return response()->json($updatedGroupUser, 200);
}
}
