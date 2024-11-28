<?php

namespace App\Services;
use App\Models\File;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CheckinRequest;



class CheckinService{

    public function checkin(CheckinRequest $request)
    {
        try {
            $user_id = Auth::id();
            DB::beginTransaction();


            $versions = Version::with('file')
                ->whereIn('id', $request->id)
                ->lockForUpdate()
                ->get();


            if ($versions->count() !== count($request->id)) {
                throw new \Exception('Some versions do not exist.');
            }

            $reservedFiles = [];


            foreach ($versions as $version) {
                if (!$version->file) {
                    throw new \Exception('No valid file associated with version: ' . $version->id);
                }
                $s=File::where('id',$version->file_id)->first();

                if ($s->state) {
                    $reservedFiles[] = $s->name;
                 //   throw new \Exception('Chosen files contain reserved files: ' . $s->name);
                }
            }
            if (!empty($reservedFiles)) {
                throw new \Exception('The following files are already reserved: ' . implode(', ', $reservedFiles));
            }


            foreach ($versions as $version) {
                $this->checkreserve($version->id, $user_id);
            }

            DB::commit();
            return response()->json(['message' => 'The files have been reserved successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function checkreserve($version_id, $user_id)
{
    try {
        DB::beginTransaction();

        $file = Version::with('file')->where('id', $version_id)->lockForUpdate()->first();
        if (!$file) {
            throw new \Exception('Version does not exist.');
        }

       $f= File::where('id',$file->file_id)->lockForUpdate()->first();
        $f->state = true;
        $file->reservedby = $user_id;
        $f->save();
        $file->save();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 400);
    }
}
}
