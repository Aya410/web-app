<?php
namespace App\Repositories;

use Carbon\Carbon;
use App\Models\File;
use App\Models\Version;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutRepository
{

public function checkout(Version $version, File $file, $user_id,$relativepath)
{
    $date = Carbon::today();
    $version_number=$version->number+1;

    Version::create([
'number'=>$version_number,
'reservedby'=>null,
'user_id'=>$user_id,
'file_id'=>$file->id,
'file'=>$relativepath,
'time'=>$date,
    ]);

$file->state=0;
$version->reservedby=null;

$file->save();
$version->save();

}

public function showfilesforcheckout($user_id, $group_id)
{
    $files = File::where('group_id', $group_id)
                 ->where('request_join', 1)
                 ->where('state', 1)
                 ->get();

    if ($files->isEmpty()) {
        return response()->json(['error' => 'No files found'], 404);
    }

    $filesWithVersions = [];

    foreach ($files as $file) {
        $version = Version::where('file_id', $file->id)
                          ->where('reservedby', $user_id)
                          ->orderBy('number', 'desc')
                          ->first();

        if ($version) {
            $filesWithVersions[] = [
                'name' => $file->name,
                'version' => $version->number,
                'path' => $version->file,
                'group_id' => $file->group_id,
                'file_id' => $version->file_id,
                'version_id' => $version->id,
            ];
        }
    }

    if (empty($filesWithVersions)) {
        return response()->json(['error' => 'No versions found'], 404);
    }

    return response()->json($filesWithVersions);
}


}
