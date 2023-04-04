<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\SalesEntity;
use App\Models\Business\SalesPattern;
use App\Models\Business\SalesStep;
use Auth;
use Illuminate\Http\Request;

class SalesPatternsController extends Controller
{
    public function index()
    {
        $patterns = SalesPattern::where('team_id', Auth::user()->team->id)->get();

        return view(Auth::user()->team->app_type.'.patterns.index', compact(
                                                    'patterns'
                                                  ));
    }

    public function save(Request $request)
    {
        $pattern = new SalesPattern;
        $pattern->team_id = Auth::user()->team->id;
        $pattern->user_id = Auth::user()->id;
        $pattern->name = request('name');
        $pattern->save();

        return redirect()->back();
    }

    public function edit($id)
    {
        $pattern = SalesPattern::find($id);

        $prospect_types = SalesEntity::where('team_id', Auth::user()->team->id)
                                     ->get()->pluck('type')->unique()->toArray();

        return view(Auth::user()->team->app_type.'.patterns.edit', compact(
                                                    'pattern',
                                                    'prospect_types'
                                                  ));
    }

    public function update($pattern_id, Request $request)
    {
        $update = [];

        foreach ($request->input() as $field => $value) {
            $field = explode('_', $field);

            if ($field[0] == 'step') {
                if ($field[1] == 'order') {
                    $step_id = $field[2] * 1;
                    $update[$step_id]['the_order'] = $value * 1;
                }

                if ($field[1] == 'name') {
                    $step_id = $field[2] * 1;
                    $update[$step_id]['name'] = $value;
                }
            }
        }

        foreach ($update as $step_id => $fields) {
            $step = SalesStep::find($step_id);
            if ($step) {
                foreach ($fields as $field => $value) {
                    $step->$field = $value;
                }
                $step->save();
            }
        }

        if (request('new_step_name')) {
            $the_count = SalesPattern::find($pattern_id)->steps->max('the_order') + 1;

            $step = new SalesStep;
            $step->team_id = Auth::user()->team->id;
            $step->pattern_id = $pattern_id;
            $step->name = request('new_step_name');
            $step->the_order = $the_count;
            $step->save();
        }

        if (request('pattern_name')) {
            $pattern = SalesPattern::find($pattern_id);
            $pattern->name = request('pattern_name');
            $pattern->save();
        }

        if (request('default_type')) {
            $pattern = SalesPattern::find($pattern_id);
            $pattern->default_type = request('default_type');
            $pattern->save();

            // A Sales Entity Type can only be the default for one pattern at a time

            $clear_other_pattern_defaults = SalesPattern::where('team_id', Auth::user()->team->id)
                                                        ->where('id', '<>', $pattern_id)
                                                        ->where('default_type', $pattern->default_type)
                                                        ->get();

            foreach ($clear_other_pattern_defaults as $other) {
                $other->default_type = null;
                $other->save();
            }
        }

        return redirect()->back();
    }
}
