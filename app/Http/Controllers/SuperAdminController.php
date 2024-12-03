<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SuperAdminService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShowUserFileRequest;
use App\Repositories\SuperAdminRepository;

class SuperAdminController extends Controller
{
   protected $superadminrepo;
   public function __construct( SuperAdminRepository $superadminrepo)
   {

   $this->superadminrepo = $superadminrepo;
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





}
