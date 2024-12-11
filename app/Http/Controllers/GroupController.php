<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Services\GroupService;
use App\Http\Requests\FileRequest;
use App\Http\Requests\GroupRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\FileUploadRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateRequestJoinRequest;
use App\Http\Requests\GetFileVersionsRequest;
use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade as PDF;
class GroupController extends Controller
{
 protected $groupService;
 protected $fileService;

 public function __construct(GroupService $groupService, FileService $fileService)
 {
 $this->groupService = $groupService;
 $this->fileService = $fileService;
 }
/*
public function store(GroupRequest $request)
{
    $validatedData = $request->validated();
    $token = $request->input('token');  // Get the shared token from the request

    // Call the service method to create the group and send notifications
    $results = $this->groupService->createGroup($validatedData, $token);

    // If user_ids are provided, add them to the group
    if (isset($validatedData['user_ids'])) {
        $this->groupService->addUsersToGroup($results['group']->id, $validatedData['user_ids']);
    }

    // Return the response with group and notification results
    return response()->json([
        'group' => $results['group'],  // Group data
        'notification_results' => $results['notification_results']  // Notification results
    ], 201);
}
*/


// Store group
public function store(GroupRequest $request)
{
    $validatedData = $request->validated();
    $token = $request->input('token');  // Get the shared token from the request

    // Call the service method to create the group and send notifications
    $results = $this->groupService->createGroup($validatedData, $token);

    // If user_ids are provided, add them to the group along with the authenticated user's ID
    if (isset($validatedData['user_ids'])) {
        $this->groupService->addUsersToGroup($results['group']->id, $validatedData['user_ids'], auth()->id());
    }

    // Return the response with group and notification results
    return response()->json([
        'group' => $results['group'],  // Group data
        'notification_results' => $results['notification_results']  // Notification results
    ], 201);
}
 // Show all users for admin group to select
 public function showAllUsers()
 {
     $users = $this->groupService->getAllUsers();
 return response()->json($users, 200);
 }

 public function getMyGroups()
 {
     $groups = $this->groupService->getRequestedGroupsForUser();
 return response()->json($groups, 200);
 }
 public function getFilesByGroupId(FileRequest $request)
 {
     // Retrieve the validated group_id
 $groupId = $request->input('group_id');
 $files = $this->fileService->getFilesWithVersionsByGroupId($groupId);

 return response()->json($files, 200);
 }


 public function getPendingGroupsForAuthUser()
 {
     // Fetch pending groups from the service
 $groups = $this->groupService->getPendingGroupsForUser();

 // Return the groups as JSON response
 return response()->json($groups, 200);
 }



 public function updateRequestJoin(UpdateRequestJoinRequest $request)
{
     // Retrieve the validated input data
 $data = $request->validated();
 $groupUserId = $data['group_user_id']; // The ID of the group_user
 $requestJoin = $data['request_join']; // The request_join value (boolean)

 // Pass the validated data to the service layer
 $updatedGroupUser = $this->groupService->updateRequestJoin($groupUserId, $requestJoin);

 return response()->json($updatedGroupUser, 200);
}

public function getFileVersions(GetFileVersionsRequest $request)
{
    $fileId = $request->input('file_id');
    $versions = $this->fileService->getFileVersions($fileId);
    return response()->json($versions);
}

public function getFileVersionsuser(GetFileVersionsRequest $request)
{
    $fileId = $request->input('file_id');
    $versions = $this->fileService->getFileVersionsuser($fileId);
    return response()->json($versions);
}


    // Method to get group names based on authenticated admin's ID
    public function getGroups()
    {
       
        $adminId = Auth::id();
        

        $groupNames = $this->groupService->getGroupNamesByAdminId($adminId);

        return response()->json($groupNames);
    }


    public function exportFileVersionsToPdf(Request $request)
{
    // Get file ID from the request
    $fileId = $request->input('file_id');

    // Get file versions from the service layer
    $versions = $this->fileService->getFileVersions($fileId);

    // Convert the versions collection to an array if needed
    $versionsArray = $versions->toArray();

    // Prepare the HTML content for the PDF
    $html = '<h1>File Versions</h1>';
    $html .= '<table border="1" style="width: 100%; border-collapse: collapse;">';
    $html .= '
        <thead>
            <tr>
                <th>#</th>
                <th>File Name</th>
                <th>Version Number</th>
                <th>Time</th>
                <th>User</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
    ';

    foreach ($versionsArray as $version) {
        $html .= '<tr>';
        $html .= '<td>' . $version['id'] . '</td>';
        $html .= '<td>' . $version['file_name'] . '</td>';
        $html .= '<td>' . $version['number'] . '</td>';
        $html .= '<td>' . $version['time'] . '</td>';
        $html .= '<td>' . $version['user_name'] . '</td>';
        $html .= '<td>' . $version['created_date'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Initialize Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Ensure the "public/pdfs" folder exists
    $pdfDirectory = public_path('pdfs');
    if (!file_exists($pdfDirectory)) {
        mkdir($pdfDirectory, 0755, true); // Create the directory with permissions
    }

    // Save the PDF to a file in the project
    $fileName = 'file_versions_' . time() . '.pdf';
    $filePath = $pdfDirectory . '/' . $fileName;
    file_put_contents($filePath, $dompdf->output());

    // Return the URL of the saved file to the frontend
    $fileUrl = url('pdfs/' . $fileName);
    return response()->json([
        'success' => true,
        'message' => 'PDF generated successfully.',
        'file_url' => $fileUrl,
    ]);
}




/*
public function exportnfoUserToPdf(Request $request)
{
    // Get user ID from the request
    $userId = $request->input('user_id');

    // Retrieve version information from the repository
    $versionInfo = $this->fileService->getVersionInfoByUserId($userId);

    // Convert the collection to an array
    $versionArray = $versionInfo->toArray();

    // Prepare the HTML content for the PDF
    $html = '<h1>User Version History</h1>';
    $html .= '<table border="1" style="width: 100%; border-collapse: collapse;">';
    $html .= '
        <thead>
            <tr>
                <th>#</th>
                <th>Version Number</th>
                <th>Time</th>
                <th>File Name</th>
                <th>File State</th>
                <th>Group Name</th>
            </tr>
        </thead>
        <tbody>
    ';

    foreach ($versionArray as $index => $version) {
        $html .= '<tr>';
        $html .= '<td>' . ($index + 1) . '</td>';
        $html .= '<td>' . ($version['version']['number'] ?? 'N/A') . '</td>';
        $html .= '<td>' . ($version['version']['time'] ?? 'N/A') . '</td>';
        $html .= '<td>' . ($version['file']['name'] ?? 'Unknown File Name') . '</td>';
        $html .= '<td>' . ($version['file']['state'] ?? 'Unknown State') . '</td>';
        $html .= '<td>' . ($version['group']['name'] ?? 'No Group Assigned') . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Initialize Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Ensure the "public/pdfs" folder exists
    $pdfDirectory = public_path('pdfs');
    if (!file_exists($pdfDirectory)) {
        mkdir($pdfDirectory, 0755, true); // Create the directory with permissions
    }

    // Save the PDF to a file in the project
    $fileName = 'user_version_history_' . time() . '.pdf';
    $filePath = $pdfDirectory . '/' . $fileName;
    file_put_contents($filePath, $dompdf->output());

    // Return the URL of the saved file to the frontend
    $fileUrl = url('pdfs/' . $fileName);
    return response()->json([
        'success' => true,
        'message' => 'PDF generated successfully.',
        'file_url' => $fileUrl,
    ]);
}


*/


public function exportnfoUserToPdf(Request $request)
{
    // Get user ID from the request
    $userId = $request->input('user_id');

    // Retrieve version information from the repository
    $versionInfo = $this->fileService->getVersionInfoByUserId($userId);

    // Convert the collection to an array
    $versionArray = $versionInfo->toArray();

    // Prepare the HTML content for the PDF
    $html = '<h1>User Version History</h1>';
    $html .= '<table border="1" style="width: 100%; border-collapse: collapse;">';
    $html .= '
        <thead>
            <tr>
                <th>#</th>
                <th>Version Number</th>
                <th>Time</th>
                <th>File Name</th>
                <th>File State</th>
                <th>Group Name</th>
            </tr>
        </thead>
        <tbody>
    ';

    foreach ($versionArray as $index => $version) {
        $html .= '<tr>';
        $html .= '<td>' . ($index + 1) . '</td>';
        $html .= '<td>' . ($version['version']['number'] ?? 'N/A') . '</td>';
        $html .= '<td>' . ($version['version']['time'] ?? 'N/A') . '</td>';
        $html .= '<td>' . ($version['file']['name'] ?? 'Unknown File Name') . '</td>';
        $html .= '<td>' . ($version['file']['state'] ?? 'Unknown State') . '</td>';
        $html .= '<td>' . ($version['group']['name'] ?? 'No Group Assigned') . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Initialize Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Stream the PDF to the browser for download
    $fileName = 'user_version_history_' . time() . '.pdf';
    return response($dompdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
}


}
    

