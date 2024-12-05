<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckinService;
use App\Services\CheckoutService;
use App\Http\Requests\CheckinRequest;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\ShowCheckoutFilesRequest;

use App\Events\BeforeNotificationSent;
use App\Events\AfterNotificationSent;


class OperationController extends Controller
{

    protected $checkinservice;
    protected $checkoutservice;

    public function __construct(CheckinService $checkinservice, CheckoutService $checkoutservice)
    {
    $this->checkinservice = $checkinservice;
    $this->checkoutservice = $checkoutservice;
    }

    public function checkin(CheckinRequest $request)
    {

        $token = $request->input('token_device');  // Get the shared token from the request

       return $this->checkinservice->checkin($request,$token);

    }

/*
    public function checkout(CheckoutRequest $request)
    {
        
        $token = $request->input('token_device'); 
       return $this->checkoutservice->checkout($request,$token);

    }
*/
public function checkout(CheckoutRequest $request)
{
    // Get the device token from the request
    $token = $request->input('token_device');

    // Call the checkout service to perform the checkout and get the notification results
    $results = $this->checkoutservice->checkout($request, $token);

    // Return the response with the checkout success and notification results
    return response()->json([
        'message' => $results['message'], // Success message from the checkout service
      //  'newname' => $results['newname'], // New file name (if applicable)
        'notification_results' => $results['notification_results'] // Notification results
    ], 200);
}

    public function showfilesforcheckout(ShowCheckoutFilesRequest $request)
{
    $response = $this->checkoutservice->showfilesforcheckout($request);
    return $response;
}


}
