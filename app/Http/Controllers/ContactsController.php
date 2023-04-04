<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Person;
use App\Traits\ConstituentQueryTrait;
use App\User;
use App\Voter;
use App\WorkCase;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class ContactsController extends Controller
{
    use ConstituentQueryTrait;

    public function deleteIndependently(Request $request, $app_type, $contact_id)
    {
        //Validation?

        $thecontact = Contact::find($contact_id);
        $this->authorize('basic', $thecontact);

        $thecontact->delete();

        return redirect('/'.Auth::user()->team->app_type.'/contacts');
    }

    public function delete(Request $request, $app_type, $model_id, $contact_id)
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
                $return_string = Auth::user()->team->app_type.'/constituents/'.$theperson->id;
                break;

            case 'entity':
                $entity = Entity::find($model_id);
                $this->authorize('basic', $entity);
                $return_string = Auth::user()->team->app_type.'/entities/'.$entity->id;
                break;

            case 'case':
                $thecase = WorkCase::find($model_id);
                $this->authorize('basic', $thecase);
                $return_string = Auth::user()->team->app_type.'/cases/'.$thecase->id;
                break;
        }

        $thecontact->delete();

        return redirect($return_string);
    }

    public function updateIndependently(Request $request, $app_type, $contact_id, $close = null)
    {

        //Validation?

        $thecontact = Contact::find($contact_id);
        $this->authorize('basic', $thecontact);

        $this->updateContactModel($request, $thecontact, $originated = 'index');

        if ($close) {
            return redirect('/'.Auth::user()->team->app_type.'/contacts');
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/contacts/'.$thecontact->id.'/edit');
        }
    }

    public function update(Request $request, $app_type, $model_id, $contact_id, $close = null)
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
                $return_string = Auth::user()->team->app_type.'/constituents/'.$theperson->id;
                break;

            case 'entity':
                $entity = Entity::find($model_id);
                $this->authorize('basic', $entity);
                $return_string = Auth::user()->team->app_type.'/entities/'.$entity->id;
                break;

            case 'case':
                $thecase = WorkCase::find($model_id);
                $this->authorize('basic', $thecase);
                $return_string = Auth::user()->team->app_type.'/cases/'.$thecase->id;
                break;
        }

        $this->updateContactModel($request, $thecontact, $originated);

        if ($close) {
            return redirect($return_string);
        } else {
            return redirect($return_string.'/contacts/'.$thecontact->id.'/edit');
        }
    }

    public function updateContactModel(Request $request, $thecontact, $originated = null)
    {
        (request('followup')) ? $fu = 1 : $fu = 0;
        (request('followup_done')) ? $fu_done = 1 : $fu_done = 0;
        (request('followup_on')) ? $fu_on = Carbon::parse(request('followup_on'))->format('Y-m-d') : $fu_on = null;

        //dd(request()->input());

        $thecontact->subject    = request('subject'); //Call Log
        $thecontact->type       = request('type');         //Call Log
        $thecontact->private    = (request('private') == 'true') ? true : false;
        $thecontact->user_id    = request('user_id');

        // if (request('user_id')) {
        //     $thecontact->user_id = request('user_id');

        //     // If changing user to someone else, do not allow contact to be private
        //     // Because then the user doing this will not be able to access the contact
        //     if (! Auth::user()->permissions->admin && $thecontact->user_id != Auth::user()->id) {
        //         $thecontact->private = false;
        //     }
        // }

        $date = request('date');
        if (request('time')) {
            $datetime = Carbon::parse(request('date').' '.request('time'))->format('Y-m-d H:i:s');
            $thecontact->date = $datetime;
        } else {
            $thecontact->date = Carbon::parse(request('date'))->format('Y-m-d');
        }

        $thecontact->notes = request('notes');
        if (request('type-other')) {
            $thecontact->type = request('type-other');
        } else {
            $thecontact->type = request('type');
        }
        $thecontact->followup = $fu;
        $thecontact->followup_on = $fu_on;
        $thecontact->followup_done = $fu_done;
        if (!$thecontact->user_id) {
            $thecontact->user_id = Auth::user()->id;
        }
        $thecontact->save();

        //SYNC CONNECTED PEOPLE + ENTITIES
        // $entities = [];
        // $people = [];

        // foreach ($request->all() as $key => $value) {
        //     $input = explode('-', $key);
        //     if (isset($input[0])) {
        //         $add_this = ($input[0] == 'add') ? true : null;
        //     }
        //     $type = (isset($input[1])) ? $input[1] : null;
        //     $add_id = (isset($input[2])) ? $input[2] : null;

        //     if ($add_this) {
        //         if ($type == 'person') {
        //             $add_person = findPersonOrImportVoter($add_id, Auth::user()->team->id);
        //             $add_id = $add_person->id;
        //             $people[] = ['person_id' => $add_id, 'team_id' => Auth::user()->team->id];
        //         }
        //         if ($type == 'entity') {
        //             $add_entity = Entity::find($entity_id);
        //             $add_id = $add_entity->id;
        //             $entities[] = ['entity_id' =>$add_id, 'team_id' => Auth::user()->team->id];
        //         }
        //     }
        // }

        // $thecontact->people()->sync($people, ['team_id' => Auth::user()->team->id]);
        // $thecontact->entities()->sync($entities, ['team_id' => Auth::user()->team->id]);
    }

    public function lookUp($app_type, $v = null)
    {
        $v = trim($v);
        $mode_all = 1;
        $search_value = $v;

        if ($v == null || strlen($v) <= 1) {
            return null;
        } elseif (strlen($v) > 1) {
            $people = $this->getPeopleFromName($v);
        }

        return view('shared-features.contacts.list', compact(
                                                    'people',
                                                    'mode_all',
                                                    'search_value'));
    }

    public function getTeamPeopleAndVotersAndEntitiesFromSearch($v)
    {
        $people_1 = Person::select(DB::raw('1 as person'),
                                   DB::raw('0 as entity'),
                                            'id',
                                            'full_name',
                                            'full_address',
                                            'household_id',
                                            'last_name',
                                            'support')
                ->where(function ($q) use ($v) {
                    $q->orWhere('first_name', 'like', '%'.$v.'%');
                    $q->orWhere('last_name', 'like', '%'.$v.'%');
                    $q->orWhere('full_name', 'like', '%'.$v.'%');
                })
                ->where('team_id', Auth::user()->team->id);

        $people_1 = $people_1->orderBy('last_name');

        $entities = Entity::select(DB::raw('0 as person'),
                                   DB::raw('1 as entity'),
                                            'id',
                                            'name',
                                            'full_address',
                                            'household_id',
                                            DB::raw('name as last_name'),
                                            DB::raw('null as support'))
                ->where(function ($q) use ($v) {
                    $q->orWhere('name', 'like', '%'.$v.'%');
                })
                ->where('team_id', Auth::user()->team->id)
                ->orderBy('name');

        $people = Voter::select(DB::raw('0 as person'),
                                DB::raw('0 as entity'),
                                        'id',
                                        'full_name',
                                        'full_address',
                                        'household_id',
                                        'last_name',
                                        DB::raw('null as support'));

        $people = $people->whereNotIn('id', Person::select('voter_id')
                         ->where('team_id', Auth::user()->team->id));

        $people = $people->where(function ($q) use ($v) {
            $q->orWhere('first_name', 'like', '%'.$v.'%');
            $q->orWhere('last_name', 'like', '%'.$v.'%');
            $q->orWhere('full_name', 'like', '%'.$v.'%');
        })
        ->union($people_1)
        ->union($entities)
        ->orderBy('last_name');

        $people = $people->get();

        return $people;
    }

    public function connectPerson($app_type, $contact_id, $person_id)
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
        $currently_suggested = json_decode($thecontact->suggested_people, false);
        $suggested_people = [];
        foreach ($currently_suggested as $suggestion) {
            if (
                ($suggestion->id != $theperson->id) &&
                ($suggestion->id != $theperson->voter_id)
                ) {
                $suggested_people[] = ['id' => $suggestion->id];
            }
        }
        if ($suggested_people) {
            $thecontact->suggested_people = json_encode($suggested_people);
        } else {
            $thecontact->suggested_people = null;
        }
        $thecontact->save();

        return view('shared-features.call-log.content');
    }

    public function editIndependently($app_type, $contact_id)
    {
        $thecontact = Contact::find($contact_id);
        $this->authorize('basic', $thecontact);
        $form_action = '/contacts/'.$thecontact->id;

        return view('shared-features.contacts.edit', compact('thecontact', 'form_action'));
    }

    public function edit(Request $request, $app_type, $person_id, $contact_id)
    {
        $theperson = Person::find($person_id);
        $thecontact = Contact::find($contact_id);

        $this->authorize('basic', $theperson);
        $this->authorize('basic', $thecontact);

        $form_action = '/constituents/'.$theperson->id.'/contacts/'.$thecontact->id;

        return view('shared-features.contacts.edit', compact('thecontact', 'form_action'));
    }

    public function index($app_type)
    {
        //Remembers last sub-tab user was at from menu

        if (Auth::user()->getMemory('show_contacts_type') == 'mine') {
            return redirect(Auth::user()->team->app_type.'/contacts/mine');
        }

        Auth::user()->addMemory('show_contacts_type', 'team');

        $contacts = Contact::TeamOrPrivateAndMine()
                            ->where('team_id', Auth::user()->team->id)
                            // ->where('private', 0)
                            // ->orWhere(function ($q) {
                            //     $q->where('private',1);
                            //     $q->where('user_id', Auth::user()->id);
                            //     $q->where('team_id', Auth::user()->team->id);
                            // })

                           ->orderBy('created_at', 'desc');

        // $contacts = Auth::user()->contacts()
        //      ->orderBy('created_at', 'desc');

        if (request('keyword')) {
            $contacts->where(function($query) {
                    return $query->where('notes', 'LIKE', '%'.request('keyword').'%')
                                 ->orWhere('subject', 'LIKE', '%'.request('keyword').'%');
                             });
            $contacts = $contacts->paginate(1000);
        } else {
            $contacts = $contacts->paginate(50);
        }
        

        return view('shared-features.contacts.index', compact('contacts'));
    }

    public function myContacts($app_type)
    {
        Auth::user()->addMemory('show_contacts_type', 'mine');
        $contacts = Auth::user()->contacts()
                        ->orderBy('date', 'desc')
                        ->get();

        return view('shared-features.contacts.mine', compact('contacts'));
    }

    public function addContactToPerson(Request $request, $app_type)
    {

        // VALIDATE
        $validator = Validator::make($request->all(), [
                // 'date' => ['date'],
                // 'time' => ['date_format:h:i A'],
                // 'subject' => ['required', 'max:255'],
                // 'notes' => ['required', 'max:255'],
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

        if (request('use_time') == 1) {
            $datetime = Carbon::parse(request('date').' '.request('time'))->format('Y-m-d H:i:s');
            $contact->date = $datetime;
        } else {
            $contact->date = Carbon::parse(request('date').' '.Carbon::now()
                                           ->format('H:i:s'))->format('Y-m-d H:i:s');
        }

        if (request('person_followup')) {
            $contact->followup = 1;
            $contact->followup_on = Carbon::parse(request('person_followup_on'))->format('Y-m-d');
        }

        $contact->subject = request('subject');
        $contact->notes = request('notes');
        $contact->type = request('type');
        $contact->save();
        $contact->people()->attach($theperson, ['team_id' => $contact->team_id]);

        return redirect(Auth::user()->team->app_type.'/constituents/'.$theperson->id);
    }

    public function linkToCase(Request $request, $app_type, $contact_id)
    {
        if (request('case_id') === null) {
            return redirect()->back();
        }

        $contact = Contact::find($contact_id);
        $case = WorkCase::find(request('case_id'));

        $this->authorize('basic', $contact);
        $this->authorize('basic', $case);

        // Do whatever needed to unlink from old Case
        if ($contact->case_id) {
            // Nothing for now ... users can remove linked people manually
        }

        // Link to new Case
        $contact->case_id = $case->id;
        $contact->save();

        // Make people linked to the Contact now linked to the Case
        $sync_contacts = $contact->people->pluck('id')->toArray();
        $sync_case = $case->people->pluck('id')->toArray();
        $people = array_merge($sync_contacts, $sync_case);

        $sync = [];
        foreach ($people as $person_id) {
            $sync[$person_id] = ['team_id' => Auth::user()->team->id];
        }
        $case->people()->sync($sync);

        return redirect('/'.Auth::user()->team->app_type.'/cases/'.$case->id);
    }

    public function convertToCase($app_type, $contact_id, $person_id = null)
    {
        $contact = Contact::find($contact_id);

        $this->authorize('basic', $contact);

        // if (! $contact->private) {
            $notes = $contact->notes;
            $date = $contact->date;
            $case_options = Auth::user()
                                ->team
                                ->cases()
                                ->orderByDesc('date')
                                ->take(100)
                                ->get();

            // If there is a case selected already put it at the top of the radio button list
            if ($contact->case_id) {
                $selected_case_on_top = WorkCase::find($contact->case_id);
                $case_options = $case_options->filter(function ($item) use ($contact) {
                    return $item->id != $contact->case_id;
                });
                $case_options->prepend($selected_case_on_top);
            }

            return view('shared-features.cases.new', compact('notes', 'date', 'contact', 'person_id', 'case_options'));
        // } else {
        //     return back();
        // }
    }
}
