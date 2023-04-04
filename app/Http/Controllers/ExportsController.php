<?php

namespace App\Http\Controllers;

use App\Person;
use App\Search;
use App\Traits\ConstituentQueryTrait;
use App\Traits\ExportTrait;
use App\Voter;
use Auth;
use Illuminate\Http\Request;
use Response;

class ExportsController extends Controller
{
    use ConstituentQueryTrait;
    use ExportTrait;

    public function show($app_type, $id)
    {
        $thesearch = Search::find($id);

        $input = $thesearch->form;

        $people = $this->constituentQuery($input);
        $municipalities = $this->getMunicipalities();
        $districts = $this->getDistricts();
        $zips = $this->getZips();
        $categories = Auth::user()->team->categories;
        $fields = $this->getConstituentFields();

        $total_count = $this->total_count;

        return view('shared-features.exports.index', compact('input', 'thesearch', 'people', 'total_count', 'municipalities', 'districts', 'zips', 'categories', 'fields'));
    }

    public function index()
    {
        $people = $this->constituentQuery(request()->input());
        $fields = $this->getConstituentFields();
        $municipalities = $this->getMunicipalities();
        $districts = $this->getDistricts();
        $zips = $this->getZips();
        $categories = Auth::user()->team->categories;

        $total_count = $this->total_count;

        $input = request()->input();

        return view('shared-features.exports.index',
                    compact('input', 'people', 'total_count', 'districts', 'municipalities', 'zips', 'fields', 'categories'));
    }

    public function download(Request $request, $app_type)
    {
        if (!Auth::user()->permissions->export) return
            dd();
        set_time_limit(-1);

        $input          = unserialize(base64_decode($request->input('search_form')));
        $constituents   = $this->constituentQuery($input, $limit = 'none');
        $column_names   = $input['fields'];
        $filename       = $request['file_name'];
        $include_groups = isset($input['include_groups']) ? $input['include_groups'] : false;
        $include_voter_phones = isset($input['include_voter_phones']) ? $input['include_voter_phones'] : false;
        $householding   = isset($input['householding']) ? true : false;

        if (!$filename) return;
        if (!$constituents) return;

        $file_array = $this->createCSVFileFromConstituents($input,
                                                           $constituents,
                                                           $column_names,
                                                           $filename,
                                                           $include_groups,
                                                           $include_voter_phones,
                                                           $householding);

        $headers        = $file_array['headers'];
        $filename_full  = $file_array['filename_full'];
        $filename       = $file_array['filename'];

        return Response::download($filename_full, $filename, $headers);
    }
}
