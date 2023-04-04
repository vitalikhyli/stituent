<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Import;
use Auth;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function paste()
    {
        return view('admin.uploads.paste');
    }

    public function indexStandalone()
    {
        return view('admin.uploads.index-standalone');
    }

    public function checkNullStatesToMA()
    {
        if(isset($_GET['nullStatesToMA'])) {
            $imports = Import::whereNull('state')->get();
            foreach($imports as $import) {
                $import->state = 'MA';
                $import->save();
            }
        }
    }

    public function index()
    {

        $this->checkNullStatesToMA();

        $uploads = Import::latest()->get();

        return view('admin.uploads.index', compact('uploads'));
    }

    public function uploadFile()
    {
        $path = request()->file('fileToUpload')->store('admin/imports');
        $city = request()->input('municipality_id');
        $import = new Import;
        $import->user_id = Auth::user()->id;
        $import->file = $path;
        $import->state = request()->input('state');
        $import->municipality_id = $city;
        $import->save();

        // Fix first line?
        return redirect('/admin/uploads/'.$import->id.'/edit');
    }

    public function edit($id)
    {

        $import = Import::find($id);
        //dd($import);
        return view('admin.uploads.edit', compact('import'));
    }

    public function download($id)
    {
        $import = Import::find($id);

        return response()->download(storage_path().'/app/'.$import->file);
    }
}
