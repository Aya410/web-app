<?php

namespace App\Repositories;
use App\Models\User;
use App\Models\Admin;
use App\Models\Group;
use App\Models\Version;
use App\Models\group_user;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ShowUserFileRequest;
use App\Http\Controllers\NotificationController;

class SuperAdminRepository{

    public function GetAllgroups(){
        $groups = Group::get();

        $Info=[];

        foreach( $groups as $group)
        {
            $admin=$group->admin->user;
            $info=[
                'group_id'=>$group->id,
                'group_name'=>$group->name,
                'group_description'=>$group->description,
                'group_photo'=>$group->photo,
                'group_created_at'=>$group->created_at,
                'admin_name'=>$admin->name,
                'admin_email'=>$admin->email,
            ];
            $Info[] = $info;

        }
        return response()->json(['All Groups' => $Info], 200);
    }



    public function GetAllUsers()
    {
        $users=User::get();
        $Info=[];
        foreach($users as $user){
            $admin=$user->admin;

            if($admin)
            {
                $role='Group Admin';
                $info=[
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'email'=>$user->email,
                    'role'=>$role,
                    'join_date'=>$user->created_at,
                ];
                $Info[] = $info;
            }
            else
            {
                $info=[
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'email'=>$user->email,
                    'role'=>$user->role,
                    'join_date'=>$user->created_at,
                ];
                $Info[] = $info;
            }

        }
        return response()->json(['All Users' => $Info], 200);

    }


    public function GetGroupsOfAUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $Info = [];

        foreach ($user->groups as $group) {
            $number = 0;
            $files = $group->files;
            $admin=$group->admin->user;

            foreach ($files as $file) {
                $version_number = Version::where('file_id', $file->id)
                                         ->where('user_id', $id)
                                         ->count();
                $number += $version_number;
            }


            $info = [
                'group_info' => $group->only(['id', 'name', 'description','photo']),
                 'admin_email'=>$admin->email,
                'checkout_number' => $number,
            ];

            $Info[] = $info;
        }

        return response()->json(['All groups with info' => $Info], 200);
    }


    public function ShowUserFiles(ShowUserFileRequest $request)
    {
        $group=Group::where('id',$request->group_id)->first();
        $files=$group->files;
        $Info = [];
        foreach($files as $file){
            $versions=$file->versions;

            foreach($versions as $version){
                if($version->user_id == $request->user_id)
                {

                  $info = [
                      'version_id'=>$version->id,
                      'file_name' =>$file->name ,
                      'file_verion'=>$version->number,
                      'version_path'=>$version->file,
                      'time' => $version->time,
                      'date' => $version->created_at,
                  ];
                  $Info[] = $info;
                }
          }
        }

        return response()->json([$Info], 200);
    }


    public function ShowFilesOfAGroup($id)
    {
        $group=Group::where('id',$id)->first();
        $files=$group->files;
        return response()->json(['All files of this group' => $files], 200);
    }




}
