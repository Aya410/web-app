<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\ResponseRequest;
use App\Http\Requests\GetFileVersionsRequest;
use App\Http\Requests\UsergroupRequest;
use App\Http\Requests\FileRequest;
class FileController extends Controller
{
    
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function uploadFile(FileUploadRequest $request)
    {
        $file = $request->file('file');
        $groupId = $request->input('group_id');

        if (!$file || !$groupId) {
            return response()->json(['message' => 'File or group_id is missing.'], 400);
        }

        $result = $this->fileService->uploadFile($file, $groupId);

        return response()->json(['message' => 'File uploaded successfully.', 'data' => $result]);
    }

    public function getPendingFiles()
    {
        $files = $this->fileService->getPendingFiles();
        return response()->json($files);
    }

    public function handleAdminResponse(ResponseRequest $request)
    {
        $isApproved = $request->input('isApproved', false);

        $fileId = $request->input('fileId');


        $message = $this->fileService->handleAdminResponse($fileId, $isApproved);
        return response()->json(['message' => $message]);
    }

    public function getallFiles(UsergroupRequest $request)
    {
      
        $groupId = $request->input('group_id');
    
        // Get the files with versions for the given group
        $files = $this->fileService->getFiles($groupId);
    
        return response()->json($files);
    }
    

    public function deleteFile(GetFileVersionsRequest $request)
    {
       

        $fileId = $request->input('file_id');

        $deleted = $this->fileService->deleteFileAndVersions($fileId);

        if ($deleted) {
            return response()->json(['message' => 'File and associated versions deleted successfully.'], 200);
        } else {
            return response()->json(['error' => 'File not found or could not be deleted.'], 404);
        }
    }



    public function getHistory(FileRequest $request)
    {
        $userId = auth()->id(); // Get the authenticated user's ID
        $groupId = $request->input('group_id'); // Get group_id from the request

        // Call the service to get files and their versions
        $files = $this->fileService->getHistoryUser($userId, $groupId);

        return response()->json($files);
    }

    
}
