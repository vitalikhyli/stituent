<?php

// namespace App\Http\Controllers\Office;

// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;

// use App\Search;

// use App\Voter;
// use App\Person;
// use App\Group;
// use App\Municipality;

// use Auth;

// use Illuminate\Support\Facades\Request as Input;

// use Response;

// use Carbon\Carbon;

class SearchesController__OLD extends Controller
{
    private static $blade = 'office';
    private static $dir = '/office';

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // AJAX STYLE
    //
    ////////////////////////////////////////////////////////////////////////////////////

    public function saveSearchForm()
    {
        $input = request()->input();
        $thesearch = new Search;
        $thesearch->form = $input;
        $thesearch->name = $input['search_name'];
        $thesearch->team_id = Auth::user()->team->id;
        $thesearch->user_id = Auth::user()->id;
        $thesearch->save();

        return $thesearch->id;
    }

    public function loadSearchForm($id)
    {
        $thesearch = Search::find($id);

        $request = $thesearch->form;

        $city_codes = Voter::withTrashed()->distinct()->select('city_code')->get();

        $municipalities = Municipality::whereIn('code', $city_codes)
                                      ->orderBy('name')
                                      ->get();

        $zips = Voter::withTrashed()->distinct()->select('address_zip')->orderBy('address_zip')->pluck('address_zip');

        return view('shared-features.constituents.sidebar', compact('thesearch', 'request', 'municipalities', 'city_codes', 'zips'));
    }

    public function view($id)
    {
        $output = $this->getResult($id);
        $output = $output->orderBy('address_zip')->orderBy('last_name');
        $output = $output->paginate(30);

        $search = Search::find($id);

        return view(self::$blade.'.searches.result', compact('search', 'output'));
    }

    public function getResult($id)
    {
        $thesearch = Search::find($id);

        $output = Person::select('id','full_name','full_address',
                                'first_name', 'last_name',
                                'address_number', 'address_fraction', 'address_street', 'address_apt',
                                'address_city', 'address_state', 'address_zip')
                          ->where('team_id', Auth::user()->team->id)
                          ->whereRaw($thesearch->sql)
                          ->orderBy('full_name');

        if ($thesearch->scope_voters == 1) {
            $output = Voter::select('id','full_name','full_address',
                                  'first_name', 'last_name',
                                  'address_number', 'address_fraction', 'address_street', 'address_apt',
                                  'address_city', 'address_state', 'address_zip')
                         ->whereNotIn('id', Person::select('voter_id')
                                                 ->where('team_id', Auth::user()->team->id)
                                                 ->whereRaw($thesearch->sql)
                                      )
                         ->whereRaw($thesearch->sql)
                         ->union($output)
                         ->orderBy('full_name');
        }

        return $output;
    }

    public function exportToCSV($id)
    {
        $headers = [
            'Content-Type' => 'text/csv',
        ];

        $output = $this->getResult($id);
        $output = $output->get()->toArray();

        $filename = 'CF-Export-'.Carbon::now()->format('Y-m-d').' '.time().'.csv';

        if (! file_exists(storage_path().'/app/user_exports/')) {
            mkdir(storage_path().'/app/user_exports/', 0777, true);
        }
        $filename_full = storage_path().'/app/user_exports/'.$filename;

        $file = fopen($filename_full, 'w');
        fputcsv($file, array_keys($output[0]));
        foreach ($output as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        return Response::download($filename_full, $filename, $headers);
    }

    public function labelsIndex()
    {
        $list_to_use = 0;

        $list_to_use = Input::get('list_to_use');

        if ($list_to_use) {
            $output = $this->getResult($list_to_use);
            $output = $output->orderBy('address_zip')->orderBy('last_name');
            // $output = $output->get();
            $output = $output->paginate(30);
        } else {
            $output = collect([]);
        }

        $list_options = Search::where('team_id', Auth::user()->team->id)
                              ->where('archived', 0)
                              ->get();

        return view(self::$blade.'.searches.labels-5160', compact('list_options', 'list_to_use', 'output'));
    }

    public function labelsShow(Request $request)
    {
        $list_to_use = request('list_to_use');

        if ($list_to_use != 0) {
            $list_options = Search::where('team_id', Auth::user()->team->id)->get();

            $output = $this->getResult($list_to_use);
            $output = $output->orderBy('address_zip')->orderBy('last_name');
            $output = $output->paginate(30);

            return view(self::$blade.'.searches.labels-5160', compact('list_options', 'list_to_use', 'output'));
        } else {
            return redirect(self::$dir.'/labels');
        }
    }

    public function index(Request $request)
    {
        $list_options = Search::where('team_id', Auth::user()->team->id)
                              ->where('archived', 0)
                              ->orderBy('created_at', 'desc')
                              ->get();

        $list_options_archived = Search::where('team_id', Auth::user()->team->id)
                              ->where('archived', 1)
                              ->orderBy('created_at', 'desc')
                              ->get();

        return view(self::$blade.'.searches.main', compact('list_options', 'list_options_archived'));
    }

    public function edit($id)
    {
        $list = Search::find($id);

        $new_id = '01';

        $new_options = ['full_name',
                             'full_address',
                             'gender',
                             'address_city',
                             'party', ];

        if (! $list->terms) {
            $terms = null;
        } else {
            $terms = collect(json_decode($list->terms));
            // $terms = $terms->reverse();
            $terms = $terms->sortBy('term');
            $new_id = str_pad($terms->count() + 1, 2, '0', STR_PAD_LEFT);

            // $new_options = array_diff($new_options, array_keys($terms));
        }

        return view(self::$blade.'.searches.edit', compact('list', 'terms', 'new_options', 'new_id'));
    }

    public function sqlArrayParse($sql, $arr)
    {
        // $q = substr($q, 1, -1);

        foreach ($arr as $q) {
            preg_match_all('/^\(.*\)$/', $q, $p);
            $arr = $this->sqlArrayParse($p, $arr);
        }
    }

    public function new()
    {
        return view(self::$blade.'.searches.new');
    }

    public function save(Request $request)
    {
        $list = new Search();

        $list->name = request('name');
        $list->team_id = Auth::user()->team->id;

        $list->save();

        return redirect(self::$dir.'/search/'.$list->id.'/edit');
    }

    public function update(Request $request, $id, $close = null)
    {
        $list = Search::find($id);

        $json_terms = [];

        $new_id = 1;

        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 5) == 'term_') {
                $term_value = request($key);
                $term_name = substr($key, 8);
                $term_id = substr($key, 5, 2);

                if ($term_value != null) {
                    $json_terms[] = ['id'       => str_pad($new_id++, 2, '0', STR_PAD_LEFT),
                                     'term'     => $term_name,
                                     'value'    => $term_value, ];
                }
            }
        }

        $sql = null;

        $terms = collect($json_terms);
        $grouped_terms = $terms->groupBy('term');
        foreach ($grouped_terms as $group) {
            $sql .= '(';
            $count = 0;
            foreach ($group as $term) {
                if ($count != 0) {
                    $sql .= ' OR ';
                }
                $sql .= $term['term'].' = "'.$term['value'].'"';
                $count++;
            }
            $sql .= ') AND ';
        }

        $sql = substr($sql, 0, -5); // Removes last superfluous AND

        $list->name = request('name');
        $list->archived = (request('archived')) ? 1 : 0;
        $list->team_id = request('team_id');
        $list->scope_voters = request('scope_voters');
        $list->terms = json_encode($json_terms);
        $list->sql = $sql;

        $list->save();

        if ($close) {
            return redirect(self::$dir.'/search/');
        } else {
            return redirect(self::$dir.'/search/'.$list->id.'/edit');
        }
    }

    public function bulkGroupsIndex()
    {
        $list_to_use = 0;
        $group_to_use = 0;

        $list_to_use = Input::get('list_to_use');
        $group_to_use = Input::get('group_to_use');

        if ($list_to_use) {
            $output = $this->getResult($list_to_use);
            // $output = $output->paginate(10);
            $output = $output->get();
        } else {
            $output = collect([]); //EMPTY
        }

        $list_options = Search::where('team_id', Auth::user()->team->id)
                              ->orderBy('created_at', 'desc')
                              ->get();

        $group_options = Group::where('team_id', Auth::user()->team->id)
                               ->orderBy('created_at', 'desc')
                               ->get();

        return view(self::$blade.'.searches.bulkgroups', compact('list_options', 'list_to_use', 'group_options', 'group_to_use', 'output'));
    }

    public function bulkGroupsShow(Request $request)
    {
        $list_to_use = request('list_to_use');
        $group_to_use = request('group_to_use');

        if (! $list_to_use || ! $group_to_use) {
            return back();
        } else {
            $output = $this->getResult($list_to_use);
            $output = $output->get();
            // $output = $output->paginate(10);

            $list_options = Search::where('team_id', Auth::user()->team->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

            $group_options = Group::where('team_id', Auth::user()->team->id)
                                   ->orderBy('created_at', 'desc')
                                   ->get();

            return view(self::$blade.'.searches.bulkgroups', compact('list_options', 'list_to_use', 'group_options', 'group_to_use', 'output'));
        }
    }
}
