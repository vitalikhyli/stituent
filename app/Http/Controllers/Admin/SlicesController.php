<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\VoterSlice;
use DB;
use Illuminate\Http\Request;
use Schema;

use Carbon\Carbon;


class SlicesController extends Controller
{

    public function delete($id)
    {
        $slice = VoterSlice::find($id);
        $slice->delete();
        return redirect('/admin/slices');
    }

    public function update(Request $request, $id, $close = null)
    {
        $state = request('state') ? request('state') : 'XX';
        $name = str_replace(' ', '_', request('name'));
        $slice = VoterSlice::find($id);
        $slice->name    = 'x_'.$state.'_'.$name;
        $slice->master  = request('master_table');
        $slice->sql     = (request('sql')) ? request('sql') : '{SQL}';
        $slice->save();

        if ($close) {
            return redirect('/admin/slices');
        } else {
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $slice = VoterSlice::find($id);

        $tables = DB::connection('voters')->select('SHOW TABLES');
        $master_table_list = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $table_name) {
                if (substr($table_name, -7) == '_master'
                    && substr($table_name, 0, 8) == 'x_voters') {

                    $master_table_list[] = $table_name;
                }
            }
        }

        return view('admin.slices.edit', compact('slice', 'master_table_list'));
    }

    public function store()
    {
        $slice = new VoterSlice;
        $slice->name = 'x_';
        $slice->sql = '{SQL}';
        $slice->save();
        return redirect('/admin/slices/'.$slice->id.'/edit');
    }

    public function index()
    {
        $included = []; //['voter_slices'];
        $included_prefixes = ['x_',
                               'z_', ];
        $excluded = ['x__template_households',
                               'x__template_voters',
                               'x_voters_ma_master',
                               'x_households_ma_master', ];
        $included_suffixes = ['_hh'];

        // $from_db    = env('DB_DATABASE');
        // $to_db      = env('DB_VOTER_DATABASE');
        $tables = DB::select('SHOW TABLES');

        $table_list = [];

        foreach ($tables as $table) {
            foreach ($table as $key => $table_name) {

                // $table_name = strtolower($table_name);

                if (
                    (in_array($table_name, $included)) ||
                    (in_array(substr($table_name, 0, 2), $included_prefixes))
                    ) {
                    if (! in_array($table_name, $excluded) &&
                        (! in_array(substr($table_name, -3), $included_suffixes))
                       ) {
                        $table_list[] = $table_name;
                    }
                }
            }
        }

        // $slices = VoterSlice::whereIn('name', $table_list)->orderBy('name')->get();
        $slices = VoterSlice::orderBy('name')->get();

        $slices_array = $slices->pluck('name')->toArray();

        $orphaned = array_diff($table_list, $slices_array);

        foreach ($orphaned as $orphan) {
            $new = new VoterSlice;
            $new->name = $orphan;
            $new->orphaned = true;
            $slices->push($new);
        }

        $slices = collect($slices);
        $slices = $slices->sortBy('name');

        $slices = $slices->each(function ($item) {
                                $item['state'] = substr($item['name'], 2, 2);
                                $item['table_exists'] = (Schema::hasTable($item['name'])) ? true: false;
                           });

        return view('admin.slices.index', compact('slices'));
    }
}
