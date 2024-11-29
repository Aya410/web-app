<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckinService;
use App\Services\CheckoutService;
use App\Http\Requests\CheckinRequest;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\ShowCheckoutFilesRequest;

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
       return $this->checkinservice->checkin($request);

    }


    public function checkout(CheckoutRequest $request)
    {
       return $this->checkoutservice->checkout($request);

    }


    public function showfilesforcheckout(ShowCheckoutFilesRequest $request)
{
    $response = $this->checkoutservice->showfilesforcheckout($request);
    return $response;
}


}
