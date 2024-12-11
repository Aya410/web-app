<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileService;
use App\Services\SuperAdminService;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\ShowUserFileRequest;
use App\Repositories\SuperAdminRepository;

class SuperAdminController extends Controller
{
   protected $superadminrepo;
   protected $fileService;
   public function __construct( SuperAdminRepository $superadminrepo , FileService $fileService)
   {

   $this->superadminrepo = $superadminrepo;
   $this->fileService = $fileService;
   }

   public function GetAllGroups(){
    return $this->superadminrepo->GetAllgroups();
   }

   public function GetAllUsers(){
    return $this->superadminrepo->GetAllUsers();
   }


   public function GetGroupsOfAUser($id){
    return $this->superadminrepo->GetGroupsOfAUser($id);

   }

   public function ShowUserFiles(ShowUserFileRequest $request)
   {
    return $this->superadminrepo->ShowUserFiles($request);
   }


   public function ShowFilesOfAGroup($id)
   {
      return $this->superadminrepo->ShowFilesOfAGroup($id);
   }

   public function deletefile($id){
    return $this->superadminrepo->deletefile($id);
   }

   public function AddFileBySuperAdmin(FileUploadRequest $request)
{
    $result = $this->fileService->uploadFileadmin($request->file, $request->group_id);
    if (isset($result['status']) && $result['status'] == 'error') {
        return response()->json([
            'message' => $result['message'],
            'existing_file' => $result['existing_file']
        ], 409);
    }

    return response()->json([
        'message' => $result['message'],
        'file' => $result['file']
    ]);
}



    public function ShowVersionsOfFile($id)
    {
    return $this->superadminrepo->ShowVersionsOfFile($id);

   }

   public function ShowUsersOfGroup($id){
    return $this->superadminrepo->ShowUsersOfGroup($id);
   }

   public function DeleteUserFromGroup(DeleteUserRequest $request){
    return $this->superadminrepo->DeleteUserFromGroup($request);
   }







}
