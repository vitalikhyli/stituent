<?php

namespace App\Http\Controllers\University;

use App\Contact;
use App\Entity;
use App\EntityPerson;
use App\Http\Controllers\Controller;
use App\Partnership;
use App\ServiceLearningPartnership;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class EntitiesController extends Controller
{
    public function delete($entity_id)
    {
        $entity = Entity::find($entity_id);
        $this->authorize('basic', $entity);

        $entity->delete();

        return redirect('/'.Auth::user()->team->app_type.'/entities');
    }

    public function unlinkPerson($entity_id, $pivot_id)
    {
        $entity = Entity::find($entity_id);
        $this->authorize('basic', $entity);

        $pivot = EntityPerson::find($pivot_id);

        $pivot->delete();

        return redirect()->back(); //redirect('/'.Auth::user()->team->app_type.'/entities/'.$entity->id);
    }

    public function linkPerson(Request $request, $entity_id)
    {
        $person_id = request('relationship_person_id');
        $person = findPersonOrImportVoter($person_id, Auth::user()->team->id);
        $this->authorize('basic', $person);

        $entity = Entity::find($entity_id);
        $this->authorize('basic', $entity);

        $relationship_type = request('relationship_type');

        $pivot = EntityPerson::where('team_id', Auth::user()->team->id)
                             ->where('person_id', $person->id)
                             ->where('entity_id', $entity->id)
                             ->where('relationship', $relationship_type)
                             ->first();

        if (! $pivot) {
            $pivot = new EntityPerson;
            $pivot->team_id = Auth::user()->team->id;
            $pivot->user_id = Auth::user()->id;
            $pivot->person_id = $person->id;
            $pivot->entity_id = $entity->id;
            $pivot->relationship = $relationship_type;
            $pivot->save();
        }

        return redirect('/'.Auth::user()->team->app_type.'/entities/'.$entity->id);
    }

    public function updateRelationship(Request $request, $entity_id)
    {
        $person_id = request('relationship_person_id');
        $person = findPersonOrImportVoter($person_id, Auth::user()->team->id);
        $this->authorize('basic', $person);

        $entity = Entity::find($entity_id);
        $this->authorize('basic', $entity);

        $relationship_type = request('relationship_type');

        $pivot = EntityPerson::where('team_id', Auth::user()->team->id)
                             ->where('person_id', $person->id)
                             ->where('entity_id', $entity->id)
                             ->first();

        if ($pivot) {
            $pivot->user_id = Auth::user()->id;
            $pivot->relationship = $relationship_type;
            $pivot->save();
        }

        return redirect()->back();
    }

    public function deleteRelationship(Request $request, $entity_id)
    {
        $person_id = request('relationship_person_id');
        $person = findPersonOrImportVoter($person_id, Auth::user()->team->id);
        $this->authorize('basic', $person);

        $entity = Entity::find($entity_id);
        $this->authorize('basic', $entity);

        $pivot = EntityPerson::where('team_id', Auth::user()->team->id)
                             ->where('person_id', $person->id)
                             ->where('entity_id', $entity->id)
                             ->first();

        if ($pivot) {
            $pivot->delete();
        }

        return redirect()->back();
    }

    public function addContactToEntity(Request $request, $entity_id)
    {

        // VALIDATE
        $validator = Validator::make($request->all(), [
                'date' => ['date'],
                'time' => ['date_format:h:i A'],
                // 'subject' => ['required', 'max:255'],
                // 'notes' => ['required', 'max:255'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $theentity = Entity::find($entity_id);

        $this->authorize('basic', $theentity);

        $contact = new Contact;
        $contact->team_id = $theentity->team_id;
        $contact->user_id = Auth::user()->id;

        $date = request('date');
        if (request('use_time') == 1) {
            $time = Carbon::parse(request('time'))->format('H:i:s');
            $contact->date = $date.' '.$time;
        } else {
            $contact->date = $date;
        }

        if (request('person_followup')) {
            $contact->followup = 1;
            $contact->followup_on = request('person_followup_on');
        }

        // $contact->subject = request('subject');
        $contact->notes = request('notes');
        $contact->save();
        $contact->entities()->attach($theentity, ['team_id' => $contact->team_id]);

        return redirect('u/entities/'.$theentity->id);
    }

    public function deleteContact(Request $request, $model_id, $contact_id)
    {
        //Validation?

        $thecontact = Contact::find($contact_id);
        $this->authorize('basic', $thecontact);

        if (strpos(request()->url(), '/constituents/') !== false) {
            $originated = 'person';
        }
        if (strpos(request()->url(), '/entities/') !== false) {
            $originated = 'entity';
        }
        if (strpos(request()->url(), '/cases/') !== false) {
            $originated = 'case';
        }

        switch ($originated) {
            case 'person':
                $theperson = Person::find($model_id);
                $this->authorize('basic', $theperson);
                $return_string = 'u/constituents/'.$theperson->id;
                break;

            case 'entity':
                $entity = Entity::find($model_id);
                $this->authorize('basic', $entity);
                $return_string = 'u/entities/'.$entity->id;
                break;

            case 'case':
                $thecase = WorkCase::find($model_id);
                $this->authorize('basic', $thecase);
                $return_string = 'u/cases/'.$thecase->id;
                break;
        }

        $thecontact->delete();

        return redirect($return_string);
    }

    public function updateContact(Request $request, $model_id, $contact_id, $close = null)
    {
        //Validation?

        $thecontact = Contact::find($contact_id);
        $this->authorize('basic', $thecontact);

        if (strpos(request()->url(), '/constituents/') !== false) {
            $originated = 'person';
        }
        if (strpos(request()->url(), '/entities/') !== false) {
            $originated = 'entity';
        }
        if (strpos(request()->url(), '/cases/') !== false) {
            $originated = 'case';
        }

        switch ($originated) {
            case 'person':
                $theperson = Person::find($model_id);
                $this->authorize('basic', $theperson);
                $return_string = 'u/constituents/'.$theperson->id;
                break;

            case 'entity':
                $entity = Entity::find($model_id);
                $this->authorize('basic', $entity);
                $return_string = 'u/entities/'.$entity->id;
                break;

            case 'case':
                $thecase = WorkCase::find($model_id);
                $this->authorize('basic', $thecase);
                $return_string = 'u/cases/'.$thecase->id;
                break;
        }

        (request('followup')) ? $fu = 1 : $fu = 0;
        (request('followup_done')) ? $fu_done = 1 : $fu_done = 0;
        (request('followup_on')) ? $fu_on = Carbon::parse(request('followup_on'))->format('Y-m-d') : $fu_on = null;

        if (request('subject')) {
            $thecontact->subject = request('subject');
        } //Call Log
        if (request('type')) {
            $thecontact->type = request('type');
        }         //Call Log
        if (request('private')) {
            $thecontact->private = (request('private') == 'true') ? 1 : 0;
        }

        $thecontact->date = Carbon::parse(request('date'))->format('Y-m-d');
        $thecontact->notes = request('notes');
        $thecontact->followup = $fu;
        $thecontact->followup_on = $fu_on;
        $thecontact->followup_done = $fu_done;
        $thecontact->save();

        //SYNC CONNECTED PEOPLE + ENTITIES

        $entities = [];
        $people = [];

        foreach ($request->all() as $key => $value) {
            $input = explode('_', $key);
            if (isset($input[0])) {
                $add_this = ($input[0] == 'add') ? true : null;
            }
            $type = (isset($input[1])) ? $input[1] : null;
            $add_id = (isset($input[2])) ? $input[2] : null;

            if ($add_this) {
                if ($type == 'person') {
                    $add_id = findPersonOrImportVoter($add_id, Auth::user()->team->id);
                    $add_id = $add_id->id;
                    $people[] = ['person_id' => $add_id, 'team_id' => Auth::user()->team->id];
                }
                if ($type == 'entity') {
                    $entities[] = ['entity_id' =>$add_id, 'team_id' => Auth::user()->team->id];
                }
            }
        }

        $thecontact->people()->sync($people, ['team_id' => Auth::user()->team->id]);
        $thecontact->entities()->sync($entities, ['team_id' => Auth::user()->team->id]);

        //FINISH AND RETURN

        if ($close) {
            return redirect($return_string);
        } else {
            return redirect('u/constituents/'.$theperson->id.'/contacts/'.$thecontact->id.'/edit');
        }
    }

    public function editContact($entity, $contact_id)
    {
        $entity = Entity::find($entity);
        $thecontact = Contact::find($contact_id);

        $this->authorize('basic', $entity);
        $this->authorize('basic', $thecontact);

        $form_action = '/entities/'.$entity->id.'/contacts/'.$thecontact->id;

        return view('shared-features.contacts.edit', compact('thecontact', 'form_action'));
    }

    public function save(Request $request)
    {
        $entity = new Entity;
        $entity->name = request('name');
        $entity->team_id = Auth::user()->team->id;
        $entity->save();

        return redirect('u/entities/'.$entity->id.'/edit');
    }

    public function new($v = null)
    {
        $name = trim($v);

        return view('u.entities.new', compact('name'));
    }

    public function search($search_value)
    {
        $search_value = trim($search_value);

        $entities = Entity::with('partnerships')->where('team_id', Auth::user()->team->id);

        $entity_types = Entity::where('team_id', Auth::user()->team_id)
                              ->orderBy('type')
                              ->pluck('type')
                              ->unique();

        if ($search_value) {
            $entities->where('name', 'like', '%'.$search_value.'%');
        }

        $entities = $entities->orderBy('name')
                             ->get();

        return view('u.entities.list', compact('entities', 'search_value', 'entity_types'));
    }

    public function index()
    {
        $entity_types = Entity::where('team_id', Auth::user()->team_id)
                              ->orderBy('type')
                              ->pluck('type')
                              ->unique();

        $entities = Entity::with('partnerships')
                          ->where('team_id', Auth::user()->team->id)
                          ->orderBy('name')
                          ->get();

        $partnerships_count = Partnership::where('team_id', Auth::user()->team_id)
                                         ->count();

        return view('u.entities.index', compact('entities', 'entity_types', 'partnerships_count'));
    }

    public function edit($id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        $entity_types = Entity::where('team_id', $entity->team_id)
                              ->orderBy('type')
                              ->pluck('type')
                              ->unique();
        //dd($entity_types);

        return view('u.entities.edit', compact('entity', 'entity_types'));
    }

    public function editType($entity_id)
    {
        $entity = Entity::find($entity_id);
        if (request('new_type')) {
            $entity->type = request('new_type');
        } else {
            $entity->type = request('type');
        }
        if (!$entity->team_id) {
            $entity->team_id = Auth::user()->team_id;
        }
        $entity->save();

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        //VALIDATION START
        $validate_array = $request->all();

        $contact_info = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 5) == 'name_') {
                $contact_id = substr($key, 5);

                if ((request('name_'.$contact_id) != null) ||
                    (request('email_'.$contact_id) != null) ||
                    (request('phone_'.$contact_id) != null)
                    ) {
                    $contact_info[] = ['name'   =>  request('name_'.$contact_id),
                                           'email'  =>  request('email_'.$contact_id),
                                           'phone'  =>  $this->formatPhone(request('phone_'.$contact_id)),
                                        ];
                }

                if (request('email_'.$contact_id) != null) {
                    $validate_array['emails'][] = request('email_'.$contact_id);
                }
            }
        }

        // VALIDATE
        $validator = Validator::make($validate_array, [
                'name' => ['required', 'max:255'],
                'emails.*' => ['email'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        // UPDATE RECORD
        if (request('new_type')) {
            $entity->type = request('new_type');
        } else {
            $entity->type = request('type');
        }
        $entity->private = request('private');
        $entity->name = request('name');
        $entity->address_street = request('address_street');
        $entity->address_number = request('address_number');
        $entity->address_fraction = request('address_fraction');
        $entity->address_apt = request('address_apt');
        $entity->address_city = request('address_city');
        $entity->address_state = substr(request('address_state'), 0, 2);
        $entity->address_zip = substr(request('address_zip'), 0, 5);
        // $entity->email              = json_encode($emails_array);
        // $entity->phone              = json_encode($phones_array);
        $entity->contact_info = $contact_info;
        $entity->social_twitter = request('social_twitter');
        $entity->social_facebook = request('social_facebook');
        $entity->social_instagram = request('social_instagram');

        if (!$entity->team_id) {
            $entity->team_id = Auth::user()->team_id;
        }

        $entity->save();

        // COMPOUND FIELDS
        $entity->full_address = $entity->generateFullAddress();
        $entity->household_id = $entity->generateHouseholdId();

        $entity->save();

        session()->flash('msg', 'Entity was Saved!');

        if (request('save_and_close')) {
            return redirect('u/entities/'.$entity->id);
        } else {
            return redirect('u/entities/'.$entity->id.'/edit');
        }
    }

    public function show($id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        $partnership_years = Partnership::select('year')
                                        ->where('team_id', Auth::user()->team->id)
                                        ->where('partner_id', $entity->id)
                                        ->groupBy('year')
                                        ->orderBy('year', 'desc')
                                        ->pluck('year');

        $common_relationships = EntityPerson::select('relationship')
                                            ->where('team_id', Auth::user()->team->id)
                                            ->whereNotNull('relationship')
                                            ->where('relationship', '<>', '')
                                            ->groupBy('relationship')
                                            ->orderBy(\DB::raw('count(relationship)'), 'DESC')
                                            ->take(5)
                                            ->get();

        return view('u.entities.show', compact('entity', 'partnership_years', 'common_relationships'));
    }

    public function partnerships()
    {
        $slps = ServiceLearningPartnership::where('team_id', Auth::user()->team_id)->get();

        return view('u.entities.service-learning-partnerships', compact('slps'));
    }
}
