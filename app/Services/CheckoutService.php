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


class CheckoutService
{
    protected $CheckoutRepo;
    protected $fileservice;
    public function __construct(CheckoutRepository $CheckoutRepo )
    {
        $this->CheckoutRepo = $CheckoutRepo;

    }
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



public function showfilesforcheckout(ShowCheckoutFilesRequest $request){

    $user_id=Auth::id();

    $response=$this->CheckoutRepo->showfilesforcheckout($user_id,$request->group_id);
    return $response;
}


}
