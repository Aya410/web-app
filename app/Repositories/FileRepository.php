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
    public function findByGroupAndName(int $groupId, string $name)
    {
        return File::where('group_id', $groupId)
                   ->where('name', $name)
                   ->first();
    }
    
  
public function getFilesByState($request_join)
{
    return File::when($request_join === null, function ($query) {
        return $query->whereNull('request_join');
    }, function ($query) use ($request_join) {
        return $query->where('request_join', $request_join);
    })
    ->with(['versions.user:id,name', 'versions.file']) // Eager load versions and associated file
    ->get()
    ->map(function ($file) {
        // Add a user_names attribute with only user names from the versions
        $file->user_names = $file->versions->pluck('user.name')->unique()->values();
        
        // Add the 'file_url' attribute from the version's file
        $file->file_url = $file->versions->first() ? url( $file->versions->first()->file) : null; // Assuming 'file' relation on version has a 'path' field

        unset($file->versions); 
        return $file;
    });
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
 
    public function getHistoryUser($userId, $groupId)
    {
        // Query the 'versions' table and join 'files' and 'users' tables
        $versions = Version::select([
                'versions.id',
                'versions.file_id',
                'versions.user_id',
                'versions.number',
                'versions.time',
                'versions.file',
                
                'versions.created_at',
                'files.name as file_name',  
                'users.name as user_name',  
            ])
            ->join('files', 'files.id', '=', 'versions.file_id') 
            ->join('users', 'users.id', '=', 'versions.user_id') 
            ->where('versions.user_id', $userId)  
            ->whereHas('file', function ($query) use ($groupId) {
                $query->where('group_id', $groupId) 
                      ->where('request_join', 1);  
            })
            ->get(); 
        return $versions->map(function ($version) {
            return [
                'id' => $version->id,
                'file_id' => $version->file_id,
                'user_id' => $version->user_id,
                'number' => $version->number,
                'time' => $version->time, 
                'file' => url($version->file),
                'file_name' => $version->file_name,  
                'user_name' => $version->user_name, 
                'created_date' => optional($version->created_at)->format('Y-m-d H:i:s'), 
            ];
        });
    }
    

}
