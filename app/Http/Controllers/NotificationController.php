<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FcmService;
use App\Models\User;

use App\Models\Version;

use App\Events\BeforeNotificationSent;
use App\Events\AfterNotificationSent;


use App\Events\BeforeNotificationcheck;
use App\Events\AfterNotificationcheck;

class NotificationController extends Controller
{
    

    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function sendTestNotification(Request $request)
    {
        $request->validate([
            'token' => 'required', // FCM device token
            'title' => 'required',
            'body' => 'required',
        ]);

        $token = $request->input('token');
        $title = $request->input('title');
        $body = $request->input('body');

        $response = $this->fcmService->sendNotification($token, $title, $body);

        return response()->json(['response' => $response], 200);
    }

    public function sendCheckinNotification(Request $request)
{
    $request->validate([
        'token' => 'required', // FCM device token
    ]);

    $token = $request->input('token');
    $title = "Notification"; // Fixed title
    $body = "The file has been reserved"; // Fixed body

    $response = $this->fcmService->sendNotification($token, $title, $body);

    return response()->json(['response' => $response], 200);
}
 
public function sendCheckoutNotification(Request $request)
{
    $request->validate([
        'token' => 'required', // FCM device token
    ]);

    $token = $request->input('token');
    $title = "Notification"; // Fixed title
    $body = "The file has been released"; // Fixed body

    $response = $this->fcmService->sendNotification($token, $title, $body);

    return response()->json(['response' => $response], 200);
}

public function sendNotificationsWithEvents(int $fileId, String $token)
{
    $userIds = Version::where('file_id', $fileId)
        ->pluck('user_id') // جلب user_id المرتبطين بـ file_id
        ->unique();

    if ($userIds->isEmpty()) {
        throw new \Exception('No users found for this file.');
    }

    // إطلاق حدث "قبل الإشعار"
    event(new BeforeNotificationSent($userIds->toArray(), $fileId));

    $results = [];
    foreach ($userIds as $userId) {
        $user = User::find($userId);

        if ($user) {
            $title = "Access Notification";
            $body = "You have access to file ID: {$fileId}.";
            $response = $this->fcmService->sendNotification($user->fcm_token, $title, $body);

            $results[] = [
                'user_id' => $userId,
                'status' => $response ? 'success' : 'failed',
                'message' => $response ? 'Notification sent successfully.' : 'Failed to send notification.',
            ];
        }
    }

    // إطلاق حدث "بعد الإشعار"
    event(new AfterNotificationSent($results, $fileId));
}

/*
public function sendNotificationcheckout(int $fileId, string $token)
{
    // Retrieve user IDs associated with the given file_id
    $userIds = Version::where('file_id', $fileId)
        ->pluck('reservedby') // Get user_id associated with the file_id
        ->unique();

    if ($userIds->isEmpty()) {
        throw new \Exception('No users found for this file.');
    }

    // Trigger "BeforeNotificationSent" event
    event(new BeforeNotificationSent($userIds->toArray(), $fileId));

    $results = [];
    foreach ($userIds as $userId) {
        $user = User::find($userId);

        if ($user) {
            $title = "File Checkout Notification";
            $body = "A file you are associated with (ID: {$fileId}) has been checked out.";
            $response = $this->fcmService->sendNotification($user->fcm_token, $title, $body, $token); // Pass the token

            $results[] = [
                'user_id' => $userId,
                'status' => $response ? 'success' : 'failed',
                'message' => $response ? 'Notification sent successfully.' : 'Failed to send notification.',
            ];
        }
    }

    // Trigger "AfterNotificationSent" event
    event(new AfterNotificationSent($results, $fileId));
      // Optional: Include before and after event details in the response
      return response()->json([
        'results' => $results,
        'log_before' => 'Before event triggered (check log for details)',
        'log_after' => 'After event triggered (check log for details)',
    ]);
}
*/
public function sendNotificationCheckout(int $fileId, string $token)
{
    // Retrieve user IDs associated with the given file_id
    $userIds = Version::where('file_id', $fileId)
        ->pluck('reservedby') // Get user_id associated with the file_id
        ->unique();

    if ($userIds->isEmpty()) {
        throw new \Exception('No users found for this file.');
    }

    // Trigger "BeforeNotificationSent" event
    event(new BeforeNotificationSent($userIds->toArray(), $fileId));

    $results = [];
    foreach ($userIds as $userId) {
        $user = User::find($userId);

        if ($user) {
            $title = "File Checkout Notification";
            $body = "A file you are associated with (ID: {$fileId}) has been checked out.";
            $response = $this->fcmService->sendNotification($user->fcm_token, $title, $body, $token); // Pass the token

            $results[] = [
                'user_id' => $userId,
                'status' => $response ? 'success' : 'failed',
                'message' => $response ? 'Notification sent successfully.' : 'Failed to send notification.',
            ];
        }
    }

    // Trigger "AfterNotificationSent" event
    event(new AfterNotificationSent($results, $fileId));

    // Return the notification results along with any relevant logs or details
    return [
        'results' => $results,
        'log_before' => 'Before event triggered (check log for details)',
        'log_after' => 'After event triggered (check log for details)',
    ];
}


public function sendGroupInvitationNotifications(array $userIds, string $token)
{
    // Fire before notification event
    event(new BeforeNotificationSent($userIds, $token));

    $results = [];
    foreach ($userIds as $userId) {
        $user = User::find($userId);
        if ($user) {
            $title = "Notification";
            $body = "You have been invited to join a group.";
            $response = $this->fcmService->sendNotification($token, $title, $body);

            if ($response) {
                $results[] = [
                    'user_id' => $userId,
                    'status' => 'success',
                    'message' => 'Notification sent successfully.',
                ];
            } else {
                $results[] = [
                    'user_id' => $userId,
                    'status' => 'failed',
                    'message' => 'Failed to send notification.',
                ];
            }
        }
    }

    // Fire after notification event
    event(new AfterNotificationSent($results));

    // Optional: Include before and after event details in the response
    return response()->json([
        'results' => $results,
        'log_before' => 'Before event triggered (check log for details)',
        'log_after' => 'After event triggered (check log for details)',
    ]);
}


}
