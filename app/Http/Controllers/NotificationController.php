<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FcmService;
use App\Models\User;


use App\Events\BeforeNotificationSent;
use App\Events\AfterNotificationSent;

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

/*
public function sendGroupInvitationNotifications(array $userIds, string $token)
{
    $results = [];

    foreach ($userIds as $userId) {
        // You can still check if the user exists, if needed
        $user = User::find($userId);
        if ($user) { 
            // Send notification to all users with the same token
            $title = "Notification";
            $body = "You have been invited to join a group.";
            $response = $this->fcmService->sendNotification($token, $title, $body);

            // Check if notification was successful or not
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

    // Return a summary of the notification results
    return $results;
}
*/
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
