<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileService
{
    protected $fileRepository;

    public function __construct(GroupRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function uploadFile($file, int $groupId)
{
    // Check if the file is provided
    if ($file) {
        // Get the file extension
        $fileExtension = $file->getClientOriginalExtension();
        
        // Generate a unique file name using the current timestamp
        $fileName = time() . '.' . $fileExtension;

        // Define the path where the file will be stored
        $filePath = 'files';  // Directory in the public folder

        // Move the file to the 'public/picture_files' directory
        $file->move(public_path($filePath), $fileName);

        // Save the relative file path in the database
        $relativePath = $filePath . '/' . $fileName;

        // Generate the full URL for the file
        $fullUrl = url($relativePath);  // Full URL like http://yourdomain.com/picture_files/your-file.jpg

        // Create the File record
        $fileData = [
            'name' => $file->getClientOriginalName(),
            'state' => 0, // Initial state (you can customize this)
            'group_id' => $groupId,
            'path' => $fullUrl,  // Store the full URL in the database
        ];
        $fileRecord = $this->fileRepository->createFile($fileData);

        // Create the first Version record (you can customize versioning logic)
        $versionData = [
            'time' => now(),  // Store the current date and time (YYYY-MM-DD HH:MM:SS)
            'number' => 0, // Initial version number
            'file' => $relativePath,  // Store the relative path
            'user_id' => Auth::id(),
            'file_id' => $fileRecord->id,
        ];
        $this->fileRepository->createVersion($versionData);

        // Return the file record
        return $fileRecord;
    }

    // Return null or throw an exception if no file is uploaded
    return null;
}

/*
    public function uploadFile($file, int $groupId)
    {
        // Store the file and retrieve the file path
        $filePath = $file->store('uploads', 'public');

        // Create the File record
        $fileData = [
            'name' => $file->getClientOriginalName(),
            'state' => 0,
            'group_id' => $groupId,
        ];
        $file = $this->fileRepository->createFile($fileData);

        // Create the first Version record
        $versionData = [
            'time' => now()->toTimeString(),
            'number' => 0, // Initial version number
            'file' => $filePath,
            'user_id' => Auth::id(),
            'file_id' => $file->id,
        ];
        $this->fileRepository->createVersion($versionData);

        return $file;
    }*/
    public function getFilesWithVersionsByGroupId($groupId)
    {
        return $this->fileRepository->getFilesWithVersionsByGroupId($groupId);
    }
}
