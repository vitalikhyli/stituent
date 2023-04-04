<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\UserUpload;
use App\UserUploadData;
use Artisan;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Schema;

class UserUploadsController extends Controller
{

    public function pasteAndLabelPage()
    {
        return view('shared-features.useruploads.paste');
    }

    public function delete($app_type, $id)
    {
        $upload = UserUpload::find($id);

        //Test that upload belongs to the correct user/team:
        if (!$upload || $upload->team_id != Auth::user()->team->id) {
            return redirect()->back()->with('msg', 'There was an error deleting this file.');
        }

        //Delete model:
        $name = $upload->name;
        $upload->delete();

        return redirect()->back()->with('msg', 'Upload "'.$name.'" deleted.');
        
        // Delete File:
        $file = storage_path().'/app/'.$upload->file;

        if (file_exists($file)) {

            try {

                unlink($file);

            } catch (\Exception $e) {

                return redirect()->back()->with('msg', 'There was an error deleting this file: '.$upload->file);

            }

        }

        //Delete model:
        $name = $upload->name;
        $upload->delete();

        return redirect()->back()->with('msg', 'Upload "'.$name.'" deleted.');
    }

    public function latest($app_type, $id)
    {
        $upload = UserUpload::find($id);

        if ($upload->matched_count == $upload->count) {
            return redirect('/'.Auth::user()->team->app_type.'/useruploads/'.$id.'/integrate');
        }

        if ($upload->imported_count == $upload->count) {
            return redirect('/'.Auth::user()->team->app_type.'/useruploads/'.$id.'/match');
        }

        return redirect('/'.Auth::user()->team->app_type.'/useruploads/'.$id.'/import');
    }

    public function index($app_type)
    {
        $uploads = UserUpload::thisTeam()->latest()->get();

        return view('shared-features.useruploads.index', compact('uploads'));
    }

    public function uploadFile($app_type)
    {
        if (! request()->file('fileToUpload')) {
            return redirect()->back();
        }

        $storage_sub = 'user_uploads/'.str_pad(Auth::user()->team->id, 4, '0', STR_PAD_LEFT);

        if (! file_exists(storage_path().'/app/'.$storage_sub)) {
            mkdir(storage_path().'/app/'.$storage_sub, 0777, true);
        }

        $path = request()->file('fileToUpload')->store($storage_sub);
        $full_path = storage_path().'/app/'.$path;

        $upload = new UserUpload;
        $upload->user_id = Auth::user()->id;
        $upload->team_id = Auth::user()->team->id;
        $upload->file = $path;
        $upload->file_size = filesize($full_path);
        $upload->hash = md5(file_get_contents($full_path));
        $upload->name = request()->file('fileToUpload')->getClientOriginalName();
        $upload->save();

        // Get Header

        $handle = fopen($full_path, 'r');
        $result = fgets($handle);
        $delimiter = ',';
        if (Str::contains($result, '|')) {
            $delimiter = '|';
        }

        $file = new \SplFileObject($full_path, 'r');
        $first_row = $file->fgetcsv($delimiter);
        foreach ($first_row as $key => $column) {
            $first_row[$key] = Str::slug($column);
        }
        $upload->columns = $first_row;
        $upload->save();

        // Create Empty Column Matches
        $upload->column_matches = [];

        // Create Empty Column Map
        // $column_map = [];
        // foreach($upload->columns as $column) {
        //     $column_map[Str::slug($column)] = [0 => ['action'    => null,
        //                                             'qual'      => null,
        //                                             'if'        => null,
        //                                             'if-qual'   => null
        //                                             ]];
        // }
        // $upload->column_map     = $column_map;
        $upload->save();

        // Count Lines
        $linecount = 0;
        $handle = fopen($full_path, 'r');
        while (! feof($handle)) {
            $line = fgetcsv($handle);
            if ($line) {
                $linecount++;
            } else {
                echo 'ERROR: Mismatch: '.print_r($line);
            }
        }
        fclose($handle);
        $upload->count = $linecount - 1;
        $upload->save();

        Artisan::queue('cf:user_upload_import --upload_id='.$upload->id.' --team_id='.Auth::user()->team->id);

        return redirect('/'.Auth::user()->team->app_type.'/useruploads/'.$upload->id.'/import');
    }

    public function import($app_type, $id)
    {
        $upload = UserUpload::find($id);

        $full_path = storage_path().'/app/'.$upload->file;

        $file = new \SplFileObject($full_path, 'r');

        $handle = fopen($full_path, 'r');
        $result = fgets($handle);
        $delimiter = ',';
        if (Str::contains($result, '|')) {
            $delimiter = '|';
        }

        $limit = 8;

        $preview = [];

        for ($i = 1; $i <= $limit; $i++) {
            if ($i == 1) {
                continue;
            }

            $rawrow = $file->fgetcsv($delimiter);

            if (! $rawrow) {
                continue;
            }

            foreach ($rawrow as $index => $val) {
                $preview[$i][] = $val;
            }
        }

        $preview_count = count($preview);

        return view('shared-features.useruploads.edit', compact('upload', 'preview', 'preview_count'));
    }

    public function match($app_type, $id)
    {
        $upload = UserUpload::find($id);

        if ($upload->matched_count != $upload->count) {
            Artisan::queue('cf:user_upload_match --upload_id='.$upload->id
                            .' --team_id='.Auth::user()->team->id
                            .' --user_id='.Auth::user()->id);
        }

        // Preload Column Map with Column Matches Suggestions Unless already populated

        if (! $upload->column_map) {
            $column_map = [];

            foreach ($upload->columns as $column) {
                $column_map[Str::slug($column)] = [0 => ['action'    => null,
                                                        'qual'      => null,
                                                        'if'        => null,
                                                        'if-qual'   => null,
                                                        ]];
            }

            foreach ($upload->column_matches as $match) {
                if ($match['db'] == 'primary_email') {
                    $column_map[$match['user']] = [0 => ['action'     => 'email-primary',
                                                         'qual'       => 'primary_email',
                                                         'if'         => null,
                                                         'if-qual'    => null, ],
                                                        ];
                } else {
                    $column_map[$match['user']] = [0 => ['action'     => 'replace',
                                                         'qual'       => $match['db'],
                                                         'if'         => null,
                                                         'if-qual'    => null, ],
                                                    ];
                }
            }

            $upload->column_map = $column_map;
            $upload->save();
        }

        return view('shared-features.useruploads.match', compact('upload'));
    }

    public function integrate($app_type, $id)
    {
        $upload = UserUpload::find($id);

        if (UserUploadData::where('team_id', $upload->team_id)
                                            ->where('upload_id', $upload->id)
                                            ->whereNull('integrated_at')
                                            ->count() > 0) {
            $this->createBackUpTable($app_type, $team = $upload->team_id);

            Artisan::queue('cf:user_upload_integrate --upload_id='.$upload->id
                            .' --team_id='.Auth::user()->team->id
                            .' --user_id='.Auth::user()->id);
        }

        return view('shared-features.useruploads.integrate', compact('upload'));
    }

    public function createBackUpTable($app_type, $team_id)
    {
        if ($app_type == 'office') {
            $table = 'people';
            $model = \App\Person::class;
        }
        if ($app_type == 'campaign') {
            $table = 'participants';
            $model = \App\Participant::class;
        }

        $new_table_name = 'bkup_'.
                        str_pad($team_id, 4, 0, STR_PAD_LEFT).'_'
                        .$table.'_'
                        .str_replace('-', '_', Str::slug(Carbon::now()->toDateTimeString()));

        $database_configs = Config::get('database.connections');
        $main = $database_configs['main']['database'];
        $archive = $database_configs['archive']['database'];

        DB::connection('archive')->statement('CREATE TABLE '.$archive.'.'.$new_table_name.' LIKE '.$main.'.'.$table);

        DB::connection('archive')->statement('INSERT INTO '.$archive.'.'.$new_table_name.' SELECT * FROM '.$main.'.'.$table.' where team_id = '.$team_id);
    }
}
