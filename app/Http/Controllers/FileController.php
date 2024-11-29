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
/*
    public function uploadFile(FileUploadRequest $request)
    {
        $file = $request->file('file');
        $groupId = $request->input('group_id');

        if (!$file || !$groupId) {
            return response()->json(['message' => 'File or group_id is missing.'], 400);
        }

        $result = $this->fileService->uploadFile($file, $groupId);

        return response()->json([ 'data' => $result]);
    }
    */
    public function uploadFileadmin(FileUploadRequest $request)
{
    // Retrieve validated data
    $groupId = $request->input('group_id');
    $file = $request->file('file');

    // Upload file and create version
    $result = $this->fileService->uploadFileadmin($file, $groupId);

    // Check if the result is an array and has 'status'
    if (isset($result['status']) && $result['status'] == 'error') {
        return response()->json([
            'message' => $result['message'],
            'existing_file' => $result['existing_file']
        ], 409); // 409 Conflict HTTP status code
    }

    // If the status is success, return the file data
    return response()->json([
        'message' => $result['message'],
        'file' => $result['file']
    ]);
}



    public function uploadFile(FileUploadRequest $request)
{
    $file = $request->file('file');
    $groupId = $request->input('group_id');

    if (!$file || !$groupId) {
        return response()->json(['message' => 'File or group_id is missing.'], 400);
    }

    // Call the service to handle the file upload
    $result = $this->fileService->handleFileUpload($file, $groupId);

    // Check the status of the response and return appropriate JSON
    if ($result['status'] == 'error') {
        return response()->json([
            'message' => $result['message'],
            'existing_file' => $result['existing_file']
        ], 409); // 409 Conflict HTTP status code
    }

    // If the status is success, return the file data
    return response()->json([
        'message' => $result['message'],
        'file' => $result['file']
    ]);
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
