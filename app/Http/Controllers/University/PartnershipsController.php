<?php

namespace App\Http\Controllers\University;

use App\Entity;
use App\Http\Controllers\Controller;
use App\Partnership;
use App\PartnershipType;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class PartnershipsController extends Controller
{
    public function searchPrograms($entity_id, $v = null)
    {
        $programs = Partnership::select('program')
                               ->where('team_id', Auth::user()->team->id)
                               // ->where('program',$v)
                               ->where('program', 'like', '%'.$v.'%')
                               ->where('program', '<>', '')
                               ->groupBy('program')
                               ->get()
                               ->pluck('program');

        return view('u.entities.partnerships.list-programs', compact('programs'));
    }

    public function new($entity_id)
    {
        $entity = Entity::find($entity_id);

        $partnership_types = PartnershipType::where('team_id', $entity->team_id)->orderBy('name')->get();
        $department_ids = Partnership::where('team_id', $entity->team_id)->pluck('department_id');
        $departments = Entity::whereIn('id', $department_ids)->orderBy('name')->get();

        $example_program = Partnership::inRandomOrder()
                                   ->where('team_id', Auth::user()->team_id)
                                   ->where('program', '<>', '')
                                   ->take(1)
                                   ->pluck('program')
                                   ->first();

        return view('u.entities.partnerships.new', compact('entity', 'partnership_types', 'departments', 'example_program'));
    }

    public function store(Request $request, $entity_id)
    {
        $entity = Entity::find($entity_id);

        $validate_array = $request->all();
        $validator = Validator::make($validate_array, [
            'program' => ['required'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $partnership = new Partnership;
        $partnership->team_id = Auth::user()->team->id;
        $partnership->user_id = Auth::user()->id;
        $partnership->partner_id = $entity->id;
        $partnership->partner_name = $entity->name;
        $partnership->program = request('program');

        $partnership->data = ['faculty'   => '',
                              'filer'    => '' ];

        $partnership->save();

        return redirect('/u/entities/'.$entity->id.'/partnerships/'.$partnership->id.'/edit');
    }

    public function update(Request $request, $entity_id, $partnership_id, $close = null)
    {
        $entity = Entity::find($entity_id);
        
        $partnership = Partnership::find($partnership_id);
        //dd($entity, $partnership_id);
        $validate_array = $request->all();

        if (trim(request('new_type'))) {
            $type = new PartnershipType;
            $type->team_id = Auth::user()->team->id;
            $type->user_id = Auth::user()->id;
            $type->name = request('new_type');
            $type->save();

            $partnership->partnership_type_id = $type->id;
        } else {
            $partnership->partnership_type_id = request('partnership_type_id');
        }

        if (trim(request('new_year'))) {
            $partnership->year = Carbon::parse(request('new_year'))->format('Y-m-d');
        } else {
            $partnership->year = request('year');
        }

        $contacts = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 5) == 'name_') {
                $contact_id = substr($key, 5);

                // if ((request('name_'.$contact_id) != null) ||
                //     (request('email_'.$contact_id) != null)) {
                //         $contacts[] = ['name'   =>  request('name_'.$contact_id),
                //                        'email'  =>  request('email_'.$contact_id)];
                // }

                // if (request('email_'.$contact_id) != null) {
                //     $validate_array['emails'][] = request('email_'.$contact_id);
                // }

                if (
                    (request('name_'.$contact_id) != null) ||
                    (request('phone_'.$contact_id) != null) ||
                    (request('email_'.$contact_id) != null)
                ) {
                    $contacts[] = ['name'   =>  request('name_'.$contact_id),
                                   'phone'   =>  request('phone_'.$contact_id),
                                   'email'  =>  request('email_'.$contact_id), ];
                }
            }
        }

        $validator = Validator::make($validate_array, [
            'program' => ['required', 'max:200'],
            'emails.*' => ['email'],
            'notes' => ['max:1000'],
        ],
        [
            'email' => 'Error in email address.',
        ]);

        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $partnership->contacts = $contacts;
        $partnership->notes = request('notes');
        $partnership->program = request('program');

        if (trim(request('new_department'))) {
            $department = new Entity;
            $department->type = 'NU Department'; //NOT NEU!
            $department->team_id = Auth::user()->team->id;
            $department->user_id = Auth::user()->id;
            $department->name = request('new_department');
            $department->save();

            $partnership->department_id = $department->id;
        } else {
            $partnership->department_id = request('department_id');
        }

        $partnership->data = ['faculty'   => request('faculty'),
                                         'filer'    => request('filer'), ];
        $partnership->save();

        if ($close) {
            return redirect('u/entities/'.$entity->id);
        } else {
            return redirect('u/entities/'.$entity->id.'/partnerships/'.$partnership->id.'/edit');
        }
    }

    public function edit($entity_id, $partnership_id)
    {
        $entity = Entity::find($entity_id);

        $partnership_types = PartnershipType::where('team_id', $entity->team_id)->orderBy('name')->get();
        $department_ids = Partnership::where('team_id', $entity->team_id)->pluck('department_id');
        $departments = Entity::whereIn('id', $department_ids)->orderBy('name')->get();

        $partnership = Partnership::find($partnership_id);

        $partnership_years = Partnership::select('year')
                                        ->where('team_id', Auth::user()->team->id)
                                        ->groupBy('year')
                                        ->orderBy('year', 'desc')
                                        ->pluck('year');

        return view('u.entities.partnerships.edit', compact('entity', 'partnership_types', 'departments', 'partnership', 'partnership_years'));
    }
}
