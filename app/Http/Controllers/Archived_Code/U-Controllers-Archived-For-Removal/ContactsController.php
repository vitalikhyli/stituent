<?php
/* EDITING OUT UNNECESSSARY CONTROLLER

namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Auth;
use App\User;
use App\Person;
use App\Contact;
use App\WorkCase;
use App\Entity;
use App\Voter;

use Validator;

use DB;


class ContactsController extends Controller
{

    public function delete(Request $request, $model_id, $contact_id)
    {
        //Validation?

        $thecontact = Contact::find($contact_id);
        $this->authorize('basic', $thecontact);

        if (strpos(request()->url(),'/constituents/') !== false)    $originated = 'person';
        if (strpos(request()->url(),'/entities/') !== false)        $originated = 'entity';
        if (strpos(request()->url(),'/cases/') !== false)           $originated = 'case';

        switch($originated) {
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

    public function update(Request $request, $model_id, $contact_id, $close = null)
    {
        //Validation?

        $thecontact = Contact::find($contact_id);
        $this->authorize('basic', $thecontact);

        if (strpos(request()->url(),'/constituents/') !== false)    $originated = 'person';
        if (strpos(request()->url(),'/entities/') !== false)        $originated = 'entity';
        if (strpos(request()->url(),'/cases/') !== false)           $originated = 'case';

        switch($originated) {
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
        (request('followup_on')) ? $fu_on = Carbon::parse(request('followup_on'))->format("Y-m-d") : $fu_on = null;

        if (request('subject')) $thecontact->subject = request('subject'); //Call Log
        if (request('type'))    $thecontact->type = request('type');         //Call Log
        if (request('private')) $thecontact->private = (request('private') == 'true') ? 1 : 0;

        $thecontact->date                 = Carbon::parse(request('date'))->format("Y-m-d");
        $thecontact->notes                = request('notes');
        $thecontact->followup             = $fu;
        $thecontact->followup_on          = $fu_on;
        $thecontact->followup_done        = $fu_done;
        $thecontact->save();


        //SYNC CONNECTED PEOPLE + ENTITIES

        $entities = [];
        $people = [];

        foreach ($request->all() as $key => $value) {
            $input = explode('_',$key);
            if(isset($input[0])) $add_this = ($input[0] == 'add') ? true : null;
            $type       = (isset($input[1])) ? $input[1] : null;
            $add_id     = (isset($input[2])) ? $input[2] : null;

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

public function lookUp($v=null)
    {

        $v = trim($v);
        $mode_all       = 1;
        $search_value   = $v;

        if ($v == null) {
            return null;
        }
        if (strlen($v) > 2) {
            $people = $this->getTeamPeopleAndVotersAndEntitiesFromSearch($v);

        }

        return view('office.contacts.list', compact(
                                                        'people',
                                                        'mode_all',
                                                        'search_value'));
    }


    public function getTeamPeopleAndVotersAndEntitiesFromSearch($v)
    {
        $people_1 = Person::select(DB::raw("1 as person"),
                                   DB::raw("0 as entity"),
                                            'id',
                                            'full_name',
                                            'full_address',
                                            'household_id',
                                            'last_name',
                                            'support')
                ->where(function($q) use ($v){
                    $q->orWhere('first_name','like','%'.$v.'%');
                    $q->orWhere('last_name','like','%'.$v.'%');
                    $q->orWhere('full_name','like','%'.$v.'%');
                })
                ->where('team_id',Auth::user()->team->id);

        $people_1 = $people_1->orderBy('last_name');

        $entities = Entity::select(DB::raw("0 as person"),
                                   DB::raw("1 as entity"),
                                            'id',
                                            'name',
                                            'full_address',
                                            'household_id',
                                            DB::raw("name as last_name"),
                                            DB::raw("null as support"))
                ->where(function($q) use ($v){
                    $q->orWhere('name','like','%'.$v.'%');
                })
                ->where('team_id',Auth::user()->team->id)
                ->orderBy('name');

        $people = Voter::select(DB::raw("0 as person"),
                                DB::raw("0 as entity"),
                                        'id',
                                        'full_name',
                                        'full_address',
                                        'household_id',
                                        'last_name',
                                        DB::raw("null as support"));

        $people = $people->whereNotIn('id',Person::select('voter_id')
                         ->where('team_id',Auth::user()->team->id));

        $people = $people->where(function($q) use ($v){
                        $q->orWhere('first_name','like','%'.$v.'%');
                        $q->orWhere('last_name','like','%'.$v.'%');
                        $q->orWhere('full_name','like','%'.$v.'%');
                    })
        ->union($people_1)
        ->union($entities)
        ->orderBy('last_name');

        $people = $people->get();

        return $people;
    }

    public function connectPerson($contact_id, $person_id)
    {
        $thecontact = Contact::find($contact_id);
        $theperson = findPersonOrImportVoter($person_id, Auth::user()->team->id);

        $this->authorize('basic', $thecontact);
        $this->authorize('basic', $theperson);

        $thecontact->people()->attach($theperson, ['team_id' => $thecontact->team_id]);

        if ($thecontact->case_id) {
            $thecase = WorkCase::find($thecontact->case_id);
            $thecase->people()->attach($theperson, ['team_id' => $thecase->team_id]);
        }

        //Remove from suggested people
        $currently_suggested = json_decode($thecontact->suggested_people,false);
        $suggested_people = array();
        foreach($currently_suggested as $suggestion) {
            if (
                ($suggestion->id != $theperson->id) &&
                ($suggestion->id != $theperson->voter_id)
                ) {
                $suggested_people[] = array("id" => $suggestion->id);
            }
        }
        if($suggested_people) {
            $thecontact->suggested_people = json_encode($suggested_people);
        } else {
            $thecontact->suggested_people = null;
        }
        $thecontact->save();

        return view('shared-features.call-log.content');
    }



    public function connectEntity($contact_id, $entity_id)
    {
        $thecontact = Contact::find($contact_id);
        $entity = Entity::find($entity_id);

        $this->authorize('basic', $thecontact);
        $this->authorize('basic', $entity);

        $thecontact->entities()->attach($entity, ['team_id' => $thecontact->team_id]);

        // if ($thecontact->case_id) {
        //     $thecase = WorkCase::find($thecontact->case_id);
        //     $thecase->entities()->attach($entity, ['team_id' => $thecase->team_id]);
        // }

        //Remove from suggested people
        $currently_suggested = json_decode($thecontact->suggested_entities,false);
        $suggested_entities = array();
        foreach($currently_suggested as $suggestion) {
            if (
                ($suggestion->id != $entity->id) &&
                ($suggestion->id != $entity->voter_id)
                ) {
                $suggested_entities[] = array("id" => $suggestion->id);
            }
        }
        if($suggested_entities) {
            $thecontact->suggested_entities = json_encode($suggested_entities);
        } else {
            $thecontact->suggested_entities = null;
        }
        $thecontact->save();

        return view('shared-features.call-log.content');
    }



    public function edit(Request $request, $id)
    {
        $thecontact = Contact::find($id);

        $this->authorize('basic', $thecontact);

        return view('elements.edit-contact-modal', compact('thecontact'));
    }

    public function index()
    {
        //Remembers last sub-tab user was at from menu

        if (Auth::user()->getMemory('show_contacts_type') == 'mine') {

            return redirect('u/contacts/mine');

        } elseif (Auth::user()->getMemory('show_contacts_type') == 'recent') {

            return redirect('u/contacts/recent');

        } else {

            return redirect('u/contacts');

        }
    }
    public function indexTeam()
    {
        Auth::user()->addMemory('show_contacts_type', 'team');
        $contacts = Auth::user()->team->contacts()
                        ->orderBy('date', 'desc')
                        ->get();
        return view('u.contacts.index', compact('contacts'));
    }

    public function recent()
    {
        $contacts = Contact::where('team_id', Auth::user()->team->id)
                            ->where('private', 0)
                            ->orWhere(function ($q) {
                                $q->where('private',1);
                                $q->where('user_id', Auth::user()->id);
                                $q->where('team_id', Auth::user()->team->id);
                            })
                           ->orderBy('created_at', 'desc');

        // $contacts = $contacts->paginate(5);
        $contacts = $contacts->get();

        return view('u.contacts.recent', compact('contacts'));
    }

    public function myContacts()
    {
        Auth::user()->addMemory('show_contacts_type', 'mine');
        $contacts = Auth::user()->contacts()
                        ->orderBy('date', 'desc')
                        ->get();
        return view('u.contacts.mine', compact('contacts'));
    }

    public function addContactToPerson(Request $request) {

        // VALIDATE
        $validator = Validator::make($request->all(), [
                'date' => ['date'],
                //'time' => ['date_format:h:i A'],
                // 'subject' => ['required', 'max:255'],
                //'notes' => ['required', 'max:255'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $theperson = findPersonOrImportVoter(request('person_id'), Auth::user()->team->id);

        $this->authorize('basic', $theperson);

        $contact = new Contact;
        $contact->team_id = $theperson->team_id;
        $contact->user_id = Auth::user()->id;

        $date = request('date');
        if (request('use_time') == 1) {
            $time = Carbon::parse(request('time'))->format("H:i:s");
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
        $contact->people()->attach($theperson, ['team_id' => $contact->team_id]);
        return redirect('u/constituents/'.$theperson->id);
    }


    public function addContactToEntity(Request $request, $entity_id) {

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
            $time = Carbon::parse(request('time'))->format("H:i:s");
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


    public function convertToCase($contact_id, $person_id) {

        $contact = Contact::find($contact_id);

        $this->authorize('basic', $contact);

        if(!$contact->private) {

            $notes      = $contact->notes;
            $date       = $contact->date;

            return view('u.cases.new', compact('notes','date','contact','person_id'));

        } else {

            return back();

        }
    }

    public function convertToCase_store(Request $request, $contact_id) {

        $contact = Contact::find($contact_id);

        $this->authorize('basic', $contact);

        $case = new WorkCase;
        $case->subject      = request('subject');
        $case->date         = $contact->date;
        $case->team_id      = $contact->team_id;
        $case->user_id      = $contact->user_id;
        $case->save();

        $contact->case_id = $case->id;
        $contact->save();

        foreach ($contact->people as $theperson) {
          $case->people()->attach($theperson, ['team_id' => $contact->team_id]);
        }

        return redirect('u/cases_edit/'.$case->id);
    }



}
