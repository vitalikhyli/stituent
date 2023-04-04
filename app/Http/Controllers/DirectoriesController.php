<?php

namespace App\Http\Controllers;

use App\Directory;
use App\WorkFile;
use Auth;
use Illuminate\Http\Request;

class DirectoriesController extends Controller
{
    public function edit($app_type, $id)
    {
        $directory = Directory::find($id);

        return view('shared-features.files.edit-directory', compact('directory'));
    }

    public function add(Request $request, $app_type)
    {
        $dir = new Directory;
        $dir->parent_id = request('parent_id');
        $dir->name = request('name');
        $dir->team_id = Auth::user()->team->id;
        $dir->save();

        $dir->depth = $dir->getDepth();
        $dir->save();

        return redirect()->back();
    }

    public function delete($app_type, $id)
    {
        $directory = Directory::find($id);

        $base = Directory::where('team_id', Auth::user()->team->id)
                         ->whereNull('parent_id')
                         ->first();

        if (! $directory->parent_id) {
            return redirect('/'.$app_type.'/files/');
        }

        foreach ($directory->files as $thefile) {
            $thefile->directory_id = $base->id;
            $thefile->save();
        }

        foreach ($directory->subModels() as $subdir) {
            $subdir->parent_id = $base->id;
            $subdir->save();
            $subdir->depth = $subdir->getDepth();
            $subdir->save();
        }

        $directory->delete();

        return redirect('/'.$app_type.'/files');
    }

    public function update(Request $request, $app_type, $id, $close = null)
    {
        $directory = Directory::find($id);
        $directory->name = request('name');
        $directory->save();

        if (! $close) {
            return redirect('/'.$app_type.'/files/directories/'.$id.'/edit');
        } else {
            return redirect('/'.$app_type.'/files');
        }
    }

    public function moveInto($app_type, $dir_id, $file_list = null)
    {
        if ($file_list) {
            $file_list = explode(',', $file_list);
            $file_list = WorkFile::where('team_id', Auth::user()->team->id)
                                 ->whereIn('id', $file_list)
                                 ->get();
            foreach ($file_list as $thefile) {
                $thefile->directory_id = $dir_id;
                $thefile->save();
            }
        }

        return redirect()->back();
    }
}
