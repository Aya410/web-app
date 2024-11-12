<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GroupService;
use App\Models\Group;
use App\Http\Requests\GroupRequest;

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
  /*
    public function store(GroupRequest $request)
    {
           $validatedData = $request->validated();
          // جلب البيانات التي تم التحقق منها
      
   

        // Call the service to create the group
        $group = $this->groupService->createGroup($validatedData);
    
    
        return response()->json($group, 201);
    }

        return response()->json($group, 201);
    }


*//*
    public function store(GroupRequest $request)
    {
        // جلب البيانات التي تم التحقق منها
        $validatedData = $request->validated();
      
        // استدعاء الخدمة لإنشاء المجموعة
        $group = $this->groupService->createGroup($validatedData);

        // إضافة المستخدمين إلى المجموعة
        $this->groupService->addUsersToGroup($group->id, $validatedData['user_ids']);

        return response()->json($group, 201);
    }*/
    

/*


    public function store(GroupRequest $request)
    {
        // جلب البيانات التي تم التحقق منها
        $validatedData = $request->validated();
      
        // استدعاء الخدمة لإنشاء المجموعة
        $group = $this->groupService->createGroup($validatedData);

        // إضافة المستخدمين إلى المجموعة
        $this->groupService->addUsersToGroup($group->id, $validatedData['user_ids']);

        return response()->json($group, 201);
    }*/
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


    public function getMyGroups(Request $request)
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
    
    }
