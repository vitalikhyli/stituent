<?php

namespace App\Http\Controllers\Admin;

set_time_limit(-1);

use App\Http\Controllers\Controller;
use App\Models\Admin\DataFolder;
use App\Models\Admin\DataImport;
use App\Models\Admin\DataJob;
use App\Models\Admin\DataWorker;
use App\Team;
use App\Voter;
use Artisan;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Schema;

class ImportsController extends Controller
{
    ////////////////////////////////////////////////////////////////////////////////////////
    //
    //  AJAX / WORKERS
    //
    ////////////////////////////////////////////////////////////////////////////////////////

    public function checkInterruptedWorkersReturnPing()
    {
        $last_ping = 0;

        if (DataWorker::whereNull('deleted_at')->exists()) {
            $last_ping = abs(time() - DataWorker::whereNull('deleted_at')->max('ping'));

            if ($last_ping > 300) {
                $stalled = DataWorker::whereNull('deleted_at')
                                     ->where('ping', '<=', time() - $last_ping)
                                     ->get();

                foreach ($stalled as $thestalled) {
                    $thestalled->markInterrupted();
                }
            }
        }

        return $last_ping;
    }

    public function ajaxListTables()
    {
        $imports = DataImport::orderBy('team_id')->orderBy('created_at', 'desc')->get();
        $folders = DataFolder::all();
        $teams = Team::all();
        $slices = DataImport::where('slice_of_id', '<>', null)
                            ->where('archived', 0)
                            ->get();

        $last_ping = $this->checkInterruptedWorkersReturnPing();
        $jobs_to_do = DataJob::where('done', 0)->count();
        $unfinished_workers = DataWorker::whereNull('deleted_at')->count();

        return view('admin.import.list-tables', compact('folders', 'teams', 'imports', 'slices', 'last_ping', 'jobs_to_do', 'unfinished_workers'));
    }

    public function ajaxListSlices($id)
    {
        $theimport = DataImport::find($id);

        $slices = DataImport::where('slice_of_id', $id)
                            ->where('archived', 0)
                            ->orderBy('created_at', 'desc')
                            ->orderBy('updated_at', 'desc')
                            ->get();

        $last_ping = $this->checkInterruptedWorkersReturnPing();
        $jobs_to_do = DataJob::where('done', 0)->count();
        $unfinished_workers = DataWorker::whereNull('deleted_at')->count();

        return view('admin.import.list-slices', compact('theimport', 'slices', 'last_ping', 'jobs_to_do', 'unfinished_workers'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    //
    //  BASIC PAGES
    //
    ////////////////////////////////////////////////////////////////////////////////////////

    public function dataTableIndex()
    {
        $include = $this->ajaxListTables();

        return view('admin.import.data', compact('include'));
    }

    public function uploadIndex()
    {
        $uploads = DataImport::orderBy('team_id')
                             ->where('file_path', '<>', null)
                             ->orderBy('created_at', 'desc')->get();

        $step = 1;

        return view('admin.import.upload', compact('uploads', 'step'));
    }

    public function edit($id)
    {
        $theimport = DataImport::find($id);

        $copies = DataImport::where('parent_id', $id)->get();

        $parent = DataImport::find($theimport->parent_id);

        if (! $theimport->parent_id) {
            $slices_of_parent = null;
        } else {
            $slices_of_parent = DataImport::where('slice_of_id', $theimport->parent_id)
                                          ->where('archived', 0)
                                          ->get();
        }

        $into_mergeable = DataImport::where('team_id', $theimport->team_id)
                                    ->where('id', '<>', $theimport->id)
                                    ->where('deployed', 0)
                                    ->where('archived', 0)
                                    ->where('type', 'v')
                                    ->orderBy('count', 'desc')
                                    ->get();

        if (! Schema::hasColumn($theimport->active_table, 'merge_report')) {
            $merged_history = null;
        } else {
            $merged_history = DB::table($theimport->activetable)->where('merge_report', '<>', null)->get();
        }

        $include = $this->ajaxListSlices($id);

        return view('admin.import.edit', compact('theimport', 'include', 'slices_of_parent', 'copies', 'parent', 'into_mergeable', 'merged_history'));
    }

    public function save(Request $request, $id, $close = null)
    {
        $import = DataImport::find($id);
        $import->name = request('name');
        $import->notes = request('notes');
        $import->slice_sql = request('slice_sql');
        $import->save();

        if (request('new_slice_sql')) {
            if (request('new_slice_name')) {
                $slice = new DataImport('v', request('new_slice_team_id'), request('new_slice_name'));
                $slice->save();

                $arguments = ['slice_of_id'    => $import->id,
                   'slice_sql'      => request('new_slice_sql'),
                    ];

                (new DataJob)->add('defineSlice', $slice->id, $arguments);
                (new DataJob)->add('populateSlice', $slice->id);
                (new DataJob)->add('createHouseholds', $slice->id);
                (new DataJob)->add('ready', $slice->id);
            }
        }

        session()->flash('startworker', 1);

        if ($close) {
            return redirect('/admin/data');
        } else {
            return redirect()->back();
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    //
    //  DATA FUNCTIONS TRIGGERED FROM USER INTERFACE
    //
    ////////////////////////////////////////////////////////////////////////////////////////

    public function merge(Request $request)
    {
        $import = DataImport::find(request('merge_into_id')); //No copy

        $arguments = ['merge_this_id' => request('merge_this_id'),
                      'merge_into_id' => request('merge_into_id'), ];

        (new DataJob)->add('notReady', $import->id);
        (new DataJob)->add('merge', $import->id, $arguments);
        (new DataJob)->add('clearHouseholds', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        (new DataJob)->add('ready', $import->id);

        session()->flash('startworker', 1);

        return redirect('/admin/data/');
    }

    public function deploy($id)
    {
        $import = DataImport::find($id);
        $import->deploy($id);
        $import->deployHouseholds($id);

        return back();
    }

    public function archive($id)
    {
        $import = DataImport::find($id);
        $import->archive();
        $import->relatedHouseholds()->archive();

        return back();
    }

    public function copy($id)
    {
        $import = DataImport::find($id);
        $copy = $import->copy();    // Copies main voters
        $copy->createHouseholds();  // Generates Households

        return back();
    }

    public function moveSlicePointers($id)
    {
        $import = DataImport::find($id);
        $import->moveSlicePointers();

        return back();
    }

    public function repopulateSlices($id)
    {
        $slices = DataImport::where('slice_of_id', $id)->where('archived', 0)->get();
        foreach ($slices as $theslice) {
            $theslice->updateSlice();
        }

        return back();
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    //
    //  UPLOADS
    //
    ////////////////////////////////////////////////////////////////////////////////////////

    public function voterTableColumns()
    {
        $all_columns = Schema::getColumnListing('x__template_voters');

        $columns_disregard = ['created_at',
                                    'updated_at',
                                    'deleted_at',
                                    'created_by',
                                    'updated_by',
                                    'deleted_by',
                                    'full_name',
                                    'full_name_middle',
                                    'household_id',
                                    'full_address',
                                    'archived_at',
                                    'origin_method', ];

        $columns_add = ['{SKIP}'];

        $columns_replace = ['dob'               => '{DATETIME} dob',
                                    'registration_date' => '{DATETIME} registration_date',
                                    'deceased_date'     => '{DATETIME} deceased_date', ];

        $available_columns = array_diff($all_columns, $columns_disregard);

        $available_columns = array_merge($columns_add, $available_columns);

        $keys = array_keys($columns_replace);
        foreach ($available_columns as $k => $v) {
            if (in_array($v, $keys)) {
                $available_columns[$k] = $columns_replace[$v];
            }
        }

        return $available_columns;
    }

    public function AIGuessHeaders($sample)
    {
        $header = [];
        $total = count($sample);
        $a = 0;
        for ($i = 0; $i < $total; $i++) {

            //Setup
            $c = trim($sample[$i][0]);
            if ($i > 0) {
                $p = $sample[($i - 1)][0];
            } else {
                $p = null;
            }
            if ($i + 1 < $total) {
                $n = $sample[$i + 1][0];
            } else {
                $n = null;
            }
            $prev = end($header);
            $guess = '{SKIP}';
            // $street_types = array(' ST',' DR',' LN',' AVE',' TRL',' RD',' HTS',' TPKE',' TER',' PATH',' CIR',' VLG',' EXT',' WAY',' ROW');

            //Logic

            if ((preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $c)) ||
                (preg_match('/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/', $c))) {

                // Is Date

                $guess = '{DATETIME} dob';
                if ($prev == '{DATETIME} dob') {
                    $guess = '{DATETIME} registration_date';
                }
            } elseif (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $c)) {

                // Is Alphanumeric

                if ($i <= 2) {
                    $guess = 'id';
                }
            } elseif (is_numeric($c)) {
                if ($i == 0) {
                    $guess = '{SKIP}';
                } //Import Order
                // if ($next == 'voter_status') { $guess = 'house_district'; }
            } elseif (! is_numeric($c)) {
                if ($prev == 'id') {
                    $guess = 'last_name';
                }
                if ($prev == 'last_name') {
                    $guess = 'first_name';
                }
                if ($prev == 'first_name') {
                    $guess = 'middle_name';
                }
                if ($prev == 'middle_name') {
                    $guess = 'name_title';
                }

                if (Str::endsWith($c, ' AVE')) {
                    $guess = 'address_street';
                }
                if ($prev == 'address_street') {
                    $guess = 'address_apt';
                }
                if ($prev == 'address_apt') {
                    $guess = 'address_zip';
                }

                if (($c == 'R') || ($c == 'D') || ($c == 'U')) {
                    $guess = 'party';
                }

                if (($c == 'F') || ($c == 'M')) {
                    $guess = 'gender';
                }

                if (($c == 'A') || ($c == 'I')) {
                    $guess = 'voter_status';
                }
            }

            //Assign
            $header[] = $guess;
        }

        // dd($header);

        return $header;

        // return '["{SKIP}","id","last_name","first_name","middle_name","name_title","address_number","address_fraction","address_street","address_apt","address_zip","{SKIP}","{SKIP}","{SKIP}","{SKIP}","{SKIP}","party","gender","{DATETIME} registration_date","{DATETIME} dob","{SKIP}","{SKIP}","congress_district","senate_district","house_district","voter_status"]';
        //     }
    }

    public function detectDelimiter($fh)
    {
        //https://stackoverflow.com/questions/26717462/php-best-approach-to-detect-csv-delimiter
        $delimiters = ["\t", ';', '|', ','];
        $data_1 = [];
        $data_2 = [];
        $delimiter = $delimiters[0];
        foreach ($delimiters as $d) {
            $data_1 = fgetcsv($fh, 4096, $d);
            if (count($data_1) > count($data_2)) {
                $delimiter = count($data_1) > count($data_2) ? $d : $delimiter;
                $data_2 = $data_1;
            }
            rewind($fh);
        }

        return $delimiter;
    }

    public function arrays_side_by_side()
    // BASED ON: https://stackoverflow.com/questions/10204749/how-to-combine-two-multidimentinal-arrays-side-by-side-in-php
    {
        $arrList = func_get_args();
        $retval = [];
        foreach ($arrList as $array) {
            foreach ($array as $key=>$arrsub) {
                $retval[$key][] = $arrsub;
            }
        }

        return $retval;
    }

    public function upload(Request $request, $step)
    {

        // dd($step);

        // PICK TEAM
        if ($step == 2) {
            $team_id = request('team_id');

            $uploads = DataImport::where('team_id', request('team_id'))
                                 ->where('file_path', '<>', null)
                                 ->where('team_id', $team_id)
                                 ->orderBy('created_at', 'desc')
                                 ->get();

            return view('admin.import.upload', compact('step', 'uploads', 'team_id'));
        }

        // UPLOAD FILE --> NEW DATAIMPORT, DETECT DELIMITER
        if ($step == 3) {
            $team_id = request('team_id');

            $uploads = null; //Do not show list of other imports

            $name = request('import_name');

            $import = new DataImport('v', $team_id, $name);
            $import->setGroupSlug();

            $the_file = $request->file('file');

            $handle = fopen($the_file, 'r');

            $the_name = $the_file->getClientOriginalName();
            $the_ext = $the_file->getClientOriginalExtension();
            $the_dir = Auth::user()->team->app_type.'/team_'.Auth::user()->team->id;

            // $the_path = $the_file->store(config('app.user_upload_dir').$the_dir);
            $the_path = $the_file->storeAs(config('app.user_upload_dir').$the_dir, $import->slug.'.'.$the_ext);

            $import->delimiter = $this->detectDelimiter($handle);
            $import->file_stored = 1;
            $import->file_hash = md5_file(base_path().'/storage/app/'.$the_path);
            $import->file_path = base_path().'/storage/app/'.$the_path;
            $import->save();

            $sample[] = fgetcsv($handle, 1000, $import->delimiter);
            $sample[] = fgetcsv($handle, 1000, $import->delimiter);
            $sample[] = fgetcsv($handle, 1000, $import->delimiter);

            $first_lines = $this->arrays_side_by_side($sample[0], $sample[1], $sample[2]);
            $first_lines = json_decode(json_encode($first_lines), false);

            //dd($first_lines);

            $previous_headers = DataImport::where('header_columns', '<>', null)->get();

            $previous_extras = DataImport::where('extra_columns', '<>', null)->get();

            $header = $this->AIGuessHeaders($first_lines);

            $available_columns = $this->voterTableColumns();

            return view('admin.import.upload', compact('step', 'uploads','team_id','import',
                'first_lines', 'previous_headers', 'previous_extras', 'header', 'available_columns'));
        }

        // ASSIGN COLUMNS AND START WORKER
        if ($step == 4) {
            $import_id = request('import_id');

            $import = DataImport::find($import_id);

            if (! request('reuse_header')) {
                $header = [];
                for ($i = 0; $i <= request('total_headers'); $i++) {
                    if (! request('header_'.$i)) {
                        $header[$i] = '{SKIP}';
                    } else {
                        $header[$i] = request('header_'.$i);
                    }
                }
            } else {
                $header = json_decode(DataImport::find(request('reuse_header'))->header_columns, true);
            }

            if (! request('reuse_extra')) {
                $extra = [
                            'address_city' => request('extra_address_city'),
                            'address_state' => request('extra_address_state'),
                            'state' => request('extra_state'),
                          ];
            } else {
                $extra = json_decode(DataImport::find(request('reuse_extra'))->extra_columns, true);
            }

            if (request('skip_first')) {
                $import->skip_first = 1;
            }
            $import->header_columns = json_encode($header);
            $import->extra_columns = json_encode($extra);
            $import->save();

            (new DataJob)->add('import', $import->id);
            (new DataJob)->add('enrich', $import->id);
            (new DataJob)->add('createHouseholds', $import->id);
            (new DataJob)->add('ready', $import->id);

            session()->flash('startworker', 1);

            return redirect('/admin/data/');
        }
    }
}
