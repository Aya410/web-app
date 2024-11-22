<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UsergroupRequest;
class UserController extends Controller
{
    
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function searchUser(UserRequest $request)
    {
        $name = $request->input('name'); // Get the name from the request

        // Call the service to perform the search
        $users = $this->userService->searchByName($name);

        // Return the response as JSON
        return response()->json($users);
    }

    public function getUsersByGroupId(UsergroupRequest $request)
    {
        $groupId = $request->input('group_id');

        // Get the users for the given group
        $users = $this->userService->getUsersByGroupId($groupId);

        return response()->json($users);
    }


    
}
