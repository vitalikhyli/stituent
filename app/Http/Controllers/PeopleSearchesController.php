<?php

namespace App\Http\Controllers;

use App\Municipality;
use App\Search;
use App\Traits\ConstituentQueryTrait;
use App\Traits\ExportTrait;
use App\Voter;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;

class PeopleSearchesController extends Controller
{
    use ExportTrait;
    use ConstituentQueryTrait;

    public function show($app_type, $id)
    {
        $thesearch = Search::find($id);

        $input = $thesearch->form;

        $people = $this->constituentQuery($input);
        $municipalities = $this->getMunicipalities();
        $zips = $this->getZips();

        $total_count = $this->total_count;

        return view('shared-features.constituents.index', compact('input', 'thesearch', 'people', 'total_count', 'municipalities', 'zips'));
    }

    public function export($app_type, $id)
    {
        $thesearch = Search::find($id);

        $fields = $this->getConstituentFields();

        $thecount = $this->constituentQuery($thesearch->form)->count();

        return view('shared-features.searches.export', compact('thesearch', 'fields', 'thecount'));
    }

    public function download(Request $request, $app_type, $search_id)
    {
        $search = Search::find($search_id);

        $output = $this->constituentQuery($search->form, $limit = 'none', $fields = $request->fields);

        $output = $output->toArray();

        // dd($request->fields, $output);

        $headers = [
            'Content-Type' => 'text/csv',
        ];

        //$filename = 'CF-Export-'.Carbon::now()->format('Y-m-d').' '.time().'.csv';
        $filename = $request['file_name'];

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

    public function index($app_type)
    {
        $team_searches = Search::where('team_id', Auth::user()->team->id)
                                ->where('user_id', '<>', Auth::user()->id)
                                ->get();

        $my_searches = Search::where('user_id', Auth::user()->id)->get();

        return view('shared-features.searches.index', compact('my_searches', 'team_searches'));
    }

    public function delete($app_type, $id)
    {
        $search = Search::find($id);
        $search->delete();

        return redirect()->back();
    }

    public function save($app_type)
    {
        $input = request()->input();

        $thesearch = new Search;

        $thesearch->form = $this->getQueryFormFields(request());

        // if($input['fields']) {
        //     $fields = explode(',', $input['constituents-list-form-fields']);
        //     $thesearch->fields  = $fields;
        // }

        $thesearch->name = $input['search_name'];
        $thesearch->team_id = Auth::user()->team->id;
        $thesearch->user_id = Auth::user()->id;

        $thesearch->save();

        //return redirect(Auth::user()->team->app_type.'/constituents/searches/'.$thesearch->id);

        return redirect()->back();
    }

    // public function update($app_type)
    // {

    //     $input = request()->input();

    //     $thesearch = Search::find($input['thesearch_id']);

    //     $thesearch->form = $this->getQueryFormFields(request());

    //     $thesearch->save();

    // 	return redirect(Auth::user()->team->app_type.'/constituents/searches/'.$thesearch->id);
    // }
}
