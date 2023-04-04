<?php

namespace App\Http\Controllers;

use App\Directory;
use App\Group;
use App\GroupFile;
use App\Person;
use App\PersonFile;
use App\Team;
use App\Traits\ConstituentQueryTrait;
use App\WorkCase;
use App\WorkCaseWorkFile;
use App\WorkFile;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Response;

class FilesController extends Controller
{
    use ConstituentQueryTrait;

    public function unlinkPerson($app_type, $file_id, $person_id)
    {
        $pivots = PersonFile::where('file_id', $file_id)
                                  ->where('person_id', $person_id)
                                  ->where('team_id', Auth::user()->team->id)
                                  ->get();

        foreach ($pivots as $thepivot) {
            $thepivot->delete();
        }

        return redirect()->back();
    }

    public function unlinkGroup($app_type, $file_id, $group_id)
    {
        $pivots = GroupFile::where('file_id', $file_id)
                                  ->where('group_id', $group_id)
                                  ->where('team_id', Auth::user()->team->id)
                                  ->get();

        foreach ($pivots as $thepivot) {
            $thepivot->delete();
        }

        return redirect()->back();
    }

    public function unlinkCase($app_type, $file_id, $case_id)
    {
        $pivots = WorkCaseWorkFile::where('file_id', $file_id)
                                  ->where('case_id', $case_id)
                                  ->where('team_id', Auth::user()->team->id)
                                  ->get();

        foreach ($pivots as $thepivot) {
            $thepivot->delete();
        }

        return redirect()->back();
    }

    public function update(Request $request, $app_type, $file_id, $close = null, $return_string = null)
    {
        $file = WorkFile::find($file_id);
        $file->description = request('description');
        $file->directory_id = request('directory_id');
        $file->save();

        if (request('case_id')) {
            $case = WorkCase::find(request('case_id'));
            $this->authorize('basic', $case);

            $pivot = WorkCaseWorkFile::where('case_id', $case->id)
                                     ->where('file_id', $file_id)
                                     ->first();
            if (! $pivot) {
                $new_pivot = new WorkCaseWorkFile;
                $new_pivot->case_id = $case->id;
                $new_pivot->file_id = $file_id;
                $new_pivot->team_id = Auth::user()->team->id;
                $new_pivot->save();
            }
        }

        foreach ($request->all() as $key => $input) {
            if (substr($key, 0, 6) == 'group_') {
                $group_id = substr($key, 6, strlen($key));
                $group = Group::find($group_id);
                $this->authorize('basic', $group);
                $pivot = GroupFile::where('group_id', $group_id)
                                  ->where('file_id', $file_id)
                                  ->first();
                if (! $pivot) {
                    $new_pivot = new GroupFile;
                    $new_pivot->group_id = $group_id;
                    $new_pivot->file_id = $file_id;
                    $new_pivot->team_id = Auth::user()->team->id;
                    $new_pivot->save();
                }
            }
        }

        foreach ($request->all() as $key => $input) {
            if (substr($key, 0, 7) == 'person_') {
                $person_id = substr($key, 7, strlen($key));
                $person = findPersonOrImportVoter($person_id, Auth::user()->team->id);
                $this->authorize('basic', $person);
                $pivot = PersonFile::where('person_id', $person->id)
                                   ->where('file_id', $file_id)
                                   ->first();
                if (! $pivot) {
                    $new_pivot = new PersonFile;
                    $new_pivot->person_id = $person->id;
                    $new_pivot->file_id = $file_id;
                    $new_pivot->team_id = Auth::user()->team->id;
                    $new_pivot->save();
                }
            }
        }

        if ($close != 'close') {
            return redirect('/'.$app_type.'/files/'.$file_id.'/edit/'.$return_string);
        } else {
            return redirect(base64_decode($return_string));
        }
    }

    public function edit($app_type, $id, $return_string = null)
    {
        $file = WorkFile::find($id);

        $top_level_directories = Directory::where('team_id', Auth::user()->team->id)
                                          ->whereNull('parent_id')
                                          ->get();

        $categories = Auth::user()->team->categories;

        $cases = WorkCase::where('team_id', Auth::user()->team->id)
                         ->orderBy('date', 'desc')
                         ->get();

        $return_string = (isset($return_string)) ? $return_string : base64_encode(url()->previous());

        return view('shared-features.files.edit-file', compact('file', 'return_string', 'top_level_directories', 'categories', 'cases', 'return_string'));
    }

    public function searchPeople($app_type, $file_id, $v = null, $return_string = null)
    {
        // dd($v);
        $v = trim($v);
        $mode_all = 1;
        $search_value = $v;

        if ($v == null || strlen($v) <= 1) {
            return null;
        } elseif (strlen($v) > 1) {
            $people = $this->getPeopleFromName($v);
        }

        $group = WorkFile::find($file_id);

        //Remove people already selected
        $attached_people = DB::table('person_file')
                             ->where('file_id', $file_id)
                             ->get()
                             ->pluck('person_id')
                             ->toArray();

        $people = $people->whereNotIn('id', $attached_people);

        return view('shared-features.files.ajax-people', compact('return_string', 'people',
                                                        'mode_all',
                                                        'search_value'));
    }

    public function search($app_type, $scope = null, $search_v = null, $return_string = null)
    {
        switch ($scope) {

            case 'all':

                $files = WorkFile::where('team_id', Auth::user()->team->id)
                                 ->where('name', 'like', '%'.$search_v.'%')
                                 ->orderBy('name')
                                 ->get();

                return view('shared-features.files.list', compact('return_string', 'files', 'search_v'));

                break;

            case 'cases':

                $files = WorkFile::where('team_id', Auth::user()->team->id)
                                 ->where('name', 'like', '%'.$search_v.'%')
                                 ->orderBy('name')
                                 ->pluck('id')
                                 ->toArray();

                $cases = WorkCaseWorkFile::where('team_id', Auth::user()->team->id)
                                         ->whereIn('file_id', $files)
                                         ->pluck('case_id')
                                         ->toArray();

                $cases = WorkCase::with('files')
                                 ->where('team_id', Auth::user()->team->id)
                                 ->whereIn('id', $cases)
                                 ->orderBy('created_at', 'desc')
                                 ->get();

                return view('shared-features.files.list-cases', compact('return_string', 'cases', 'search_v'));

                break;

            case 'groups':

                $files = WorkFile::where('team_id', Auth::user()->team->id)
                                 ->where('name', 'like', '%'.$search_v.'%')
                                 ->orderBy('name')
                                 ->pluck('id')
                                 ->toArray();

                $groups = GroupFile::where('team_id', Auth::user()->team->id)
                                         ->whereIn('file_id', $files)
                                         ->pluck('group_id')
                                         ->toArray();

                $groups = Group::with('files')
                                 ->where('team_id', Auth::user()->team->id)
                                 ->whereIn('id', $groups)
                                 ->orderBy('name')
                                 ->get();

                return view('shared-features.files.list-groups', compact('return_string', 'groups', 'search_v'));

                break;

            case 'constituents':

                $files = WorkFile::where('team_id', Auth::user()->team->id)
                                 ->where('name', 'like', '%'.$search_v.'%')
                                 ->orderBy('name')
                                 ->pluck('id')
                                 ->toArray();

                $people = PersonFile::where('team_id', Auth::user()->team->id)
                                         ->whereIn('file_id', $files)
                                         ->pluck('person_id')
                                         ->toArray();

                $people = Person::with('files')
                                 ->where('team_id', Auth::user()->team->id)
                                 ->whereIn('id', $people)
                                 ->orderBy('last_name')
                                 ->get();

                return view('shared-features.files.list-constituents', compact('return_string', 'people', 'search_v'));

                break;
        }
    }

    public function index($app_type)
    {
        $which_file_view = Auth::user()->getMemory('which_file_view');

        if ($which_file_view == 'cases') {
            return redirect('/'.Auth::user()->team->app_type.'/files/list/cases');
        } elseif ($which_file_view == 'groups') {
            return redirect('/'.Auth::user()->team->app_type.'/files/list/groups');
        } elseif ($which_file_view == 'directories') {
            return redirect('/'.Auth::user()->team->app_type.'/files/list/directories');
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/files/list/all');
        }
    }

    public function indexAll($app_type, $return_string = null)
    {
        if (! isset($return_string)) {
            $return_string = base64_encode(url()->current());
        }

        Auth::user()->addMemory('which_file_view', 'all');

        $files = WorkFile::where('team_id', Auth::user()->team->id)
                        ->orderBy('name')
                        ->get();

        return view('shared-features.files.index', compact('files', 'return_string'));
    }

    public function indexCases($app_type, $return_string = null)
    {
        if (! isset($return_string)) {
            $return_string = base64_encode(url()->current());
        }

        Auth::user()->addMemory('which_file_view', 'cases');

        $cases = WorkCaseWorkFile::where('team_id', Auth::user()->team->id)
                                 ->pluck('case_id')
                                 ->toArray();

        $cases = WorkCase::with('files')
                         ->where('team_id', Auth::user()->team->id)
                         ->whereIn('id', $cases)
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('shared-features.files.index-cases', compact('cases', 'return_string'));
    }

    public function indexGroups($app_type, $return_string = null)
    {
        if (! isset($return_string)) {
            $return_string = base64_encode(url()->current());
        }

        Auth::user()->addMemory('which_file_view', 'groups');

        $groups = GroupFile::where('team_id', Auth::user()->team->id)
                           ->pluck('group_id')
                           ->toArray();

        $groups = Group::with('files')
                       ->where('team_id', Auth::user()->team->id)
                       ->whereIn('id', $groups)
                       ->orderBy('name')
                       ->get();

        return view('shared-features.files.index-groups', compact('groups', 'return_string'));
    }

    public function indexConstituents($app_type, $return_string = null)
    {
        if (! isset($return_string)) {
            $return_string = base64_encode(url()->current());
        }

        Auth::user()->addMemory('which_file_view', 'constituents');

        $people = PersonFile::where('team_id', Auth::user()->team->id)
                           ->pluck('person_id')
                           ->toArray();

        $people = Person::with('files')
                       ->where('team_id', Auth::user()->team->id)
                       ->whereIn('id', $people)
                       ->orderBy('last_name')
                       ->get();

        return view('shared-features.files.index-constituents', compact('people', 'return_string'));
    }

    public function indexDirectories($app_type, $return_string = null)
    {
        if (! isset($return_string)) {
            $return_string = base64_encode(url()->current());
        }

        Auth::user()->addMemory('which_file_view', 'directories');

        if (request()->open) {
            Auth::user()->openFileDirectory(request()->open);
        }
        if (request()->close) {
            Auth::user()->closeFileDirectory(request()->close);
        }

        $directories = Directory::with('files')
                                ->where('team_id', Auth::user()->team->id)
                                ->get();

        return view('shared-features.files.index-directories', compact('directories', 'return_string'));
    }

    public function delete($app_type, $id, $return_string = null)
    {
        $file = WorkFile::find($id);

        $file_to_delete = config('app.user_upload_dir').$file->path;

        $success = Storage::delete($file_to_delete);

        if (! $success) {

            // session()->flash('msg', 'ERROR -- '.$file->name.' not deleted.');
        } else {
            $pivots = PersonFile::where('file_id', $file->id)->get();
            foreach ($pivots as $thepivot) {
                $thepivot->delete();
            }

            $pivots = WorkCaseWorkFile::where('file_id', $file->id)->get();
            foreach ($pivots as $thepivot) {
                $thepivot->delete();
            }

            $pivots = GroupFile::where('file_id', $file->id)->get();
            foreach ($pivots as $thepivot) {
                $thepivot->delete();
            }

            $file->delete();
        }

        return redirect(base64_decode($return_string));
    }

    public function uploadImage($app_type)
    {
        // https://a1websitepro.com/store-image-uploads-on-server-with-summernote-not-base-64/3/
        if (empty($_FILES['file'])) {
            exit();
        }
        $errorImgFile = '/images/uploadfailed.jpg';

        $newfilename = round(microtime(true)).'_'.$_FILES['file']['name'];
        $newfilename = $this->filter_filename($newfilename, true);
        $teampublicfolder = Auth::user()->team->public_folder;
        $destinationFilePath = $teampublicfolder.'/'.$newfilename;
        if (! move_uploaded_file($_FILES['file']['tmp_name'], $destinationFilePath)) {
            echo $errorImgFile;
        } else {
            echo config('app.url').Auth::user()->team->public_url.'/'.$newfilename;
        }
    }

    public function filter_filename($filename, $beautify = true)
    {

        // https://stackoverflow.com/questions/2021624/string-sanitizer-for-filename
        // sanitize filename
        $filename = preg_replace(
            '~
            [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
            [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
            [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
            [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
            [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
            ~x',
            '-', $filename);
        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        if ($beautify) {
            $filename = $this->beautify_filename($filename);
        }
        // maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)).($ext ? '.'.$ext : '');

        return $filename;
    }

    public function beautify_filename($filename)
    {
        // reduce consecutive characters
        $filename = preg_replace([
            // "file   name.zip" becomes "file-name.zip"
            '/ +/',
            // "file___name.zip" becomes "file-name.zip"
            '/_+/',
            // "file---name.zip" becomes "file-name.zip"
            '/-+/',
        ], '-', $filename);
        $filename = preg_replace([
            // "file--.--.-.--name.zip" becomes "file.name.zip"
            '/-*\.-*/',
            // "file...name..zip" becomes "file.name.zip"
            '/\.{2,}/',
        ], '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        // ".file-name.-" becomes "file-name"
        $filename = trim($filename, '.-');

        return $filename;
    }

    public function download($app_type, $id)
    {
        $file = WorkFile::find($id);
        //dd($file);
        if ($file->team_id != Auth::user()->team->id) {
            return;
        }

        $path = $file->path;

        $internalpath = storage_path().'/app/'.config('app.user_upload_dir').$path;

        //dd($path);

        //dd($the_file_to_get);

        if (! file_exists($internalpath)) {
            session()->flash('msg', 'ERROR -- '.$file->name.' does not exist');
        }

        try {

            // $ext = substr($file->name, strrpos($file->name, '.') + 1);

            ob_end_clean();

            //$path = storage_path().'/app/user_files'.$the_file_to_get;

            //$path =
            $streamfile = File::get($internalpath);
            $type = File::mimeType($internalpath);

            if (in_array($type, ['image/jpg',
                                 'image/png',
                                 'image/gif',
                                 'application/pdf', ])) {
                $response = Response::make($streamfile, 200);
                $response->header('Content-Type', $type);
            } else {

              //dd("Laz2");

                $response = response()->download($internalpath, $file->name);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect()->back();
        }

        return $response;
    }

    public function upload(Request $request, $app_type, $options = null)
    {
        if (empty($request->file('fileToUpload'))) {
            return back();
        }

        //GET FILE
        try {
            $file = $request->file('fileToUpload');
        } catch (\Exception $e) {
            return back();
        }

        //GET OPTIONS
        $options = base64_decode($options);
        $options = json_decode($options, true);

        //CREATE MODEL
        $model = new WorkFile;
        $model->name = $file->getClientOriginalName();
        $model->user_id = Auth::user()->id;
        $model->team_id = Auth::user()->team->id;
        if (! isset($options['directory_id'])) {
            $model->directory_id = Auth::user()->team->defaultDirectory();
        } else {
            $model->directory_id = $options['directory_id'];
        }
        $model->save();

        //CALCULATE PATH AND SAVE FILE
        $name_to_save = str_pad($model->id, 8, '0', STR_PAD_LEFT).'_'.$model->name;
        $dir = '/'.Auth::user()->team->app_type.'/team_'
                          .str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT);

        try {

            $path = $file->storeAs(config('app.user_upload_dir').$dir, $name_to_save);

        } catch (\Exception $e) {

            session()->flash('msg', 'Error uploading file.');

            $model->delete();

            return back();
        }

        //SAVE PATH TO MODEL
        $model->path = $dir.'/'.$name_to_save;
        $model->save();

        //ATTACH TO CASE
        if (isset($options['case_id'])) {
            $thecase = WorkCase::find($options['case_id']);
            $this->authorize('basic', $thecase);
            $model->cases()->attach($thecase, ['team_id' => Auth::user()->team->id]);
        }

        //ATTACH TO PERSON
        if (isset($options['person_id'])) {
            $theperson = findPersonOrImportVoter($options['person_id'], Auth::user()->team->id);
            $this->authorize('basic', $theperson);
            $model->people()->attach($theperson, ['team_id' => Auth::user()->team->id]);
        }

        //ATTACH TO GROUP
        if (isset($options['group_id'])) {
            $thegroup = Group::find($options['group_id']);
            $this->authorize('basic', $thegroup);
            $model->groups()->attach($thegroup, ['team_id' => Auth::user()->team->id]);
        }

        // if (request('edit_after_upload')) {

        //   return view('shared-features.files.edit', compact)

        // } else {

        return back();

        // }
    }

    public function showLogo($app_type, $team_id)
    {
        $team = Team::find($team_id);
        $the_file_to_get = 'app/'.config('app.user_upload_dir').$team->logo_img;
        $path = storage_path($the_file_to_get);
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header('Content-Type', $type);

        return $response;
    }

    public function uploadLogo(Request $request, $app_type)
    {
        if (empty($request->file('fileToUpload'))) {
            return back();
        }

        $the_file = $request->file('fileToUpload');
        $the_name = $the_file->getClientOriginalName();
        $the_ext = $the_file->getClientOriginalExtension();

        $the_dir = Auth::user()->team->app_type.'/team_'.Auth::user()->team->id;
        $the_path = $the_file->storeas(config('app.user_upload_dir').$the_dir, 'logo.'.$the_ext);

        $the_path_to_save = str_replace(config('app.user_upload_dir'), '', $the_path);

        $team = Team::find(Auth::user()->team->id);
        $team->logo_img = $the_path_to_save;
        $team->save();

        return back();
    }
}
