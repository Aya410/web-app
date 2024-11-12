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
    }
}
