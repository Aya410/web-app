<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Repositories\FileRepository;
use App\Events\FileUploadPendingApproval; 
class FileService
{
    protected $fileRepository;
    protected $fileRepo;


    public function __construct(GroupRepository $fileRepository,FileRepository $fileRepo)
    {
        $this->fileRepository = $fileRepository;
        $this->fileRepo = $fileRepo;

    }

    
    public function handleFileUpload($file, int $groupId, $requestJoin = null)
    {
        // Check if a file with the same group_id and name already exists
        $existingFile = $this->fileRepo->findByGroupAndName($groupId, $file->getClientOriginalName());
    
        if ($existingFile) {
            // Return a response with a message indicating the file already exists
            return [
                'status' => 'error',
                'message' => 'File with the same name already exists.',
                'existing_file' => $existingFile
            ]; // Return array to be handled in controller
        }
    
        // Proceed with file upload if no matching file is found
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = time() . '.' . $fileExtension;
        $filePath = 'files';
        $file->move(public_path($filePath), $fileName);
    
        $relativePath = $filePath . '/' . $fileName;
    
        // Create File record
        $fileData = [
            'name' => $file->getClientOriginalName(),
            'state' => 0, // Default state
            'request_join' => $requestJoin,
            'group_id' => $groupId,
        ];
        $fileRecord = $this->fileRepo->createFile($fileData);
    
        // Create Version record
        $versionData = [
            'time' => now(),
            'number' => 0,
            'file' => $relativePath,
            'user_id' => Auth::id(),
            'file_id' => $fileRecord->id,
        ];
        $this->fileRepo->createVersion($versionData);
    
        // Return success response with file record
        return [
            'status' => 'success',
            'message' => 'File uploaded successfully.',
            'file' => $fileRecord
        ];
    }
    
/*
private function handleFileUpload($file, int $groupId, $requestJoin = null)
{
    // Check if a file with the same group_id and name already exists
    $existingFile = $this->fileRepo->findByGroupAndName($groupId, $file->getClientOriginalName());

    if ($existingFile) {
        // Return response with message and data
    
       return $existingFile;
    }
    // Proceed with file upload if no match found
    $fileExtension = $file->getClientOriginalExtension();
    $fileName = time() . '.' . $fileExtension;
    $filePath = 'files';
    $file->move(public_path($filePath), $fileName);

    $relativePath = $filePath . '/' . $fileName;

    // Create File record
    $fileData = [
        'name' => $file->getClientOriginalName(),
        'state' => 0, // Default state
        'request_join' => $requestJoin,
        'group_id' => $groupId,
    ];
    $fileRecord = $this->fileRepo->createFile($fileData);

    // Create Version record
    $versionData = [
        'time' => now(),
        'number' => 0,
        'file' => $relativePath,
        'user_id' => Auth::id(),
        'file_id' => $fileRecord->id,
    ];
    $this->fileRepo->createVersion($versionData);

    return $fileRecord;
}

*/

public function uploadFileadmin($file, int $groupId)
{
    if ($file) {
        return $this->handleFileUpload($file, $groupId, $requestJoin = 1);
    }
    return null;
}

public function uploadFile($file, int $groupId)
{
    if ($file) {
        $fileRecord = $this->handleFileUpload($file, $groupId);
        
        // Fire event for admin approval
        event(new FileUploadPendingApproval($fileRecord));

        return $fileRecord;
    }
    return null;
}

    public function getPendingFiles()
    {
        // Retrieve files with state = 0 (Pending)
        return $this->fileRepo->getFilesByState(NULL);
    }

    public function handleAdminResponse(int $fileId, bool $isApproved)
    {
        $fileRecord = $this->fileRepo->find($fileId);

        if (!$fileRecord) {
            return 'File not found.';
        }

        if (($isApproved)==(1)) {
            // Approve the file
            $this->fileRepo->update($fileRecord->id, ['request_join' => 1]);
            return 'File approved and finalized.';
        }
    else{

    
        $this->fileRepo->delete($fileRecord->id);

        return 'File rejected and deleted.';
}
    }
   public function getFilesWithVersionsByGroupId($groupId)
    {
        return $this->fileRepository->getFilesWithVersionsByGroupId($groupId);
    }


        public function getFileVersions($fileId)
    {
        return $this->fileRepository->getByFileId($fileId);
    }
    public function getFileVersionsuser($fileId)
    {
        return $this->fileRepository->getByFileIduser($fileId);
    }

    public function getFiles($groupId)
{
    return $this->fileRepository->fetchFilesByGroupId($groupId);
}


public function deleteFileAndVersions(int $fileId): bool
{
    return $this->fileRepo->deleteFileAndVersions($fileId);
}


public function getHistoryUser($userId, $groupId)
{
    // Call the repository to retrieve files and their versions
    return $this->fileRepo->getHistoryUser($userId, $groupId);
}
}
