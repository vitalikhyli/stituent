<?php

namespace App\Http\Controllers;

use App\BulkEmailQueue;
use App\CasePerson;
use App\Category;
use App\Contact;
use App\ContactPerson;
use App\District;
use App\Entity;
use App\Group;
use App\GroupPerson;
use App\Household;
use App\Http\Controllers\Controller;
use App\Municipality;
use App\Person;
use App\PersonFile;
use App\Traits\ConstituentQueryTrait;
use App\Traits\ExportTrait;
use App\Traits\SharedCasesTrait;   
use App\Voter;
use App\WorkCase;
use App\SharedCase;
use Auth;
use Carbon\Carbon;
use DB;
use Schema;
use Illuminate\Http\Request;
use Validator;

class PeopleController extends Controller
{
    use ConstituentQueryTrait;
    use ExportTrait;
    use SharedCasesTrait;

    public function masterEmailConfirm()
    {
        $input = request()->input();

        //$input['limit'] = 1000;
        $people = Person::where('team_id', Auth::user()->team->id)
                        ->whereNotNull('primary_email')
                        ->get();

        //dd($people);
        $people = $people->sortByDesc('email');

        $master_email_existing = Person::where('team_id', Auth::user()->team->id)
                                       ->where('master_email_list', true)
                                       ->get();

        $master_email_existing = $master_email_existing->diff($people);

        $people_already_on = $people->filter(function ($item) {
            return $item['master_email_list'];
        });

        $people_to_add = $people->filter(function ($item) {
            return $item['email'] && ! $item['master_email_list'];
        });

        return view('shared-features.constituents.master-email', compact('people_to_add', 'people_already_on', 'master_email_existing'));
    }

    public function masterEmailUpdate(Request $request)
    {
        //if (!Auth::user()->permissions->developer) return redirect()->back();

        $input = request()->input();
        foreach ($input as $key => $value) {
            if (substr($key, 0, 6) == 'person') {
                $id = substr($key, 6 + 1);

                $person = findPersonOrImportVoter($id, Auth::user()->team->id);
                // $person = Person::find($id);
                $this->authorize('basic', $person);

                $person->master_email_list = ($value == 'remove') ? false : true;
                $person->save();
            }
        }

        return redirect()->back();
    }

    public function delete($app_type, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        $this->authorize('basic', $person);

        CasePerson::where('person_id', $person->id)->delete();
        ContactPerson::where('person_id', $person->id)->delete();
        PersonFile::where('person_id', $person->id)->delete();
        GroupPerson::where('person_id', $person->id)->delete();

        $person->delete();

        return redirect('/'.$app_type.'/constituents');
    }

    public function districtEdit($app_type, $id, $district_type)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        if ($district_type == 'house') {
            $district_type_short = 'H';
        }
        if ($district_type == 'senate') {
            $district_type_short = 'S';
        }
        if ($district_type == 'congress') {
            $district_type_short = 'F';
        }

        $district_field = $district_type.'_district';

        $districts = District::where('type', $district_type_short)
                             ->where('state', session('team_state'))
                             ->get();

        return view('shared-features.constituents.edit-district', compact('person', 'district_type', 'districts', 'district_field'));
    }

    public function districtUpdate(Request $request, $app_type, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);
        $district_field = request('district_type').'_district';
        $person->$district_field = request('district_code');
        $person->save();

        if (request('save_and_close')) {
            return redirect('/'.$app_type.'/constituents/'.$person->id);
        } else {
            return redirect()->back();
        }
    }

    public function districtRevert($app_type, $id, $district_type)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);
        $voter = Voter::find($person->voter_id);
        $district_voter_field = $district_type.'_district';
        $district_value = $voter->$district_voter_field;
        $district_person_field = $district_type.'_district';
        $person->$district_person_field = $district_value;
        $person->save();

        return redirect()->back();
    }

    public function getArrayFieldsAsObject($model, $column, $fields)
    {
        $is_empty = true;
        $object = new class {
        };
        try {
            foreach ($fields as $thefield) {
                $object->$thefield = (isset($model->$column[$thefield])) ? $model->$column[$thefield] : null;
                if ($model->$column[$thefield] != null && $model->$column[$thefield] != '') {
                    $is_empty = false;
                }
            }
        } catch (\Exception $e) {
        }
        $object->empty = $is_empty;

        return $object;
    }

    public function otherEmailPhoneObject($array)
    {

        // The other_emails and other_phones columns sometimes have strange data in them.
        // Also, handling empty or non-existant arrays is cumbersome in Laravel views.
        // This function standardizes the data and outputs as a more usable object, not array.

        $contacts = collect([]);

        if ($array && is_array($array)) {
            foreach ($array as $item) {
                $object = new class {
                };

                if (isset($item[0])) {
                    if (is_array($item[0])) {
                        $object->contact = (isset($item[0][0])) ? $item[0][0] : null;
                        $object->notes = (isset($item[0][1])) ? $item[0][1] : null;
                    } else {
                        $object->contact = (isset($item[0])) ? $item[0] : null;
                    }
                }

                if (isset($item[1])) {
                    if (is_array($item[1])) {
                        $object->contact = (isset($item[1][0])) ? $item[1][0] : null;
                        $object->notes = (isset($item[1][1])) ? $item[1][1] : null;
                    } else {
                        $object->notes = (isset($item[1])) ? $item[1] : null;
                    }
                }

                $object->contact = (isset($object->contact)) ? $object->contact : null;
                $object->notes = (isset($object->notes)) ? $object->notes : null;

                if (! $object->contact && ! $object->notes) {
                    continue;
                }

                $contacts->push($object);
            }
        }

        return $contacts;
    }

    public function editBusiness($app_type, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        $this->authorize('basic', $person);

        $business = $this->getArrayFieldsAsObject($person, 'business_info', ['occupation', 'work_phone', 'work_phone_ext', 'fax', 'name', 'address_1', 'address_2', 'city', 'state', 'zip', 'zip4', 'web']);

        return view('shared-features.constituents.edit-business', compact('person', 'business'));
    }

    public function updateBusiness($app_type, Request $request, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        $this->authorize('basic', $person);

        // VALIDATION GOES HERE

        // UPDATE RECORD
        $data = $person->business_info; // THis gets other fields like "mcrc16"
        $data['occupation'] = request('business_occupation');
        $data['work_phone'] = request('business_work_phone');
        $data['work_phone_ext'] = request('business_work_phone_ext');
        $data['fax'] = request('business_fax');
        $data['name'] = request('business_name');
        $data['address_1'] = request('business_address_1');
        $data['address_2'] = request('business_address_2');
        $data['city'] = request('business_city');
        $data['state'] = request('business_state');
        $data['zip'] = request('business_zip');
        $data['zip4'] = request('business_zip4');
        $data['web'] = request('business_web');

        $person->business_info = $data;
        $person->save();

        if (request('save_and_close')) {
            return redirect('/'.Auth::user()->team->app_type.'/constituents/'.$person->id);
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/constituents/'.$person->id.'/business/edit');
        }
    }

    public function masterEmailList($app_type, $person_id, $off_or_on)
    {
        $person = findPersonOrImportVoter($person_id, Auth::user()->team->id);
        $person->master_email_list = ($off_or_on == 'add') ? true : false;
        $person->save();

        return redirect($app_type.'/constituents/'.$person->id);
    }

    public function merge($app_type, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        return view('shared-features.constituents.merge-people');
    }

    public function edit($app_type, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        $this->authorize('basic', $person);

        $other_emails = $this->otherEmailPhoneObject($person->other_emails);
        $other_phones = $this->otherEmailPhoneObject($person->other_phones);

        $mailing = $this->getArrayFieldsAsObject($person, 'mailing_info', ['address', 'address2', 'city', 'state', 'zip', 'zip4']);

        return view('shared-features.constituents.edit', compact('person', 'other_emails', 'other_phones', 'mailing'));
    }

    public function birthdays()
    {
        $birthdays = Auth::user()->team->people()
                                     ->whereRaw(
                                          'DATE_FORMAT(curdate() - INTERVAL 3 DAY, "%m-%d") <= DATE_FORMAT(dob, "%m-%d")
                                          AND DATE_FORMAT(CURRENT_TIMESTAMP + INTERVAL 4 DAY, "%m-%d") >= DATE_FORMAT(dob, "%m-%d")')
                                     ->orderByRaw('DATE_FORMAT(dob, "%m-%d"), dob DESC')
                                     ->get();

        return view('shared-features.constituents.birthdays', compact('birthdays'));
    }

    public function save(Request $request)
    {
        $person = new Person;
        $person->first_name = request('first_name');
        $person->last_name = request('last_name');
        if (request('email')) {
            $person->primary_email = request('email');
        }
        if (request('phone')) {
            $person->primary_phone = request('phone');
        }

        $person->team_id = Auth::user()->team->id;
        $person->created_by = Auth::user()->id;
        $person->save();

        if (request('contact_id')) {
            $cp = new ContactPerson;
            $cp->person_id = $person->id;
            $cp->team_id = Auth::user()->team->id;
            $cp->contact_id = request('contact_id');
            $cp->save();
        }

        $person->full_name = $person->generateFullName();

        //Add Initial Groups
        $sync = [];
        foreach ($request->all() as $field => $value) {
            if (substr($field, 0, 6) == 'group_') {
                $group_id = substr($field, 6);
                $sync[$group_id]['team_id'] = Auth::user()->team->id;
            }

            if (substr($field, 0, 9) == 'position_') {
                $position = request($field);
                if ($position) {
                    $group_id = substr($field, 9);
                    $sync[$group_id]['position'] = $position;
                }
            }

            if (substr($field, 0, 6) == 'title_') {
                $title = request($field);
                if ($title) {
                    $group_id = substr($field, 6);
                    $sync[$group_id]['title'] = $title;
                }
            }

            if (substr($field, 0, 6) == 'notes_') {
                $notes = request($field);
                if ($notes) {
                    $group_id = substr($field, 6);
                    $sync[$group_id]['notes'] = $notes;
                }
            }
        }

        foreach ($sync as $group_id => $data) {
            $group = Group::find($group_id);
            $this->authorize('basic', $group);
            $person->groups()->attach($group_id, $data);
        }

        return redirect('shared-features/constituents/'.$person->id.'/edit');
    }

    public function syncVoterAddress($app_type, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);
        $this->authorize('basic', $person);

        $voter = $person->voter;

        if ($person && $voter) {

            $person->address_street     = $voter->address_street;
            $person->address_number     = $voter->address_number;
            $person->address_fraction   = $voter->address_fraction;
            $person->address_apt        = $voter->address_apt;
            $person->address_city       = $voter->address_city;
            $person->address_state      = $voter->address_state;
            $person->address_zip        = $voter->address_zip;

            $person->save();

        }

        return redirect()->back();
    }

    public function update($app_type, Request $request, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        $this->authorize('basic', $person);

        $old_hh_id = $person->household_id;

        //VALIDATION START
        $validate_array = $request->all();

        $emails_array = [];
        foreach ($request->all() as $key => $val) {
            if (substr($key, 0, 6) == 'email_') {
                $email_id = substr($key, 6);
                if (! request('email_'.$email_id) && ! request('email-notes_'.$email_id)) {
                    continue;
                }
                $emails_array[] = [request('email_'.$email_id),
                                   request('email-notes_'.$email_id), ];
            }
            if (substr($key, 0, 6) == 'email_new') {
                if (! request('email_new') && ! request('email-notes_new')) {
                    continue;
                }
                $emails_array[] = [request('email_new'),
                                   request('email-notes_new'), ];
            }
        }

        $phones_array = [];
        foreach ($request->all() as $key => $val) {
            if (substr($key, 0, 6) == 'phone_') {
                $phone_id = substr($key, 6);
                if (! request('phone_'.$phone_id) && ! request('phone-notes_'.$phone_id)) {
                    continue;
                }
                $phones_array[] = [request('phone_'.$phone_id),
                                   request('phone-notes_'.$phone_id), ];
            }
            if (substr($key, 0, 6) == 'phone_new') {
                if (! request('phone_new') && ! request('phone-notes_new')) {
                    continue;
                }
                $phones_array[] = [request('phone_new'),
                                   request('phone-notes_new'), ];
            }
        }

        // VALIDATE
        $validator = Validator::make($validate_array, [
                'first_name' => ['required', 'max:255'],
                'last_name' => ['required', 'max:255'],
                'emails.*' => ['email'],
                'primary_email' => ['nullable', 'email'],
                'work_email' => ['nullable', 'email'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        // UPDATE RECORD
        $person->private =          request('private');
        $person->name_title =       request('name_title');
        $person->first_name =       request('first_name');
        $person->nickname =         request('nickname');
        $person->language =         request('language');
        $person->middle_name =      request('middle_name');
        $person->last_name =        request('last_name');
        $person->address_street =   request('address_street');
        $person->address_number =   request('address_number');
        $person->dob =              request('dob');
        $person->address_fraction = request('address_fraction');
        $person->address_apt =      request('address_apt');
        $person->address_city =     request('address_city');
        $person->address_state =    substr(request('address_state'), 0, 2);
        $person->address_zip =      substr(request('address_zip'), 0, 5);

        $person->primary_email =    request('primary_email');
        $person->work_email =       request('work_email');
        $person->other_emails = ($emails_array) ? $emails_array : null;

        $person->primary_phone = request('primary_phone');
        $person->social_media = request('social_media');
        $person->other_phones = ($phones_array) ? $phones_array : null;

        $person->gender = request('gender');
        $person->pronouns = request('pronouns');

        $person->save();

        // COMPOUND FIELDS
        $person->full_name = $person->generateFullName();
        $person->full_name_middle = $person->generateFullNameMiddle();
        $person->full_address = $person->generateFullAddress();
        $person->household_id = $person->generateHouseholdId();

        $person->mailing_info = ['address'        => request('mailing_address'),
                                       'address2'       => request('mailing_address2'),
                                       'city'           => request('mailing_city'),
                                       'state'          => request('mailing_state'),
                                       'zip'            => request('mailing_zip'),
                                       'zip4'           => request('mailing_zip4'),
                                      ];
        $person->save();

        if (request('deceased') || request('deceased_date')) {
            $person->deceased = true;
            $person->deceased_date = Carbon::parse(request('deceased_date'))->toDateString();
            $person->save();
        } else {
            $person->deceased = false;
            $person->deceased_date = null;
            $person->save();
        }

        session()->flash('msg', 'Person was Saved!');

        if (request('save_and_close')) {
            return redirect('/'.Auth::user()->team->app_type.'/constituents/'.$person->id);
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/constituents/'.$person->id.'/edit');
        }
    }

    public function setPrimaryEmail($app_type, $id, $email)
    {
        if (IDisPerson($id)) {
            
            $person = Person::find($id);
            $this->authorize('basic', $person);

            if(!$person->primary_email) {
                $person->primary_email = base64_decode($email);
                $person->save();
            }

        }

        return redirect('/'.Auth::user()->team->app_type.'/constituents/'.$id);
    }

    public function setPrimaryPhone($app_type, $id, $phone)
    {
        if (IDisPerson($id)) {
            
            $person = Person::find($id);
            $this->authorize('basic', $person);

            if(!$person->primary_phone) {
                $person->primary_phone = base64_decode($phone);
                $person->save();
            }

        }

        return redirect('/'.Auth::user()->team->app_type.'/constituents/'.$id);
    }
    public function addGroup($app_type, $person_id)
    {
        if (!is_numeric($person_id)) {
            $person = findPersonOrImportVoter($person_id, Auth::user()->team->id);
            $person_id = $person->id;
        }
        $group_id = request('group_id');
        $person_group = new GroupPerson;
        $person_group->team_id = Auth::user()->team_id;
        $person_group->group_id = $group_id;
        $person_group->person_id = $person_id;
        $person_group->save();

        return redirect()->back();
    }

    public function show($app_type, $id)
    {
        $tab = Auth::user()->getmemory('person_tabs', 'notes');

        if (IDisPerson($id)) {
            $person = Person::find($id);

            $this->authorize('basic', $person);

            if (! $person) {
                dd('Error - No person found!', $id);
            }

            $other_emails = $this->otherEmailPhoneObject($person->other_emails);
            $other_phones = $this->otherEmailPhoneObject($person->other_phones);

            $groups = $person->groups;

            $cases = $person->cases()->StaffOrPrivateAndMine()->get();

            $shared_cases = $this->getSharedCases($person->voter_id);

            $contacts = Contact::select(DB::raw('contacts.type as type'),
                                        DB::raw('contacts.date as date'),
                                        DB::raw('contacts.id as id'),
                                        DB::raw('contacts.notes as notes'),
                                        DB::raw('contacts.private as private'),
                                        'followup',
                                        'followup_on',
                                        'followup_done',
                                        'case_id',
                                        DB::raw('null as name'),
                                        DB::raw('contacts.subject as subject'),
                                        DB::raw('contacts.user_id as user_id'),
                                        DB::raw('contacts.id as id'),
                                        )
                                ->join('contact_person', 'contacts.id', 'contact_id')
                                ->leftJoin('cases', 'contacts.case_id', 'cases.id')
                                ->where('contact_person.person_id', $id);

                        if (!Auth::user()->permissions->admin) {
                            $contacts->where(function ($q) {
                                    $q->orWhere('contacts.private', false);
                                    $q->orWhere('contacts.user_id', Auth::user()->id);
                                })
                                ->where(function ($q) {
                                    $q->orWhereNull('contacts.case_id');
                                    $q->orWhere('cases.private', false);
                                    $q->orWhere('cases.user_id', Auth::user()->id);
                                });
                        }

                        $contacts = $contacts->orderBy('date', 'desc')->get();


            $detected_emails = $contacts->each(function ($item) {
                $item['text'] = $item['subject'].' '.$item['notes'];
            })->each(function ($item) {
                $string = $item['text'];
                $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
                preg_match_all($pattern, $string, $matches);
                if ($matches[0]) {
                    $item['emails'] = $matches[0];
                }
            })->pluck('emails')
            ->reject(function($email) {
                return (!$email);
            })
            ->flatten()
            ->unique();

            $detected_phones = $contacts->each(function ($item) {
                $item['text'] = $item['subject'].' '.$item['notes'];
            })->each(function ($item) {
                $string = $item['text'];
                $pattern = '/'
                          .'(\([0-9]{3}\)(\s|\.|-)?|[0-9]{3}(\s|\.|-))[0-9]{3}(\s|\.|-)[0-9]{4}'
                          .'/';
                preg_match_all($pattern, $string, $matches);
                if ($matches[0]) {
                    $item['phones'] = $matches[0];
                }
            })->pluck('phones')
            ->reject(function($phone) {
                return (!$phone);
            })
            ->flatten()
            ->unique();

            $bulk_emails = BulkEmailQueue::select(DB::raw('"bulk_email" as type'),
                                                 DB::raw('bulk_emails.completed_at as date'),
                                                 DB::raw('bulk_emails.id as id'),
                                                 DB::raw('null as notes'),
                                                 DB::raw('null as private'),
                                                 DB::raw('null as followup'),
                                                 DB::raw('null as followup_on'),
                                                 DB::raw('null as followup_done'),
                                                 'name',
                                                 'subject',
                                                 'user_id'
                                                 )
                                        ->where('person_id', $person->id)
                                        ->join('bulk_emails', 'bulk_email_id', 'bulk_emails.id')
                                        ->orderBy('date', 'desc')
                                        ->get();

            //$contacts = $contacts->union($bulk_emails)->orderBy('date', 'desc')->get();


            if (!$person->household_id) {
                $cohabitors = null;
            } else {
                if (session('team_state') == 'RI') {
                    $cohabitors = $this->getCohabitorsPersonYob($person);
                } else {
                    $cohabitors = $this->getCohabitorsPerson($person);
                }
            }

            $voterRecord = Voter::find($person->voter_id);
            if (! $voterRecord) {
                $voterRecord = null;
            }

            $groupcats = $person->team->categories()->get()->sortBy('name');


            $mode_external = false;

            $business = $this->getArrayFieldsAsObject($person, 'business_info', ['occupation', 'work_phone', 'work_phone_ext', 'fax', 'name', 'address_1', 'address_2', 'city', 'state', 'zip', 'zip4', 'web']);

            return view('shared-features.constituents.show', compact('person',
                                                                   'groupcats',
                                                                   // 'groups', //Gets from person->groups
                                                                   'cases',
                                                                   'shared_cases',
                                                                   'contacts',
                                                                   'bulk_emails',
                                                                   'cohabitors',
                                                                   'voterRecord',
                                                                   'tab',
                                                                   'bulk_emails',
                                                                   'business',
                                                                   'other_emails',
                                                                   'other_phones',
                                                                   'mode_external',
                                                                   'detected_emails',
                                                                   'detected_phones'));
        }

        if (IDisVoter($id)) {

            // IF PERSON EXISTS, REDIRECT
            $does_a_person_exist = Person::where('team_id', Auth::user()->team->id)
                                         ->where('voter_id', $id)
                                         ->first();

            if ($does_a_person_exist) {
                return redirect('/'.Auth::user()->team->app_type.'/constituents/'.$does_a_person_exist->id);
            }

            // OTHERWISE:

            $shared_cases = $this->getSharedCases($id);
            //dd($shared_cases);

            $voter = Voter::find($id);

            if (!$voter->household_id) {
                // should try to fill in ID
                $voter->save();
                $voter = Voter::find($id);
            }

            // Format emails to display before Voter is a Person
            $voter_other_emails_array = [];
            if ($voter->emails) {
                if (count($voter->emails) > 0) {
                    $voter_other_emails_array[] = [$voter->emails[0], 'Primary'];

                    if (count($voter->emails) > 1) {
                        foreach (array_slice($voter->emails, 1) as $value) {
                            $voter_other_emails_array[] = [$value, null];
                        }
                    }
                }
            }

            // Format phones to display before Voter is a Person
            $voter_other_phones_array = [];
            if ($voter->cell_phone) {
                $voter_other_phones_array[] = [$voter->cell_phone, 'Mobile'];
            }
            if ($voter->home_phone) {
                $voter_other_phones_array[] = [$voter->home_phone, 'Home'];
            }

            $other_emails = $this->otherEmailPhoneObject($voter_other_emails_array);
            $other_phones = $this->otherEmailPhoneObject($voter_other_phones_array);
            //dd(session('team_state'));
            if (session('team_state') == 'RI') {
                $cohabitors = $this->getCohabitorsYob($voter, $id);
            } else {
                $cohabitors = $this->getCohabitors($voter, $id);    
            }

            $groupcats = Auth::user()->team->categories()->get();

            $voterRecord = $voter;
            $person = $voter;
            $mode_external = true;

            $business = $this->getArrayFieldsAsObject($person, 'business_info', ['occupation', 'work_phone', 'work_phone_ext', 'fax', 'name', 'address_1', 'address_2', 'city', 'state', 'zip', 'zip4', 'web']);

            return view('shared-features.constituents.show', compact('person',
                                                                  'cohabitors',
                                                                  'voterRecord',
                                                                  'tab',
                                                                  'mode_external',
                                                                  'groupcats',
                                                                  'other_emails',
                                                                  'other_phones',
                                                                  'business',
                                                                  'shared_cases',
                                                                  ));
        }
    }

    public function getCohabitorsPerson($person) {
        $cohabitors_2 = Person::select('id', 'voter_id', 'full_name', 'updated_at', 'dob', DB::raw('YEAR(dob) as yob'), 'gender', DB::raw('0 as external'))
                         ->where('household_id', $person->household_id)
                         ->where('team_id', $person->team_id)
                         ->where('id', '<>', $person->id);

                $cohabitors = Voter::select('id', DB::raw('null as voter_id'), 'full_name', 'updated_at', 'dob', DB::raw('YEAR(dob) as yob'), 'gender', DB::raw('1 as external'))
                             ->where('household_id', $person->household_id)
                            ->whereNotIn('id', $cohabitors_2->pluck('voter_id')->toArray())
                            ->where('id', '<>', $person->voter_id)
                            ->union($cohabitors_2)
                            ->get();
        return $cohabitors;
    }
    public function getCohabitorsPersonYob($person) {
        $cohabitors_2 = Person::select('id', 'voter_id', 'full_name', 'updated_at', 'dob', DB::raw('YEAR(dob) as yob'), 'gender', DB::raw('0 as external'))
                         ->where('household_id', $person->household_id)
                         ->where('team_id', $person->team_id)
                         ->where('id', '<>', $person->id);

                $cohabitors = Voter::select('id', DB::raw('null as voter_id'), 'full_name', 'updated_at', 'dob', 'yob', 'gender', DB::raw('1 as external'))
                             ->where('household_id', $person->household_id)
                            ->whereNotIn('id', $cohabitors_2->pluck('voter_id')->toArray())
                            ->where('id', '<>', $person->voter_id)
                            ->union($cohabitors_2)
                            ->get();
        return $cohabitors;
    }
    public function getCohabitors($voter, $id)
    {
        $cohabitors_2 = Person::select('id', 'voter_id', 'full_name', 'updated_at', 'dob', DB::raw('YEAR(dob) as yob'), DB::raw('0 as external'))
                     ->where('household_id', $voter->household_id)
                     ->where('team_id', Auth::user()->team->id)
                     ->whereNotIn('id', Person::select('id')->where('voter_id', $id))
                     ->where('id', '<>', $voter->id);

        $cohabitors = Voter::select('id', DB::raw('id as voter_id'), 'full_name', 'updated_at', 'dob', DB::raw('YEAR(dob) as yob'), DB::raw('1 as external'))
                         ->where('household_id', $voter->household_id)
                         ->where('id', '<>', $id)
                         ->whereNotIn('id', Person::select('voter_id')->where('household_id', $voter->household_id)->where('team_id', Auth::user()->team->id))
                         ->union($cohabitors_2)
                         ->get();
        return $cohabitors;
    }
    public function getCohabitorsYob($voter, $id)
    {
        $cohabitors_2 = Person::select('id', 'voter_id', 'full_name', 'updated_at', 'dob', DB::raw('YEAR(dob) as yob'), DB::raw('0 as external'))
                     ->where('household_id', $voter->household_id)
                     ->where('team_id', Auth::user()->team->id)
                     ->whereNotIn('id', Person::select('id')->where('voter_id', $id))
                     ->where('id', '<>', $voter->id);

        $cohabitors = Voter::select('id', DB::raw('id as voter_id'), 'full_name', 'updated_at', 'dob', 'yob', DB::raw('1 as external'))
                         ->where('household_id', $voter->household_id)
                         ->where('id', '<>', $id)
                         ->whereNotIn('id', Person::select('voter_id')->where('household_id', $voter->household_id)->where('team_id', Auth::user()->team->id))
                         ->union($cohabitors_2)
                         ->get();
        return $cohabitors;
    }
    

    public function new($app_type, $v = null)
    {
        $v = trim($v);
        if (strpos($v, ' ') == false) {
            $first_name = $v;
            $last_name = '';
        } else {
            $first_name = substr($v, 0, strpos($v, ' '));
            $last_name = substr($v, strpos($v, ' ') + 1, strlen($v));
        }

        $categories = Category::where('team_id', Auth::user()->team->id)
                              ->orderBy('name')
                              ->get();

        return view('shared-features.constituents.new', compact('first_name', 'last_name', 'categories'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    // public function index()
    // {
    //     if (Auth::user()->getMemory('show_constituents_type') == 'team') {
    //         return $this->indexLinked();
    //     } else {
    //         return $this->indexAll();
    //     }
    // }

    public function indexLinked()
    {
        Auth::user()->addMemory('show_constituents_type', 'team');
        request()->merge(['linked' => true, 'all_people' => true]);

        return $this->indexAll();
    }

    public function indexAll()
    {
        return view('shared-features.constituents.index-new');

        // Auth::user()->addMemory('show_constituents_type', 'all');

        // $people         = $this->constituentQuery(request()->input());

        // $municipalities = $this->getMunicipalities();
        // $districts = $this->getDistricts();
        // $zips = $this->getZips();
        // $categories = Auth::user()->team->categories;

        // $total_count = $this->total_count;
        // $total_count_people = $this->total_count_people;
        // $total_count_voters = $this->total_count_voters;

        // $input = request()->input();

        // return view('shared-features.constituents.index', compact('input', 'people', 'total_count', 'total_count_voters', 'total_count_people', 'districts', 'municipalities', 'zips', 'categories'))->with('mode_all', 1);
    }

    public function list()
    {
        $search_value = request('constituent_name');
        $input = request()->input();
        $people = $this->constituentQuery($input);
        $total_count = $this->total_count;
        $total_count_people = $this->total_count_people;
        $total_count_voters = $this->total_count_voters;

        return view('shared-features.constituents.list', compact('people',
                                                                'input',
                                                                'total_count',
                                                                'total_count_people',
                                                                'total_count_voters',
                                                                'search_value'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function regulars()
    {
        $top_contacts = ContactPerson::selectRaw('COUNT(*) as count, person_id')
                            ->where('team_id', Auth::user()->team_id)
                            ->groupBy('person_id')
                            ->orderByDesc('count')
                            ->having('count', '>', '2')
                            ->pluck('person_id');

        $top_cases = CasePerson::selectRaw('COUNT(*) as count, person_id')
                            ->where('team_id', Auth::user()->team_id)
                            ->groupBy('person_id')
                            ->orderByDesc('count')
                            ->having('count', '>', '2')
                            ->pluck('person_id');

        $top_groups = GroupPerson::selectRaw('COUNT(*) as count, person_id')
                            ->where('team_id', Auth::user()->team_id)
                            ->groupBy('person_id')
                            ->orderByDesc('count')
                            ->having('count', '>', '2')
                            ->pluck('person_id');
        
        $top = $top_cases->merge($top_contacts)->merge($top_groups)->unique();

        $people = Auth::user()->team->people()
                              ->whereIn('id', $top)
                              ->with(['contacts', 'groups', 'cases'])
                              ->get();
        //dd($people);
        $people = $people->sortByDesc('activity_count');

        if (request('export')) {
            //dd($people);
            $plain = $people->map(function ($item) {
                            $item->activity_count = $item->activity_count;
                            $item->mailing_address_or_residential = $item->mailing_address_or_residential;
                            //dd($item);
                            return collect($item->toArray())
                                    ->only(['full_name', 'first_name', 'last_name', 'address_street', 'address_number', 'address_apt', 'address_city', 'address_zip', 'mailing_address_or_residential', 'activity_count'])
                                    ->all();
                });
            //dd($plain);
            return $this->createCSV($plain);
        }
        //dd($people);
        return view('shared-features.constituents.regulars', compact('people'));
    }
    public function searchDashboard($app_type, $v = null)
    {
        $v = trim($v);
        $mode_all = 1;
        $search_value = $v;

        if ($v == null) {
            return null;
        }
        if (strlen($v) > 2) {
            $people = $this->constituentQuery(['constituent_name' => $v], $limit = 'none', null);
        } else {
            $people = $this->constituentQuery(['constituent_name' => $v], null, null);
        }

        return view('shared-features.dashboard.list', compact(
                                                        'people',
                                                        'mode_all',
                                                        'search_value'));
    }

    public function searchAll($app_type, $v = null)
    {
        $v = trim($v);
        $mode_all = 1;

        if (($v == null) || (strlen($v) < 1)) {
            $people = $this->constituentQuery(['constituent_name' => $v], null, null);
            $total_count = $this->total_count;

            return view('shared-features.constituents.list', compact('people',
                                                                    'total_count',
                                                                    'mode_all',
                                                                    'search_value'));
        } elseif (strlen($v) > 0) {
            $people = $this->constituentQuery(['constituent_name' => $v], $limit = 'none', null);
            $total_count = $this->total_count;

            return view('shared-features.constituents.list', compact('people',
                                                                    'total_count',
                                                                    'mode_all',
                                                                    'search_value'));
        }
    }

    public function searchPeople($app_type, $v = null)
    {
        $v = trim($v);

        if (($v == null) || (strlen($v) <= 2)) {
            $people = $this->constituentQuery(['constituent_name' => $v], null, null);

            return view('shared-features.constituents.list', compact('people', 'search_value'));
        } elseif (strlen($v) > 2) {
            $people = $this->constituentQuery(['constituent_name' => $v], $limit = 'none', null);

            return view('shared-features.constituents.list', compact('people', 'search_value'));
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////

    public function getTeamPeopleFromSearch($v, $paginate = null)
    {
        $input = [
            'people_only'      => true,
            'constituent_name' => $v,
        ];

        return $this->constituentQuery($input);
    }

    public function getTeamPeopleAndVotersFromSearch($v, $paginate = null)
    {
        $input = ['constituent_name' => $v];

        return $this->constituentQuery($input);
    }

    public function getTeamPeopleAndVotersAndEntitiesFromSearch($v, $paginate = null)
    {
        $people_1 = Person::select(DB::raw('1 as person'),
                                   DB::raw('0 as entity'),
                                            'id',
                                            'full_name',
                                            'full_address',
                                            'dob',
                                            'last_name',
                                            'support')
                ->where(function ($q) use ($v) {
                    $q->orWhere('first_name', 'like', '%'.$v.'%');
                    $q->orWhere('last_name', 'like', '%'.$v.'%');
                })
                ->where('team_id', Auth::user()->team->id);

        $people_1 = $people_1->orderBy('last_name');

        $entities = Entity::select(DB::raw('0 as person'),
                                   DB::raw('1 as entity'),
                                            'id',
                                            'name',
                                            'full_address',
                                            'dob',
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
                                        'dob',
                                        'last_name',
                                        DB::raw('null as support'));

        $people = $people->whereNotIn('id', Person::select('voter_id')
                         ->where('team_id', Auth::user()->team->id));

        $people = $people->where(function ($q) use ($v) {
            $q->orWhere('first_name', 'like', '%'.$v.'%');
            $q->orWhere('last_name', 'like', '%'.$v.'%');
        })
        ->union($people_1)
        ->union($entities)
        ->orderBy('last_name');

        if ($paginate) {
            $people = $people->paginate($paginate);
        } else {
            $people = $people->get();
        }

        return $people;
    }

    //////////////////////////////////////////////////////////////////////////////////////
}
