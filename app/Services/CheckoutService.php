<?php
namespace App\Services;

use App\Models\File;
use App\Models\Version;
use Illuminate\Http\Request;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CheckoutRequest;
use App\Repositories\CheckoutRepository;
use App\Http\Requests\ShowCheckoutFilesRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\NotificationController;
use App\Events\BeforeNotificationSent;
use App\Events\AfterNotificationSent;

class CheckoutService
{
    protected $CheckoutRepo;
    protected $fileservice;

    protected $notificationController;

    public function __construct(CheckoutRepository $CheckoutRepo ,NotificationController $notificationController)
    {
        $this->CheckoutRepo = $CheckoutRepo;
        
        $this->notificationController = $notificationController;

    }

    public function checkout(CheckoutRequest $request, string $token)
{
    try {
        $user_id = Auth::id();
        DB::beginTransaction();

        // Retrieve the version and file details
        $version = Version::with('file')->where('id', $request->version_id)->lockForUpdate()->first();
        if (!$version) {
            throw new \Exception('Version not found.');
        }

        $file_id = $version->file_id;
        $file = File::find($file_id);
        if (!$file) {
            throw new \Exception('File not found.');
        }

        // Validate the file upload
        $fileExtension = $request->file->getClientOriginalExtension();
        $fileName = time() . '.' . $fileExtension;
        $filePath = 'files';
        $request->file->move(public_path($filePath), $fileName);
        $relativePath = $filePath . '/' . $fileName;

        // Check conditions for successful checkout
        $newname = $request->file->getClientOriginalName();
        if ($file->name === $newname && $version->reservedby === $user_id && $file->state == 1) {
            // Perform checkout operation
            $this->CheckoutRepo->checkout($version, $file, $user_id, $relativePath);

            // Send notifications to users with the same file_id, passing the token
            $this->notificationController->sendNotificationcheckout($file_id, $token);

            DB::commit();
            return response()->json(['message' => 'The file has been checked out successfully'], 200);
        } else {
            throw new \Exception('Invalid conditions for checkout.');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 400);
    }
}


  /*
public function checkout(CheckoutRequest $request)
    {
        $user_id=Auth::id();
        $version_id=$request->version_id;
        $version=Version::where('id',$version_id)->first();
        $file_id=$version->file_id;
        $file=File::where('id',$file_id)->first();



        $fileExtension = $request->file->getClientOriginalExtension();
        $fileName = time() . '.' . $fileExtension;
        $filePath = 'files';
        $request->file->move(public_path($filePath), $fileName);

        $relativePath = $filePath . '/' . $fileName;

    $newname=$request->file->getClientOriginalName();


        if($file->name==$newname && $version->reservedby==$user_id && $file->state==1)
        {
           $this->CheckoutRepo->checkout($version , $file , $user_id, $relativePath);

           return response()->json(['message' => 'The file has been checked out successfully'], 200);
        }
        else{
            return response()->json(['error'], 400);
        }

    }



  */
public function showfilesforcheckout(ShowCheckoutFilesRequest $request){

    $user_id=Auth::id();

    $response=$this->CheckoutRepo->showfilesforcheckout($user_id,$request->group_id);
    return $response;
}


}
