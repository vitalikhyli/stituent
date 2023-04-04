<?php
/* EDITING OUT UNNECESSSARY CONTROLLER

namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Person;
use App\Group;
use App\Category;
use App\GroupPerson;
use App\Voter;
use App\Contact;
use App\Household;
use App\WorkCase;
use App\Entity;
use App\BulkMailQueue;

use DB;
use Auth;

use Validator;

use Carbon\Carbon;


class PeopleController extends Controller
{

    private static $blade = 'university';
    private static $dir = '/u';

    public function edit($id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        $this->authorize('basic', $person);

        return view(self::$blade.'.constituents.edit',compact('person'));
    }

    public function save(Request $request)
    {
        $person = new Person;
        $person->first_name         = request('first_name');
        $person->last_name          = request('last_name');
        $person->team_id            = Auth::user()->team->id;
        $person->save();

        $person->full_name          = $person->generateFullName();

        //Add Initial Groups
        foreach ($request->all() as $key => $value) {
            if(substr($key,0,6) == 'group_') {
                $group_id        = substr($key,6);
                $group = Group::find($group_id);

                $this->authorize('basic', $group);

                $category = Category::find($group->category_id);
                $person->groups()->attach($group_id, ['team_id' => Auth::user()->team->id,
                                                      'data' => json_encode($category->data_template)]);
            }
        }

        return redirect(self::$dir.'/constituents/'.$person->id.'/edit');

    }

    public function update(Request $request, $id)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);

        $this->authorize('basic', $person);

        $old_hh_id  = $person->household_id;

        //VALIDATION START
        $validate_array = $request->all();

        // PROCESS EMAILS + ADD TO VALIDATION
        $email_number = request('email_number');
        $email_main = request('email_main');
        $emails_array = array();
        for ($e=1; $e<=$email_number; $e++) {
            if (request('email_'.$e) != null) {
                ($e == $email_main) ? $main = 1 : $main = 0;
                $validate_array['emails'][] = request('email_'.$e);
                $emails_array[] = array(
                    "main" => $main,
                    "email" => request('email_'.$e),
                    "notes" => request('email_notes_'.$e),
                    );
            }
        }

        // PROCESS PHONES + ADD TO VALIDATION
        $phone_number = request('phone_number');
        $phone_main = request('phone_main');
        $phones_array = array();
        for ($e=1; $e<=$phone_number; $e++) {
            if (request('phone_'.$e) != null) {
                ($e == $phone_main) ? $main = 1 : $main = 0;
                $phones_array[] = array(
                    "main" => $main,
                    "phone" => request('phone_'.$e),
                    "notes" => request('phone_notes_'.$e),
                    );
            }
        }

        // VALIDATE
        $validator = Validator::make($validate_array, [
                'first_name' => ['required', 'max:255'],
                'last_name' => ['required', 'max:255'],
                'emails.*' => ['email'],
                // 'primary_email' => ['email'],
                // 'work_email' => ['email'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        // UPDATE RECORD
        $person->private            = request('private');
        $person->first_name         = request('first_name');
        $person->middle_name        = request('middle_name');
        $person->last_name          = request('last_name');
        $person->address_street     = request('address_street');
        $person->address_number     = request('address_number');
        $person->address_fraction   = request('address_fraction');
        $person->address_apt        = request('address_apt');
        $person->address_city       = request('address_city');
        $person->address_state      = substr(request('address_state'),0,2);
        $person->address_zip        = substr(request('address_zip'),0,5);

        $person->primary_email      = request('primary_email');
        $person->work_email         = request('work_email');
        $person->other_emails       = json_encode($emails_array);

        $person->primary_phone      = request('primary_phone');
        $person->other_phones       = json_encode($phones_array);

        $person->gender             = request('gender');

        $person->save();

        // COMPOUND FIELDS
        $person->full_name          = $person->generateFullName();
        $person->full_name_middle   = $person->generateFullNameMiddle();
        $person->full_address       = $person->generateFullAddress();
        $person->household_id       = $person->generateHouseholdId();

        $person->save();
        $person->updateHouseholds();

        session()->flash('msg', 'Person was Saved!');

        if (request('save_and_close')) {
            return redirect(self::$dir.'/constituents/'.$person->id);
        } else {
            return redirect(self::$dir.'/constituents/'.$person->id.'/edit');
        }
    }

    public function show($id)
    {

        $tab = Auth::user()->getmemory('person_tabs', 'notes');

        if (IDisPerson($id)) {

            $person = Person::find($id);

            $this->authorize('basic', $person);

            if(!$person) { dd('Error - No person found!', $id); }

            $groups = $person->groups;

            $cases = $person->cases;

            $contacts = Contact::select(DB::raw('"note" as type'),
                                        DB::raw('contacts.date as date'),
                                        DB::raw('contacts.id as id'),
                                        'notes',
                                        'private',
                                        'followup',
                                        'followup_on',
                                        'followup_done',
                                        DB::raw('null as name'),
                                        DB::raw('null as subject')
                                        )
                                ->join('contact_person','contacts.id','contact_id')
                                ->where('contact_person.person_id',$id)
                                ->where('case_id', null)
                                ->where(function($q) {
                                    $q->orWhere('private',false);
                                    $q->orWhere('user_id',Auth::user()->id);
                                 });

            $bulk_emails = BulkMailQueue::select(DB::raw('"bulk_email" as type'),
                                                 DB::raw('bulk_emails.completed_at as date'),
                                                 DB::raw('bulk_emails.id as id'),
                                                 DB::raw('null as notes'),
                                                 DB::raw('null as private'),
                                                 DB::raw('null as followup'),
                                                 DB::raw('null as followup_on'),
                                                 DB::raw('null as followup_done'),
                                                 'name',
                                                 'subject'
                                                 )
                                        ->where('person_id',$person->id)
                                        ->join('bulk_emails','bulk_email_id','bulk_emails.id');

            $contacts = $contacts->union($bulk_emails)->orderBy('date','desc')->get();


            if (!$person->household_id) {
                $cohabitors = null;
            } else {
                $cohabitors_2 = Person::select('id','full_name','updated_at','dob',DB::raw('0 as external'))
                         ->where('household_id',$person->household_id)
                         ->where('team_id', $person->team_id)
                         ->where('id','<>',$person->id);

                $cohabitors = Voter::select('id','full_name','updated_at','dob',DB::raw('1 as external'))
                             ->where('household_id',$person->household_id)
                            ->whereNotIn('id',Person::select('voter_id')
                            ->where('team_id',$person->team_id))
                            ->union($cohabitors_2)
                            ->get();
            }

            $voterRecord = Voter::find($person->voter_id);
            if (!$voterRecord) { $voterRecord = null; }


            $email_list_cat = Category::where('preset', self::$blade)
                                      ->where('name','email lists')
                                      ->first();

            if ($email_list_cat) {

                $email_list_cat_id = $email_list_cat->id;

                $groupcats     = Category::where('preset', self::$blade)
                                         ->where('id','<>',$email_list_cat_id)
                                         ->get();
            } else {

                $email_list_cat_id = null;

                $groupcats = null;

            }

            $mode_external = false;

            return view(self::$blade.'.constituents.show', compact('person',
                                                                   'groupcats',
                                                                   'groups',
                                                                   'cases',
                                                                   'contacts',
                                                                   'cohabitors',
                                                                   'voterRecord',
                                                                   'tab',
                                                                   'bulk_emails',
                                                                   'email_list_cat_id',
                                                                   'mode_external'));
        }

        if (IDisVoter($id)) {

            $voter = Voter::find($id);

            $cohabitors = Person::select('id','full_name','updated_at','dob',DB::raw('0 as external'))
                     ->where('household_id',$voter->household_id)
                     ->where('team_id', Auth::user()->team->id)
                     ->whereNotIn('id',Person::select('id')->where('voter_id',$id))
                     ->where('id','<>',$voter->id);

            $cohabitors = Voter::select('id','full_name','updated_at','dob',DB::raw('1 as external'))
                         ->where('household_id',$voter->household_id)
                         ->where('id','<>',$id)
                         ->whereNotIn('id',Person::select('voter_id')->where('household_id',$voter->household_id)->where('team_id',Auth::user()->team->id))
                         ->union($cohabitors)
                         ->get();



            $voterRecord = $voter;
            $person = $voter;
            $mode_external = true;

            return view(self::$blade.'.constituents.show', compact('person',
                                                                  'cohabitors',
                                                                  'voterRecord',
                                                                  'tab',
                                                                  'mode_external'
                                                                  ));
        }

    }

    public function new($v)
    {
        $v = trim($v);
        if(strpos($v, " ") == false) {
            $first_name = $v;
            $last_name = '';
        } else {
            $first_name = substr($v, 0, strpos($v, " "));
            $last_name = substr($v, strpos($v, " ")+1, strlen($v));
        }

        $categories = Category::where('preset',self::$blade)
                              ->orWhere(function ($q) {
                                $q->where('team_id', Auth::user()->team->id);
                              })
                              ->orderBy('name')
                              ->get();

        return view(self::$blade.'.constituents.new', compact('first_name','last_name','categories'));

    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function index()
    {
        if (Auth::user()->getMemory('show_constituents_type') == 'team') {
            return redirect(self::$dir.'/constituents_linked');
        } else {
            return redirect(self::$dir.'/constituents_all');
        }
    }

    public function indexPeople()
    {
        $people = Person::select(DB::raw("'1' as person"), 'id', 'full_name', 'full_address',
        'dob', 'last_name', 'support')
            ->where('team_id',Auth::user()->team->id)
            ->orderBy('last_name')
            ->paginate(100);

        Auth::user()->addMemory('show_constituents_type', 'team');
        return view(self::$blade.'.constituents.index', compact('people'));
    }

    public function indexAll()
    {

        $people = $this->getTeamPeopleAndVoters();

        Auth::user()->addMemory('show_constituents_type', 'all');
        $total_count = $this->total_count;

        return view(self::$blade.'.constituents.index', compact('people', 'total_count'))->with('mode_all',1);
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function searchCasesPeople($case_id, $v=null)
    {
        $v = trim($v);
        $mode_all       = 1;
        $search_value   = $v;

        if ($v == null) {

            return null;

        } elseif (strlen($v) > 2) {

            $people = $this->getTeamPeopleAndVotersFromSearch($v);

        } else {

            $people = $this->getTeamPeopleAndVoters();

        }

        //Remove people already selected
        $attached_people = DB::table('case_person')
                             ->where('case_id',$case_id)
                             ->get()
                             ->pluck('person_id')
                             ->toArray();

        $people = $people->whereNotIn('id',$attached_people);

        return view(self::$blade.'.cases.list-people', compact('people',
                                                        'mode_all',
                                                        'search_value'));
    }

    public function searchDashboard($v=null)
    {
        $v = trim($v);
        $mode_all       = 1;
        $search_value   = $v;

        if ($v == null) {
            return null;
        }
        if (strlen($v) > 2) {
            $people = $this->getTeamPeopleAndVotersAndEntitiesFromSearch($v);
        } else {
            $people = $this->getTeamPeopleAndVoters();
        }
        return view(self::$blade.'.dashboard.list', compact(
                                                        'people',
                                                        'mode_all',
                                                        'search_value'));
    }

    public function searchAll($search_value=null)
    {
        $search_value = trim($search_value);
        $mode_all = 1;

        if (($search_value == null) || (strlen($search_value) < 1)) {
            $people = $this->getTeamPeopleAndVoters();
            $total_count = $this->total_count;
            return view(self::$blade.'.constituents.list', compact('people',
                                                                    'total_count',
                                                                    'mode_all',
                                                                    'search_value'));
        } elseif (strlen($search_value) > 0) {
            $people = $this->getTeamPeopleAndVotersFromSearch($search_value);
            $total_count = $this->total_count;
            return view(self::$blade.'.constituents.list', compact('people',
                                                                    'total_count',
                                                                    'mode_all',
                                                                    'search_value'));
        }
    }

    public function searchPeople($search_value=null)
    {
        $search_value = trim($search_value);

        if (($search_value == null) || (strlen($search_value) <= 2)) {

            $people = $this->getTeamPeople();
            return view(self::$blade.'.constituents.list', compact('people','search_value'));

        } elseif (strlen($search_value) > 2) {

            $people = $this->getTeamPeopleFromSearch($search_value);
            return view(self::$blade.'.constituents.list', compact('people','search_value'));

        }
    }


    //////////////////////////////////////////////////////////////////////////////////////

    public function getTeamPeopleFromSearch($v, $paginate = null)
    {
        $people = Person::select(DB::raw("'1' as person"),
                                         'id',
                                         'full_name',
                                         'full_address',
                                         'dob',
                                         'last_name',
                                         'support')
                ->where(function($q) use ($v){
                    $q->orWhere('first_name','like','%'.$v.'%');
                    $q->orWhere('last_name','like','%'.$v.'%');
                    $q->orWhere('full_name','like','%'.$v.'%');
                })
                ->where('team_id',Auth::user()->team->id)
                ->orderBy('last_name');


        if ($paginate) {
            $people = $people->paginate($paginate);
        } else {
            $people = $people->get();
        }

        return $people;
    }

    public function getTeamPeopleAndVotersFromSearch($v, $paginate = null)
    {
        $start = microtime(-1);

        $names_arr = explode(' ', trim($v));
        $name = ""; $first_name = ""; $last_name = "";

        if (count($names_arr) == 1) {
            $name = $names_arr[0];
        } else if (count($names_arr) > 1) {
            $first_name = $names_arr[0];
            $last_name  = $names_arr[1];
        }

        $people = Person::select(DB::raw("1 as person"),
                                            'id',
                                            'voter_id',
                                            'full_name',
                                            'full_address',
                                            'dob',
                                            'last_name',
                                            'support');
        if ($name) {
                $people->where(function($q) use ($name){
                    $q->orWhere('first_name','like',$name.'%');
                    $q->orWhere('last_name','like',$name.'%');
                });
        }
        if ($first_name) {
                $people->where('first_name','like',$first_name.'%')
                       ->where('last_name','like',$last_name.'%');
        }
        $people = $people->where('team_id',Auth::user()->team->id)
                         ->orderBy('last_name')
                         ->paginate(100);


        $voters = Voter::select(DB::raw("0 as person"),
                                        'id',
                                        'full_name',
                                        'full_address',
                                        'dob',
                                        'last_name',
                                        DB::raw("null as support"));

        $voters = $voters->whereNotIn('id',$people->pluck('voter_id'));

        if ($name) {
                $voters->where(function($q) use ($name){
                    $q->orWhere('first_name','like',$name.'%');
                    $q->orWhere('last_name','like',$name.'%');
                });
        }
        if ($first_name) {
                $voters->where('first_name','like',$first_name.'%')
                       ->where('last_name','like',$last_name.'%');
        }
        $voters = $voters->orderBy('last_name')
                         ->paginate(100);

        $this->total_count = $voters->total() + $people->total();

        $everyone = $people->merge($voters)
                           ->sortBy('last_name');
        //echo microtime(-1) - $start;
        return $everyone;
    }


    public function getTeamPeopleAndVotersAndEntitiesFromSearch($v, $paginate = null)
    {
        $people_1 = Person::select(DB::raw("1 as person"),
                                   DB::raw("0 as entity"),
                                            'id',
                                            'full_name',
                                            'full_address',
                                            'dob',
                                            'last_name',
                                            'support')
                ->where(function($q) use ($v){
                    $q->orWhere('first_name','like','%'.$v.'%');
                    $q->orWhere('last_name','like','%'.$v.'%');
                })
                ->where('team_id',Auth::user()->team->id);

        $people_1 = $people_1->orderBy('last_name');

        $entities = Entity::select(DB::raw("0 as person"),
                                   DB::raw("1 as entity"),
                                            'id',
                                            'name',
                                            'full_address',
                                            'dob',
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
                                        'dob',
                                        'last_name',
                                        DB::raw("null as support"));

        $people = $people->whereNotIn('id',Person::select('voter_id')
                         ->where('team_id',Auth::user()->team->id));

        $people = $people->where(function($q) use ($v){
                        $q->orWhere('first_name','like','%'.$v.'%');
                        $q->orWhere('last_name','like','%'.$v.'%');
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

    public function editContact(Request $request, $person_id, $contact_id)
    {
        $theperson = Person::find($person_id);
        $thecontact = Contact::find($contact_id);

        $this->authorize('basic', $theperson);
        $this->authorize('basic', $thecontact);

        $form_action = '/constituents/'.$theperson->id.'/contacts/'.$thecontact->id;

        return view(self::$blade.'.contacts.edit', compact('thecontact','form_action'));
    }


    public function getTeamPeopleAndVoters()
    {
        //$start = microtime(-1);
        $people = Person::select(DB::raw("1 as person"), 'id', 'full_name', 'full_address',
        'dob', 'last_name', 'support')
                        ->where('team_id',Auth::user()->team->id)
                        ->orderBy('last_name')
                        ->take(100)
                        ->get();

        $voters = Voter::select(DB::raw("0 as person"), 'id', 'full_name', 'full_address',
        'dob', 'last_name', DB::raw("null as support"))
                       ->orderBy('last_name')
                       ->take(100)
                       ->get();

        $this->total_count = Auth::user()->team->constituents_count;

        $people = $people->merge($voters)->sortBy('last_name');

        return $people;
    }


}
