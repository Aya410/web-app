<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GroupService;
use App\Http\Requests\UpdateRequestJoinRequest;
use App\Models\Group;
use App\Http\Requests\GroupRequest;
<<<<<<< HEAD
use App\Http\Requests\FileRequest;
=======
use Illuminate\Support\Facades\Response;




>>>>>>> 8600d5b9b37787102a7a4d8033b24554f12e25bc
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    //show  all user for admingroup to select
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
<<<<<<< HEAD
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
=======

    public function yy(){
        return response()->json("fuhsh");
    }

>>>>>>> 8600d5b9b37787102a7a4d8033b24554f12e25bc
    }
