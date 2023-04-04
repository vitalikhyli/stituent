<?php

namespace App\Http\Controllers;

use App\CaseFile;
use App\CasePerson;
use App\Contact;
use App\ContactPerson;
use App\Group;
use App\GroupPerson;
use App\Http\Controllers\Controller;
use App\Notifications\AssignUserToCase;
use App\Person;
use App\Traits\ConstituentQueryTrait;
use App\Traits\ExportTrait;
use App\User;
use App\WorkCase;
use App\WorkFile;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class CasesController extends Controller
{
    use ConstituentQueryTrait;
    use ExportTrait;

    public function print($app_type, $id)
    {
        $thecase = WorkCase::find($id);

        $this->authorize('basic', $thecase);

        $contacts = Contact::TeamOrPrivateAndMine()->where('case_id', $thecase->id)->orderBy('created_at', 'desc')->get();

        return view('shared-features.cases.show-print', compact('thecase', 'contacts'));
    }

    public function export($app_type, $scope, $status)
    {

        $cases = $this->getCases($scope, $status, $user_id = null, 'NO_PAGINATE')->get();

        $cases = $cases->each(function ($item) {
            $people = [];
            foreach($item->people as $person) {
                $people[] = $person->full_name
                            .' ('
                            .$person->address_city
                            .')';
            }
            $item['constituents'] = (!empty($people)) ? implode(', ', $people) : null;
        });

        $cases = $cases->map(function ($item) {
            return collect($item->toArray())
                ->only(['date', 'priority', 'subject', 'notes', 'resolved_date', 'closing_remarks', 'status', 'type', 'constituents'])
                ->all();
        });

        return $this->createCSV($cases);
    }

    public function closingRemarks(Request $request, $app_type, $case_id)
    {
        $case = WorkCase::find($case_id);
        $this->authorize('basic', $case);

        $case->closing_remarks = request('closing_remarks');
        if (! $case->closing_remarks) {
            $case->status = 'open';
        }
        $case->save();

        return redirect('/'.Auth::user()->team->app_type.'/cases/'.$case_id);
    }

    public function new($app_type, $person_id = null)
    {
        return view('shared-features.cases.new', compact('person_id'));
    }

    public function save(Request $request, $app_type)
    {
        if (request('contact_id')) {

         // CONVERTING A CONTACT TO A CASE

            $contact_id = request('contact_id');
            $contact = Contact::find($contact_id);

            $this->authorize('basic', $contact);

            $case = new WorkCase;
            $case->subject = request('subject');
            $case->date = Carbon::parse($contact->date)->toDateString();
            $case->notes = request('notes'); //$contact->notes;
            $case->team_id = $contact->team_id;
            $case->user_id = $contact->user_id;
            $case->save();

            $contact->case_id = $case->id;
            $contact->save();

            foreach ($contact->people as $theperson) {
                $case->people()->attach($theperson, ['team_id' => $contact->team_id, 'voter_id' => $theperson->voter_id]);
            }
        } elseif (request('person_id')) {

         //  STRAIGHT UP NEW CASE

            $person_id = request('person_id');

            // $theperson = Person::find($person_id);
            $theperson = findPersonOrImportVoter(request('person_id'), Auth::user()->team->id);
            $this->authorize('basic', $theperson);

            $case = new WorkCase;
            $case->subject = request('subject');
            $case->date = Carbon::now();
            $case->team_id = Auth::user()->team->id;
            $case->user_id = Auth::user()->id;
            $case->save();

            $case->people()->attach($theperson, ['team_id' => Auth::user()->team->id, 'voter_id' => $theperson->voter_id]);
        } else {

        // Not created in connection to a person or contact

            $case = new WorkCase;
            $case->subject = request('subject');
            $case->date = Carbon::now();
            $case->team_id = Auth::user()->team->id;
            $case->user_id = Auth::user()->id;
            $case->save();
        }

        return redirect(Auth::user()->team->app_type.'/cases/'.$case->id.'/edit');
    }

    public function searchPeople($app_type, $case_id, $v = null)
    {
        $v = trim($v);
        $mode_all = 1;
        $search_value = $v;

        if ($v == null || strlen($v) <= 1) {
            return null;
        } elseif (strlen($v) > 1) {
            $people = $this->getPeopleFromName($v);
        }

        //Remove people already selected
        $attached_people = DB::table('case_person')
                             ->where('case_id', $case_id)
                             ->get()
                             ->pluck('person_id')
                             ->toArray();

        $people = $people->whereNotIn('id', $attached_people);

        return view('shared-features.cases.list-people', compact('people',
                                                        'mode_all',
                                                        'search_value'));
    }

    public function syncPeople(Request $request, $app_type, $id)
    {
        $case = WorkCase::find($id);

        $this->authorize('basic', $case);

        $people = request('linked');
        $people = collect($people)->unique();

        $case->people()->sync($people);

        return redirect(Auth::user()->team->app_type.'/cases/'.$id);
    }

    public function syncHouseholds(Request $request, $app_type, $id)
    {
        $case = WorkCase::find($id);

        $this->authorize('basic', $case);

        $households = request('linked_hh');

        $households = collect($households)->unique();

        $case->households()->sync($households);

        return redirect(Auth::user()->team->app_type.'/cases/'.$id);
    }

    public function linkHousehold($app_type, $case_id, $household_id)
    {
        $thecase = WorkCase::find($case_id);

        // should just check to see if in case-household table, add if not
    }

    public function linkPerson($app_type, $case_id, $person_id)
    {
        $thecase = WorkCase::find($case_id);
        $theperson = findPersonOrImportVoter($person_id, $thecase->team_id);

        $this->authorize('basic', $thecase);
        $this->authorize('basic', $theperson);

        if (! $thecase->people->contains($theperson->id)) {
            $thecase->people()->attach($theperson->id, ['team_id' => $thecase->team_id,
                'voter_id' => $theperson->voter_id]);
            $thecase->save();

            return '
            <div>
                <label for="linked_'.$theperson->id.'"><input type="checkbox" value="'.$theperson->id.'" checked name="linked[]" id="linked_'.$theperson->id.'" />
                <span class="ml-2">'.$theperson->full_name.'</span></label>
            </div>
            ';
        }
    }

    public function editContact(Request $request, $app_type, $case_id, $contact_id)
    {
        $thecase = WorkCase::find($case_id);
        $thecontact = Contact::find($contact_id);

        $this->authorize('basic', $thecase);
        $this->authorize('basic', $thecontact);

        $form_action = '/cases/'.$thecase->id.'/contacts/'.$thecontact->id;

        return view('shared-features.contacts.edit', compact('thecontact', 'form_action'));
    }

    public function scopeResolved($app_type, $scope = null, $status = null)
    {
        $cases = $this->getCases($scope, $status = 'resolved');
        $cases_count = $cases->count();

        return view('shared-features.cases.index', compact('cases', 'cases_count', 'status'));
    }

    public function delete($app_type, $case_id)
    {
        $case = WorkCase::find($case_id);

        /*
        $people_linked_to_case = $case->people()->pluck('people.id')->toArray();

        $file_pivots = CaseFile::where('case_id', $case_id)->get();
        foreach ($file_pivots as $pivot) {
            $pivot->delete();
        }

        $contacts = Contact::where('case_id', $case_id)->get();
        foreach ($contacts as $case_contact) {

        //Preserve all People Connected to Contact
            $people_linked_to_contact = $case_contact->people->pluck('people.id')->toArray();
            $people_linked_to_contact = array_merge($people_linked_to_case);

            $sync = [];
            foreach ($people_linked_to_contact as $linked_id) {
                $sync[$linked_id] = ['team_id' => Auth::user()->team->id,
                                     'voter_id' => $];
            }

            $case_contact->people()->sync($sync);

            //Erase Case Link
            $case_contact->case_id = null;

            //Save Each Contact
            $case_contact->save();
        }
        */
        $case->delete();

        return redirect('/'.$app_type.'/cases/');
    }

    public function edit($app_type, $case_id)
    {
        $thecase = WorkCase::find($case_id);

        $this->authorize('basic', $thecase);

        $contacts = Contact::TeamOrPrivateAndMine()->where('case_id', $thecase->id)->orderBy('created_at', 'desc')->get();

        $available_types = WorkCase::StaffOrPrivateAndMine()
                                   ->select('type')
                                   ->where('team_id',Auth::user()->team->id)

                                   ->whereNotNull('type')
                                   ->groupBy('type')
                                   ->orderBy('type')
                                   ->get()
                                   ->pluck('type');

        $available_subtypes = WorkCase::StaffOrPrivateAndMine()
                                   ->select('subtype')
                                   ->where('team_id',Auth::user()->team->id)

                                   ->whereNotNull('subtype')
                                   ->groupBy('subtype')
                                   ->orderBy('subtype')
                                   ->get()
                                   ->pluck('subtype');

        return view('shared-features.cases.edit', compact('thecase', 'contacts', 'available_types', 'available_subtypes'));
    }

    public function update(Request $request, $app_type, $case_id, $close = null)
    {
        $validator = Validator::make($request->all(), [
                'date' => ['required', 'date'],
                // 'subject' => ['required', 'max:255'],
                // 'notes' => ['required', 'max:255'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        } else {
            $case = WorkCase::find($case_id);

            $this->authorize('basic', $case);

            $case->subject = request('subject');
            $case->date = Carbon::parse(request('date'))->toDateString();
            $case->notes = request('notes');

            if (trim(request('type_new'))) {
                $case->type = request('type_new');
            } else {
                $case->type = request('type');
            }
            if (trim(request('subtype_new'))) {
                $case->subtype = request('subtype_new');
            } else {
                $case->subtype = request('subtype');
            }

            // dd(request('priority'));

            if (request('priority') == 'high') {
                $priority = 'High';
            }
            if (request('priority') == 'medium') {
                $priority = 'Medium';
            }
            if (request('priority') == 'low') {
                $priority = 'Low';
            }
            $case->priority = (isset($priority)) ? $priority : null;

            $private = request('private');
            $case->private = (isset($private)) ? $private : 0;
            if (request('user_id')) {
                $case->user_id = request('user_id');
            } else {
                $case->user_id = Auth::user()->id;
            }

            $case->save();

            if ($close) {
                return redirect(Auth::user()->team->app_type.'/cases/'.$case_id);
            } else {
                return redirect(Auth::user()->team->app_type.'/cases/'.$case_id.'/edit');
            }
        }
    }

    // public function undo($app_type, $case_id, $previous_url)
    // {
    //   $case = WorkCase::find($case_id);

    //   $this->authorize('basic', $case);

    //   foreach ($case->people as $theperson) {
    //     $case->people()->detach($theperson);
    //   }
    //   $contacts = Contact::where('case_id', $case->id)->get();
    //   foreach ($contacts as $thecontact) {
    //     $thecontact->case_id = 0;
    //     $thecontact->save();
    //   }
    //   $case->delete();

    //   $previous_url = base64_decode($previous_url);
    //   return redirect($previous_url);
    // }

    public function assignUser($app_type, $case_id, $user_id)
    {
        $case = WorkCase::find($case_id);
        $this->authorize('basic', $case);
        $case->user_id = $user_id;
        $case->save();
        session()->flash('assignment', true);

        return redirect(Auth::user()->team->app_type.'/cases/'.$case_id);
    }

    public function notifyUser($app_type, $case_id, $user_id)
    {
        $case = WorkCase::find($case_id);
        $this->authorize('basic', $case);
        $theuser = User::find($user_id);

        try {
            $theuser->notify(new AssignUserToCase(Auth::user(), $theuser, $case));
        } catch (\Exception $e) {
            session()->flash('assignment_error', true);

            return redirect(Auth::user()->team->app_type.'/cases/'.$case_id);
        }

        session()->flash('assignment_success', true);

        return redirect(Auth::user()->team->app_type.'/cases/'.$case_id);
    }

    public function status($app_type, $case_id, $status)
    {
        $case = Workcase::find($case_id);

        $this->authorize('basic', $case);

        if (in_array($status, ['open', 'held', 'resolved'])) {
            $case->status = $status;
            $case->save();
        }

        session()->flash('msg', 'Marked as '.$status);

        return redirect(Auth::user()->team->app_type.'/cases/'.$case_id);
    }

    public function addContact(Request $request, $app_type, $case_id)
    {        

        // Update Contact->people()
        //dd(request()->input());

        // foreach(request()->input() as $field => $value) {
        //     if (substr($field, 0, 14) == 'contact_person') {

        //         $json = json_decode(base64_decode(request($field)), false);

        //         if (request('checked_'.$field) == null) {
                
        //             $person = Person::find($json->person_id);
        //             $this->authorize('basic', $person);

        //             $contact = Contact::find($json->contact_id);
        //             $this->authorize('basic', $contact);

        //             $contact->people()->detach($person->id);
        //         }
        //     }
        // }

        // VALIDATE
        $validator = Validator::make($request->all(), [
                'date'      => ['required', 'date'],
                'subject'   => 'required_without:notes',
                'notes'     => 'required_without:subject',
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        } else {

            if(request('notes') || request('subject')) {

                $thecase = WorkCase::find($case_id);
                $this->authorize('basic', $thecase);

                $contact = new Contact;
                $contact->case_id = $case_id;
                $contact->team_id = Auth::user()->team_id;
                $contact->user_id = Auth::user()->id;
                $contact->private = (request('private')) ? true : false;
                $contact->type = (request('type')) ? request('type') : null;

                if (request('use_time')) {
                   
                    try {
                    $contact->date = Carbon::parse(request('date').' '.request('time'))
                                           ->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $contact->date = Carbon::parse(request('date').' '.date('H:i:s'));
                    }
                     $datetime = $contact->date;
                } else {
                    $contact->date = Carbon::parse(request('date').' '.Carbon::now()
                                           ->format('H:i:s'))->format('Y-m-d H:i:s');

                }

                if (request('person_followup')) {
                    $contact->followup = 1;

                    if (request('person_followup_on')) {
                        $contact->followup_on = Carbon::parse(request('person_followup_on'))->format('Y-m-d');
                    }
                }
                $contact->subject = request('subject');
                $contact->notes = request('notes');

                $contact->save();

                //Pivot: ContactPerson
                if (request('include_people')) {
                    $people = Person::whereIn('id', request('include_people'))->get();
                    foreach ($people as $theperson) {
                        $contact->people()->attach($theperson, ['team_id' => $contact->team_id, 'voter_id' => $theperson->voter_id]);
                    }
                }
                
                //Mark Case Itself as Updated
                $thecase->touch();

            }

            return redirect(Auth::user()->team->app_type.'/cases/'.$case_id);
        }
    }

    public function index($app_type, $scope = null, $status = null, $user_id = null)
    {

        return view('shared-features.cases.index');
        
        // if (isset($_GET['livewire'])) {
        //     return view('shared-features.cases.index');
        // }
        
        // $cases = $this->getCases($scope, $status, $user_id = $user_id);

        // $cases_unpaginated = $this->getCases($scope, $status, $user_id = $user_id, $no_paginate = true);

        // $cases_count = $cases_unpaginated->count();
        // $cases_unpaginated = $cases_unpaginated->get();

        // $case_breadcrumb = ucwords($scope);

        // $case_breadcrumb = ($case_breadcrumb == 'Mine') ? 'My' : $case_breadcrumb;
        // if ($status) {
        //     $case_breadcrumb .= ' '.ucwords($status);
        // }
        // $case_breadcrumb .= ' Cases';

        // if (! $status) {
        //     $status = 0;
        // }
        // if (! $scope) {
        //     $scope = 0;
        // }
        // if (! $user_id) {
        //     $user_id = 0;
        // }

        // return view('shared-features.cases.index-old', compact('cases', 'cases_count', 'scope', 'case_breadcrumb', 'status', 'cases_unpaginated', 'user_id'));
    }

    public function show($app_type, $id)
    {
        $thecase = WorkCase::find($id);

        $this->authorize('basic', $thecase);

        $contacts = Contact::TeamOrPrivateAndMine()
                           ->where('case_id', $thecase->id)
                           ->orderBy('date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->get();

                           //dd($contacts);

        return view('shared-features.cases.show', compact('thecase', 'contacts'));
    }

    public function search($app_type, $scope = null, $status = null, $user_id = null, $v = null)
    {
        $files = null;
        $v = trim($v);

        $cases_unpaginated = $this->getCases($scope, $status, $user_id = null, $no_paginate = true);
        $cases_count = $cases_unpaginated->count();
        $cases_unpaginated = $cases_unpaginated->get();

        if (($v == null) || ($v == '') || (strlen($v) < 2)) {
            $cases = $this->getCases($scope, $status);
        } else {
            $cases = WorkCase::where('team_id', Auth::user()->team->id);

            if ($scope == 'mine') {
                $cases = $cases->where('user_id', Auth::user()->id);
            }
            if ($status == 'resolved') {
                $cases = $cases->where('status', 'resolved');
            }
            if ($status == 'held') {
                $cases = $cases->where('status', 'held');
            }
            if ($status == 'open') {
                $cases = $cases->where('status', 'open');
            }

            $type_string = null;
            if (strpos($v, 'type:') !== false) {
                $split_v = explode('type:', $v);
                $type_string = trim($split_v[1]);
                $v = trim($split_v[0]);
            }
            $group_string = null;
            $person_string = null;
            if (strpos($v, 'for:') !== false) {
                $split_v = explode('for:', $v);
                $person_string = trim($split_v[1]);
                $v = trim($split_v[0]);
            } elseif (strpos($v, 'group:') !== false) {
                $split_v = explode('group:', $v);
                $group_string = trim($split_v[1]);
                $v = trim($split_v[0]);
            }

            if ($v != null) {
                $cases = $cases->where(function ($q) use ($v) {
                    $q->orWhere('subject', 'like', '%'.$v.'%');
                    $q->orWhere('notes', 'like', '%'.$v.'%');
                    $q->orWhereHas('people', function ($query) use ($v) {
                        $query->where('full_name', 'like', '%'.$v.'%');
                    });
                    $q->orWhereHas('contacts', function ($query) use ($v) {
                        $query->TeamOrPrivateAndMine();
                        $query->where('subject', 'like', '%'.$v.'%');
                        $query->orWhere('notes', 'like', '%'.$v.'%');
                    });
                });
            }


          $cases = WorkCase::StaffOrPrivateAndMine()->where('team_id', Auth::user()->team->id);

          if ($scope == 'mine')           $cases = $cases->where('user_id', Auth::user()->id);
          if ($status == 'resolved')      $cases = $cases->where('status', 'resolved');
          if ($status == 'held')          $cases = $cases->where('status', 'held');
          if ($status == 'open')          $cases = $cases->where('status', 'open');  

          $type_string = null;
          if (strpos($v, 'type:') !== false) {
            $split_v      = explode('type:', $v);
            $type_string  = trim($split_v[1]);
            $v            = trim($split_v[0]);
          }
          $group_string = null;
          $person_string = null;
          if (strpos($v, 'for:') !== false) {

            $split_v        = explode('for:', $v);
            $person_string  = trim($split_v[1]);
            $v              = trim($split_v[0]);

          } elseif (strpos($v, 'group:') !== false) {

            $split_v        = explode('group:', $v);
            $group_string  = trim($split_v[1]);
            $v              = trim($split_v[0]);
          }

          if ($v != null) { 
               $cases = $cases->where(function($q) use ($v){
                            $q->orWhere('subject','like','%'.$v.'%');
                            $q->orWhere('notes','like','%'.$v.'%');
                            $q->orWhereHas('people', function($query) use($v) {
                                  $query->where('full_name', 'like', '%'.$v.'%');
                              });
                            $q->orWhereHas('contacts', function($query) use($v) {
                                  $query->TeamOrPrivateAndMine();
                                  $query->where('subject', 'like', '%'.$v.'%');
                                  $query->orWhere('notes', 'like', '%'.$v.'%');
                            });
                          });

          }

          if ($person_string) {
            $cases = $cases->whereHas('people', function($query) use($person_string) {
                                  $query->where('full_name', 'like', '%'.$person_string.'%');
                              });
          }

          if ($group_string) {

            $group = Group::where('team_id', Auth::user()->team->id)
                          ->where('name', $group_string)
                          ->first();

                $group_array = GroupPerson::where('team_id', Auth::user()->team->id)
                                      ->where('group_id', $group->id)
                                      ->get()
                                      ->pluck('person_id')
                                      ->toArray();

                $cases = $cases->whereHas('people', function ($query) use ($group_array) {
                    $query->whereIn('people.id', $group_array);
                });
            }

            if ($type_string) {
                $cases = $cases->where('type', $type_string);
            }

            $cases = $cases->orderBy('date', 'desc')->get();
        }

        return view('shared-features.cases.list-cases', compact('cases',
                                                              'v',
                                                              'cases_count',
                                                              'cases_unpaginated',
                                                              'scope',
                                                              'status',
                                                              'user_id'));
    }

    public function getCases($scope = null, $status = null, $user_id = null, $no_paginate = null)
    {
        $cases = WorkCase::StaffOrPrivateAndMine()->where('team_id', Auth::user()->team->id);

        // orWhereIn('id', Auth::user()->sharedCases->get())

        if ($scope == 'mine') {
            $cases = $cases->where('user_id', Auth::user()->id);
        }
        if ($status == 'resolved') {
            $cases = $cases->where('status', 'resolved');
        }
        if ($status == 'held') {
            $cases = $cases->where('status', 'held');
        }
        if ($status == 'open') {
            $cases = $cases->where('status', 'open');
        }
        if (! $no_paginate) {
            if ($user_id && $user_id != 0) {
                $cases = $cases->where('user_id', $user_id);
            }
        }
        if (isset($_GET['type']) && ($_GET['type'])) {
            $cases = $cases->where('type', $_GET['type']);
        }

        $cases = $cases->TeamOrPrivateAndMine();

        $cases = $cases->orderBy('date', 'desc');

        // dd($cases->get());
        // if ($no_paginate) $cases = $cases->get();

        if (! $no_paginate) {
            $cases = $cases->paginate(20);
        }

        return $cases;
    }
}
