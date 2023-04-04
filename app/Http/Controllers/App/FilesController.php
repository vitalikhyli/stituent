<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\WorkFile;
use Auth;
use Response;

class FilesController extends Controller
{
    public function show($id)
    {
        $file = WorkFile::find($id);
    
        if (!$file->currentUserCanAccess()) {
        	abort(403);
        }

        $path = $file->path;

        $internalpath = storage_path().'/app/'.config('app.user_upload_dir').$path;

        if (! file_exists($internalpath)) {
            session()->flash('msg', 'ERROR -- '.$file->name.' does not exist');
        }

        try {
            ob_end_clean();

            $streamfile = File::get($internalpath);
            $type = File::mimeType($internalpath);

            if (in_array($type, ['image/jpg',
                                 'image/png',
                                 'image/gif',
                                 'application/pdf', ])) {
                $response = Response::make($streamfile, 200);
                $response->header('Content-Type', $type);
            } else {

                $response = response()->download($internalpath, $file->name);
            }
        } catch (\Exception $e) {
            dd("Error: ".$e->getMessage());
        }

        return $response;
    }
}
