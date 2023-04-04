<?php

namespace App\Http\Controllers;

use App\CallLogViewModel;
use App\Contact;
use App\ContactEntity;
use App\ContactPerson;
use App\Entity;
use App\Person;
use App\Traits\ConstituentQueryTrait;
use App\Traits\ExportTrait;
use App\User;
use App\Voter;
use App\WorkCase;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallLogController extends Controller
{
    use ConstituentQueryTrait;
    use ExportTrait;

    public function report(Request $request)
    {
        $arguments = request()->all();

        if(!isset($arguments['end'])) {
            $arguments['end'] = Carbon::today()->toDateString(); //default
        }

        $start = Carbon::parse($arguments['start'])->format('Y-m-d');
        $end    = Carbon::parse($arguments['end'])->addDay()->format('Y-m-d');
        $user   = isset($arguments['user']) ? $arguments['user'] : null;

        $calls_original = Contact::TeamOrPrivateAndMine()
                        ->where('team_id', Auth::user()->team->id)
                        ->where('date', '>=', $start)
                        ->where('date', '<', $end);

        if ($user) $calls_original = $calls_original->where('user_id', $user);

        $calls = $calls_original->select('id',
                                'date',
                                'type',
                                'source',
                                'subject',
                                'notes',
                                'followup',
                                'followup_on',
                                'followup_done',
                                'private',
                                'user_id',
                                'created_at');

        $calls = $calls->orderBy('date', 'desc');

        // if(!$calls->first()) {
        //     $msg = 'There are no calls between '.$start.' and '.$end;
        //     if ($user) {
        //         $msg .= ' for user '.User::find($user)->name;
        //     }
        //     echo $ms;
        // }

        $calls = $calls->get();

        // Add: Concatenate linked people

        try {
            if ($arguments['format'] == 'csv') {

                
                $calls->each(function ($item, $key) {

                    // Add these to the CSV file:
                    $user = User::find($item['user_id']);
                    $item['user'] = ($user) ? $user->name : null;
                    $item['title'] = ($user->permissions) ? $user->permissions->title : null;

                    // Don't include these in the CSV file:
                    unset($item['id']);
                    unset($item['user_id']);
                    
                });

                return $this->createCSV($calls);
            }

            if ($arguments['format'] == 'pdf') {
                return $this->PDFReport($calls);
            }
        } catch (\Exception $e) {
            echo "Error: ".$e->getMessage();
            // Add: Error handling
        }
    }

    public function PDFReport($calls)
    {
        return view('shared-features.call-log.report', compact('calls'));
    }

    public function searchAllLogs(Request $request)
    {
        $v = request('search_value');

        if (! $v) {
            $call_log = new CallLogViewModel(Auth::user());

            return view('shared-features.call-log.content', compact('call_log'));
        } else {

            // Search Contacts

            $logs = Contact::where('team_id', Auth::user()->team->id)
                           ->where(function ($q) use ($v) {
                               $q->orWhere('contacts.notes', 'like', '%'.$v.'%');
                               $q->orWhere('contacts.subject', 'like', '%'.$v.'%');
                           })
                           ->get();

            // Search People Connected to Contacts

            $people_contacts_ids = Contact::where('contacts.team_id', Auth::user()->team->id)
                        ->join('contact_person', 'contacts.id', '=', 'contact_person.contact_id')
                        ->join('people', 'contact_person.person_id', '=', 'people.id')
                        ->where('contacts.team_id', Auth::user()->team->id)
                        ->where(function ($q) use ($v) {
                            $q->orWhere('people.full_name', 'like', '%'.$v.'%');
                        })
                        ->pluck('contacts.id')->toArray();

            $people = Contact::whereIn('id', $people_contacts_ids)->get();

            // Merge Collections

            $logs = $logs->merge($people);

            return view('shared-features.call-log.search-result', compact('logs', 'v'));
        }
    }

    public function lookUp($v = null)
    {
        $v = trim($v);
        $mode_all = 1;
        $search_value = $v;

        //dd($v);

        if ($v == null || strlen($v) <= 1) {
            return null;
        } elseif (strlen($v) > 1) {
            $people = $this->getPeopleAndVotersFromName($v);
        }


        return view('shared-features.call-log.list', compact('people',
                                                             'mode_all',
                                                             'search_value'));
    }

    // public function getTeamPeopleAndVotersAndEntitiesFromSearch($v, $paginate = null)
    // {

    //     $people =   Person::select(DB::raw("1 as person"),
    //                                DB::raw("0 as entity"),
    //                                         'id',
    //                                         'full_name',
    //                                         'full_address',
    //                                         'household_id',
    //                                         'last_name',
    //                                         'support')
    //             ->where(function($q) use ($v){
    //                 $q->orWhere('first_name','like','%'.$v.'%');
    //                 $q->orWhere('last_name','like','%'.$v.'%');
    //                 $q->orWhere('full_name','like','%'.$v.'%');
    //             })
    //             ->where('team_id',Auth::user()->team->id)
    //             ->orderBy('last_name')->get();

    //     $entities = Entity::select(DB::raw("0 as person"),
    //                                DB::raw("1 as entity"),
    //                                         'id',
    //                                         'name',
    //                                         'full_address',
    //                                         'household_id',
    //                                         DB::raw("name as last_name"),
    //                                         DB::raw("null as support"))
    //             ->where(function($q) use ($v){
    //                 $q->orWhere('name','like','%'.$v.'%');
    //             })
    //             ->where('team_id',Auth::user()->team->id)
    //             ->orderBy('name')->get();

    //     $voters = Voter::select(DB::raw("0 as person"),
    //                             DB::raw("0 as entity"),
    //                                     'id',
    //                                     'full_name',
    //                                     'full_address',
    //                                     'household_id',
    //                                     'last_name',
    //                                     DB::raw("null as support"))
    //                     ->whereNotIn('id',Person::select('voter_id')
    //                     ->where('team_id',Auth::user()->team->id))
    //                     ->where(function($q) use ($v){
    //                         $q->orWhere('first_name','like','%'.$v.'%');
    //                         $q->orWhere('last_name','like','%'.$v.'%');
    //                         $q->orWhere('full_name','like','%'.$v.'%');
    //                     })
    //                     // ->union($people_1)
    //                     // ->union($entities)
    //                     ->orderBy('last_name')->get();

    //     $all_of_it = $people->merge($voters)->merge($entities);

    //     // if ($paginate) {
    //     //     $people = $people->paginate($paginate);
    //     // } else {
    //     //     $people = $people->get();
    //     // }

    //     return $all_of_it;
    // }

    public function searchEntities($v, $thecall_id)
    {
        $entities = Entity::where('team_id', Auth::user()->team->id)
                          ->where('name', 'like', '%'.$v.'%')
                          ->get();

        $thecall = Contact::find($thecall_id);

        return view('shared-features.call-log.modal-list-entities', compact('entities', 'thecall'));
    }

    public function search($v, $thecall_id)
    {
        if ($v == null || strlen($v) <= 1) {
            return null;
        } elseif (strlen($v) > 1) {
            $people = $this->getPeopleFromName($v);

            $thecall = Contact::find($thecall_id);

            return view('shared-features.call-log.modal-list', compact('people', 'thecall'));
        }

        // $people_1 = Person::select(DB::raw("1 as person"), 'id', 'full_name', 'full_address', 'last_name', 'support')
        //         ->where(function($q) use ($v){
        //             $q->orWhere('first_name','like','%'.$v.'%');
        //             $q->orWhere('last_name','like','%'.$v.'%');
        //             $q->orWhere('full_name','like','%'.$v.'%');
        //         })
        //         ->where('team_id',Auth::user()->team->id);

        // $people_1 = $people_1->orderBy('last_name');

        // $people = Voter::select(DB::raw("0 as person"), 'id', 'full_name', 'full_address', 'last_name',DB::raw("null as support"));

        // $people = $people->whereNotIn('id',Person::select('voter_id')->where('team_id',Auth::user()->team->id));

        // $people = $people->where(function($q) use ($v){
        //                 $q->orWhere('first_name','like','%'.$v.'%');
        //                 $q->orWhere('last_name','like','%'.$v.'%');
        //                 $q->orWhere('full_name','like','%'.$v.'%');
        //             })
        // ->union($people_1)
        // ->take(5)
        // ->orderBy('last_name');
        // $people = $people->get();

        // $thecall = Contact::find($thecall_id);

        // return view('shared-features.call-log.modal-list', compact('people', 'thecall'));
    }

    public function scanForNames($call_id)
    {
        // $call = Contact::find($call_id);

        // $people_1 = Person::select('full_name', 'id')
        //                   ->where('team_id',Auth::user()->team->id)
        //                   ->whereNotIn('id',ContactPerson::select('person_id')->where('contact_id',$call_id));

        // $people = Voter::select('full_name', 'id')
        //                ->whereNotIn('id',
        //                 Person::select('voter_id')
        //                   ->where('team_id',Auth::user()->team->id)
        //                 )
        //                ->union($people_1)->get();

        // $suggested_people = array();
        // foreach($people as $theperson) {
        //     if (trim($theperson->full_name)) { //PREVENT EMPTY SUGGESTIONS
        //         if (stripos($call->subject.$call->notes, $theperson->full_name) !== false) {
        //             $suggested_people[] = array("id" => $theperson->id);
        //         }
        //     }
        // }

        // $call->suggested_people = json_encode($suggested_people);
        // $call->save();
    }

    public function scanForEntities($call_id)
    {
        // $call = Contact::find($call_id);

        // $entities = Entity::select('name', 'id')
        //                   ->where('team_id',Auth::user()->team->id)
        //                   ->whereNotIn('id', ContactEntity::select('entity_id')->where('contact_id',$call_id))
        //                   ->get();

        // $suggested_entities = array();

        // foreach($entities as $entity) {
        //     if (trim($theperson->name)) { //PREVENT EMPTY SUGGESTIONS
        //         if (stripos($call->subject.$call->notes, $entity->name) !== false) {
        //             $suggested_entities[] = array("id" => $entity->id);
        //         }
        //     }
        // }

        // $call->suggested_entities = json_encode($suggested_entities);

        // $call->save();
    }

    public function store(Request $request)
    {
        $call = new Contact;
        $call->user_id = Auth::user()->id;
        $call->team_id = Auth::user()->team->id;
        $call->source = 'call_log';
        
        if (request('type-other')) {
            $call->type = request('type-other');
        } else {
            $call->type = request('type');
        }

        $timestr = str_replace(' ', '', request('time'));

        
        if ($timestr && request('date')) {
            $datetime = Carbon::parse(request('date').' '.$timestr);
        } elseif ($timestr) {
            $datetime = Carbon::parse(Carbon::now()->toDateString().' '.$timestr);
        } elseif (request('date')) {
            $datetime = Carbon::parse(request('date').' '.Carbon::now()->toTimeString());
        } else {
            $datetime = now();
        }

        try {

            $formatted_time = $datetime->format('Y-m-d H:i:s');

        } catch(\Exception $e) {

            //

        }

        $call->date = $formatted_time;

        $call->subject = request('subject');
        $call->notes = request('notes');
        if (request('followup')) {
            $call->followup = 1;
        }
        if (request('followup_on')) {
            $followup_on = request('followup_on');
            $call->followup_on = Carbon::parse($followup_on)->format('Y-m-d');
        }
        if (request('private')) {
            $call->private = 1;
        }

        $call->save();

        // Link people and entities selected from look up

        $entities = [];
        $people = [];

        foreach ($request->all() as $key => $value) {
            $input = explode('-', $key);   //EXAMPLE: "add-person-1234" / "add-person-MA_02BAR311"
            if (isset($input[0])) {
                $add_this = ($input[0] == 'add') ? true : null;
            }
            $type = (isset($input[1])) ? $input[1] : null;
            $add_id = (isset($input[2])) ? $input[2] : null;

            if ($add_this) {
                if ($type == 'person') {
                    $add_person = findPersonOrImportVoter($add_id, Auth::user()->team->id);
                    $add_id = $add_person->id;
                    $people[] = ['person_id' => $add_id, 'team_id' => Auth::user()->team->id];
                }
                if ($type == 'entity') {
                    $add_entity = Entity::find($entity_id);
                    $add_id = $add_entity->id;
                    $entities[] = ['entity_id' =>$add_id, 'team_id' => Auth::user()->team->id];
                }
            }
        }

        $call->people()->sync($people, ['team_id' => Auth::user()->team->id]);
        $call->entities()->sync($entities, ['team_id' => Auth::user()->team->id]);

        $this->scanForNames($call->id);
        $this->scanForEntities($call->id);
        
        if (request('save_as_new_case_serialize')) {

            // Use this because submit buttons not serialized by jQuery

            $case = new WorkCase;
            $case->subject  = $call->subject;
            $case->date     = Carbon::parse($call->date)->toDateString();
            $case->notes    = $call->notes;
            $case->team_id  = $call->team_id;
            $case->user_id  = $call->user_id;
            $case->save();

            $call->case_id = $case->id;
            $call->save();

            foreach ($call->people as $theperson) {
                $case->people()->attach($theperson, ['team_id' => $call->team_id, 'voter_id' => $theperson->voter_id]);
            }

        }

        $call_log = new CallLogViewModel(Auth::user());

        return view('shared-features.call-log.content', compact('call_log'));
    }

    public function edit(Request $request, $id)
    {
        // ADD LATER: Validation / Authorization
        $call = Contact::find($id);

        return view('shared-features.call-log.edit-modal', compact('call'));
    }

    public function connect(Request $request, $id)
    {
        // ADD LATER: Validation / Authorization
        $call = Contact::find($id);

        return view('shared-features.call-log.connect-modal', compact('call'));
    }

    public function update(Request $request, $id)
    {
        // ADD LATER: Validation / Authorization
        $call = Contact::find($id);

        $call->type = request('type');
        $call->subject = request('subject');

        if (request('time') && request('date')) {
            $datetime = Carbon::parse(request('date').' '.request('time'));
        } elseif (request('time')) {
            $datetime = Carbon::parse(Carbon::now()->toDateString().' '.request('time'));
        } elseif (request('date')) {
            $datetime = Carbon::parse(request('date').' '.Carbon::now()->toTimeString());
        } else {
            $datetime = now();
        }

        try {

            $formatted_time = $datetime->format('Y-m-d H:i:s');

        } catch(\Exception $e) {

            //

        }
        
        $call->date = $formatted_time;
        $call->notes = request('notes');
        $call->followup = (request('followup')) ? true : false;
        $call->private = (request('private')) ? true : false;

        $call->save();

        $this->scanForNames($call->id);
        $this->scanForEntities($call->id);

        $call_log = new CallLogViewModel(Auth::user());

        return view('shared-features.call-log.content', compact('call_log'));
    }

    public function updateConnections(Request $request, $id)
    {
        // ADD LATER: Validation / Authorization

        $call = Contact::find($id);

        $people = request('people');
        $people = collect($people)->unique();

        $call->people()->sync([]);

        foreach ($people as $person_id) {
            $add_person = findPersonOrImportVoter($person_id, Auth::user()->team->id);
            $call->people()->attach($add_person, ['team_id' => Auth::user()->team->id]);
        }

        $entities = request('entities');
        $entities = collect($entities)->unique();
        $call->entities()->sync([]);
        foreach ($entities as $entity_id) {
            $add_entity = Entity::find($entity_id);
            $call->entities()->attach($add_entity, ['team_id' => Auth::user()->team->id]);
        }

        $this->ScanForNames($call->id);

        $call_log = new CallLogViewModel(Auth::user());

        return view('shared-features.call-log.content', compact('call_log'));
    }

    public function delete(Request $request, $id)
    {
        $call = Contact::find($id);

        $this->authorize('basic', $call);

        $call->delete();

        $call_log = new CallLogViewModel(Auth::user());

        return view('shared-features.call-log.content', compact('call_log'));
    }
}
