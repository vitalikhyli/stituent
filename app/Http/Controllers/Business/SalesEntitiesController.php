<?php

namespace App\Http\Controllers\Business;

use App\Account;
use App\Contact;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Models\Business\SalesContact;
use App\Models\Business\SalesEntity;
use App\Models\Business\SalesPattern;
use App\Models\Business\SalesTeam;
use App\Person;
use App\Team;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class SalesEntitiesController extends Controller
{
    public function importAccount($type, $id = null)
    {
        $type = base64_decode($type);

        if (! $id) {
            $unlinked_accounts = Account::where('id', '<>', Auth::user()->team->account->id)
                                       ->whereNotIn('id',
                                            SalesEntity::where('team_id', Auth::user()->team->id)
                                                       ->whereNotNull('cf_id')
                                                       ->pluck('cf_id')
                                                       ->toArray()
                                       )
                                       ->get();
        } else {
            $unlinked_accounts = Account::where('id', $id)->get();
        }

        foreach ($unlinked_accounts as $account) {

            // Don't import if in any team:
            if (SalesEntity::where('cf_id', $account->id)->exists()) {
                continue;
            }

            // Create Entity from Account

            $entity = new Entity;
            $entity->team_id = Auth::user()->team->id;
            $entity->user_id = Auth::user()->id;
            $entity->name = $account->name;
            $entity->save();

            // Create SalesEntity from Account

            $sales_entity = new SalesEntity;
            $sales_entity->entity_id = $entity->id;
            $sales_entity->team_id = Auth::user()->team->id;
            $sales_entity->user_id = Auth::user()->id;
            $sales_entity->type = $type;
            $sales_entity->client = true;
            $sales_entity->cf_id = $account->id;
            $sales_entity->save();

            $default_pattern = SalesPattern::where('team_id', Auth::user()->team->id)
                                           ->where('default_type', $sales_entity->type)
                                           ->first();

            if ($default_pattern) {
                $sales_entity->pattern_id = $default_pattern->id;
                $sales_entity->save();
            }

            // Create People from Users

            $people_to_link = [];

            foreach ($account->users() as $user) {
                $person = new Person;
                $name = explode(' ', $user->name);
                $person->first_name = (isset($name[0])) ? $name[0] : null;
                $person->last_name = (isset($name[1])) ? $name[1] : null;
                $person->team_id = Auth::user()->team->id;
                $person->primary_email = $user->email;
                $person->save();

                $people_to_link[$person->id] = ['team_id'       => Auth::user()->team->id,
                                                'relationship'  => 'User', ];
            }

            $entity->people()->attach($people_to_link);
        }

        return redirect()->back();
    }

    public function indexClients()
    {
        $sales_entities = SalesEntity::where(function ($q) {
            $q->orWhere('user_id', Auth::user()->id);
            $q->orWhereIn('type',
                                                SalesTeam::where('team_id', Auth::user()->team->id)
                                                         ->pluck('type')
                                                         ->unique()
                                                         ->toArray()
                                            );
        })
                                       ->where('team_id', Auth::user()->team->id)
                                       ->where('client', true);

        $prospect_types = $sales_entities->get()->pluck('type')->unique()->toArray();

        $prospects = $sales_entities->get();

        $prospects = $prospects->sortBy(function ($item) {
            return $item->entity->name;
        });

        $unlinked_accounts = Account::where('id', '<>', Auth::user()->team->account->id)
                                   ->whereNotIn('id',
                                                $sales_entities->whereNotNull('cf_id')->pluck('cf_id')->toArray())
                                   ->get();

        return view(Auth::user()->team->app_type.'.clients.index', compact(
                                                    'prospect_types',
                                                    'prospects',
                                                    'unlinked_accounts'
                                                  ));
    }

    public function index()
    {
        $sales_entities = SalesEntity::where(function ($q) {
            $q->orWhere('user_id', Auth::user()->id);
            $q->orWhereIn('type',
                                                SalesTeam::where('team_id', Auth::user()->team->id)
                                                         ->pluck('type')
                                                         ->unique()
                                                         ->toArray()
                                            );
        })
                                       ->where('client', false)
                                       ->where('team_id', Auth::user()->team->id);

        if (isset($_GET['type'])) {
            $sales_entities = $sales_entities->where('type', $_GET['type']);
        }

        $prospect_types = $sales_entities->get()->pluck('type')->unique()->toArray();

        $prospects = $sales_entities->get();

        $prospects = $prospects->sortBy(function ($item) {
            return $item->entity->name;
        });

        return view(Auth::user()->team->app_type.'.prospects.index', compact(
                                                    'prospect_types',
                                                    'prospects'
                                                  ));
    }

    public function save(Request $request)
    {
        if (! request('name')) {
            return back();
        }

        $entity = new Entity;
        $entity->team_id = Auth::user()->team->id;
        $entity->user_id = Auth::user()->id;
        $entity->name = request('name');
        $entity->save();

        $sales_entity = new SalesEntity;
        $sales_entity->entity_id = $entity->id;
        $sales_entity->team_id = Auth::user()->team->id;
        $sales_entity->user_id = Auth::user()->id;
        $sales_entity->type = (request('type')) ? request('type') : null;
        $sales_entity->save();

        $default_pattern = SalesPattern::where('team_id', Auth::user()->team->id)
                                       ->where('default_type', $sales_entity->type)
                                       ->first();

        if ($default_pattern) {
            $sales_entity->pattern_id = $default_pattern->id;
            $sales_entity->save();
        }

        // return redirect()->back();
        return redirect('/'.Auth::user()->team->app_type.'/prospects/'.$sales_entity->id.'/edit');
    }

    public function edit($id)
    {
        $opportunity = SalesEntity::find($id);

        $prospect_types = SalesEntity::where('team_id', Auth::user()->team->id)
                                     ->get()->pluck('type')->unique()->toArray();

        $patterns = SalesPattern::where('team_id', Auth::user()->team->id)->get();

        return view(Auth::user()->team->app_type.'.prospects.edit', compact(
                                                    'opportunity',
                                                    'prospect_types',
                                                    'patterns'
                                                  ));
    }

    public function update(Request $request, $id, $close = null)
    {
        $sales_entity = SalesEntity::find($id);
        // $sales_entity->team_id      = Auth::user()->team->id;
        // $sales_entity->user_id      = Auth::user()->id;
        if (request('new_type')) {
            $sales_entity->type = request('new_type');
        } else {
            $sales_entity->type = request('type');
        }

        $sales_entity->pattern_id = request('pattern_id');
        if (! request('next_check_in')) {
            $sales_entity->next_check_in = null;
        } else {
            $sales_entity->next_check_in = Carbon::parse(request('next_check_in'))->toDateTimeString();
        }

        $sales_entity->days_check_in = request('days_check_in');
        $sales_entity->client = (request('client')) ? true : false;
        $sales_entity->save();

        $entity = $sales_entity->entity;
        $entity->name = request('name');
        $entity->department = request('department');
        $entity->url = request('url');
        $entity->address_number = request('address_number');
        $entity->address_street = request('address_street');
        $entity->address_apt = request('address_apt');
        $entity->address_city = request('address_city');
        $entity->address_state = request('address_state');
        $entity->address_zip = request('address_zip');

        $entity->save();

        if (! $close) {
            return back();
        }
        if ($close) {
            return redirect('/'.Auth::user()->team->app_type.'/prospects/'.$sales_entity->id);
        }
    }

    public function show($id)
    {
        $opportunity = SalesEntity::find($id);

        $prospect_types = SalesEntity::where('team_id', Auth::user()->team->id)
                                     ->get()->pluck('type')->unique()->toArray();

        return view(Auth::user()->team->app_type.'.prospects.show', compact(
                                                    'opportunity',
                                                    'prospect_types'
                                                  ));
    }

    public function addPerson(Request $request, $sales_entity_id)
    {
        $sales_entity = SalesEntity::find($sales_entity_id);

        $entity = $sales_entity->entity;

        $people_to_link = [];

        $person = new Person;
        $name = explode(' ', request('person_new_name'));
        $person->first_name = (isset($name[0])) ? $name[0] : null;
        $person->last_name = (isset($name[1])) ? $name[1] : null;
        $person->team_id = Auth::user()->team->id;
        $person->primary_email = request('person_new_email');
        $person->primary_phone = request('person_new_phone');
        $person->save();

        $people_to_link[$person->id] = ['team_id'       => Auth::user()->team->id,
                                        'relationship'  => request('person_new_title'), ];

        $entity->people()->attach($people_to_link);

        return redirect()->back();
    }

    public function addContact(Request $request, $sales_entity_id)
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

        $sales_entity = SalesEntity::find($sales_entity_id);

        $theentity = $sales_entity->entity;

        // $this->authorize('basic', $theentity);

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

        $sales_contact = new SalesContact;
        $sales_contact->team_id = Auth::user()->team->id;
        $sales_contact->user_id = Auth::user()->id;
        $sales_contact->contact_id = $contact->id;
        $sales_contact->step = request('step');
        $sales_contact->check_in = (request('check_in')) ? true : false;
        $sales_contact->amount_secured = request('amount_secured');
        $sales_contact->save();

        // Set next check-in
        if ($sales_contact->check_in) {
            if ($sales_entity->days_check_in > 0) {
                $sales_entity->next_check_in = Carbon::parse($contact->date)
                                                     ->addDays($sales_entity->days_check_in)
                                                     ->toDateString();
            } else {
                $sales_entity->next_check_in = null;
            }
            $sales_entity->save();
        }

        return back();
    }
}
