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
private function handleFileUpload($file, int $groupId, $requestJoin = null)
{
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

}
