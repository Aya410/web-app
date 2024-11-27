<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FcmService;
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
    
}
