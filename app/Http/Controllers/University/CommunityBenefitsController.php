<?php

namespace App\Http\Controllers\University;

use App\CommunityBenefit;
use App\Entity;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityBenefitsController extends Controller
{
    public function index($mode = null)
    {
        $selected_year = (isset($_GET['year'])) ? $_GET['year'] : null;

        $benefits_by_year = CommunityBenefit::where('team_id', Auth::user()->team->id)
                                            ->orderBy('value', 'desc');

        $all_years = $benefits_by_year->get()
                                      ->groupBy('fiscal_year')
                                      ->sortKeysDesc()
                                      ->keys();

        if ($selected_year) {
            $benefits_by_year = $benefits_by_year->where('fiscal_year', $selected_year);
        }

        if ($mode == 'pilot') {
            $benefits_by_year = $benefits_by_year->wherePilot(true);
        }
        if ($mode == 'non') {
            $benefits_by_year = $benefits_by_year->wherePilot(false);
        }

        $benefits_by_year = $benefits_by_year->get()
                                             ->groupBy('fiscal_year')
                                             ->sortKeysDesc();

        $summary = [];

        foreach ($benefits_by_year as $year => $benefits) {
            $summary[$year]['programs'] = $benefits->count();
            $summary[$year]['cash'] = $benefits->where('value_type', 'Cash')->sum('value');
            $summary[$year]['inkind'] = $benefits->where('value_type', 'In Kind')->sum('value');
            $summary[$year]['both'] = $benefits->where('value_type', 'Both')->sum('value');
            $summary[$year]['total'] = $benefits->sum('value');
            $summary[$year]['beneficiaries'] = $benefits->unique('beneficiaries')->pluck('beneficiaries');
            //dd($summary);
        }

        return view('u.community-benefits.index', compact('mode', 'all_years', 'selected_year', 'benefits_by_year', 'summary'));
    }

    public function create($mode = null, $fiscal_year = null)
    {
        if (! $fiscal_year) {
            $year = Carbon::now()->format('Y');
            $fiscal_year = ($year == Carbon::now()->format('n') > 6) ? $year + 1 : $year;
        }

        return view('u.community-benefits.new', compact('fiscal_year', 'mode'));
    }

    public function store()
    {
        $cb = new CommunityBenefit;
        $cb->team_id = Auth::user()->team->id;

        $cb->fiscal_year = request('fiscal_year');
        $cb->pilot = request('pilot');
        $cb->program_name = request('program_name');
        $cb->program_description = request('program_description');
        $cb->value = request('value');
        $cb->value_type = request('value_type');
        $cb->time_frame = request('time_frame');
        $cb->beneficiaries = request('beneficiaries');
        $cb->initiators = request('initiators');
        $cb->partners = request('partners');

        $cb->save();

        return redirect('/u/community-benefits/'.$cb->id.'/edit');
    }

    public function edit($id)
    {
        $program = CommunityBenefit::find($id);

        return view('u.community-benefits.edit', compact('program'));
    }

    public function update(Request $request, $id, $close = null)
    {
        $program = CommunityBenefit::find($id);

        $program->team_id = Auth::user()->team->id;

        $program->fiscal_year = request('fiscal_year');
        $program->pilot = (request('pilot') == 1) ? true : false;
        $program->program_name = request('program_name');
        $program->program_description = request('program_description');
        $program->value = str_replace(',' ,'', request('value'));
        $program->value_type = request('value_type');
        $program->time_frame = request('time_frame');
        $program->beneficiaries = request('beneficiaries');
        $program->initiators = request('initiators');
        $program->partners = request('partners');

        $program->save();

        if (! $close) {
            return redirect('/u/community-benefits/'.$program->id.'/edit');
        }
        if ($close) {
            return redirect('/u/community-benefits/');
        }
    }

    public function delete($id)
    {
        $program = CommunityBenefit::find($id);
        $program->delete();

        return redirect('/u/community-benefits');
    }
}
