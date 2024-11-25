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

  
/*
    public function getFilesByState(null $request_join)
    {
        return File::where('request_join', $request_join)->get();
    }
    
    
*/
public function getFilesByState($request_join)
{
    // Handle NULL or specific values
    return File::when($request_join === NULL, function ($query) {
        return $query->whereNull('request_join');
    }, function ($query) use ($request_join) {
        return $query->where('request_join', $request_join);
    })->get();
}


    public function createVersion(array $data)
    {
        return Version::create($data);
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
