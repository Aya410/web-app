<?php
namespace App\Repositories;

use App\Models\File;
use App\Models\Version;

class FileRepository
{
    public function create(array $data)
    {
        return File::create($data);
    }

  

    public function getFilesByState(null $request_join)
    {
        return File::where('request_join', $request_join)->get();
    }
    
    


    public function createVersion(array $data)
    {
        return Version::create($data);
    }


    
       // Fetch groups created by the admin (matching admin_id)
       public function getGroupsByAdminId($adminId)
       {
           return Group::where('admin_id', $adminId)->get();
       }

       
    public function createFile(array $data)
    {
        return File::create($data);
    }

  
 
    public function find(int $id)
    {
        return File::find($id);
    }
    public function update(int $id, array $data)
    {
        $file = $this->find($id);
        if ($file) {
            $file->update($data);
        }
        return $file;
    }
    public function delete(int $id)
    {
        $file = $this->find($id);
        if ($file) {
            $file->delete();
        }
        return $file;
    }


    public function deleteFileAndVersions(int $fileId): bool
    {
        $file = File::with('versions')->find($fileId);

        if (!$file) {
            return false; 
        }

        $file->versions()->delete();

        return $file->delete();
    }
 

}
