<?php
/* EDITING OUT UNNECESSSARY CONTROLLER

namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\WorkCase;
use App\Contact;
use App\User;
use App\WorkFile;
use App\ContactPerson;
use App\CasePerson;
use App\Household;
use App\VotingHousehold;

use Illuminate\Support\Facades\DB;

use Validator;

use Auth;

class CasesController extends Controller
{

    public function syncPeople(Request $request, $id)
    {
        $case = WorkCase::find($id);

        $this->authorize('basic', $case);

        $people = request('linked');
        $people = collect($people)->unique();

        $case->people()->sync($people);

        return redirect('u/cases/'.$id);
    }


    public function syncHouseholds(Request $request, $id)
    {

        $case = WorkCase::find($id);

        $this->authorize('basic', $case);

        $households = request('linked_hh');

        $households = collect($households)->unique();

        $case->households()->sync($households);

        return redirect('u/cases/'.$id);
    }


    public function linkHousehold($case_id, $household_id)
    {

        $thecase = WorkCase::find($case_id);

        $thehousehold = findHouseholdOrImportVotingHousehold($household_id, Auth::user()->team->id);

        $this->authorize('basic', $thecase);
        // $this->authorize('basic', $thehousehold); // NOT WORKING????

        if(!$thecase->households->contains($thehousehold->id)) {

            $thecase->households()->attach($thehousehold->id, ['team_id' => $thecase->team_id]);
            $thecase->save();

            return '
            <div>
                <label for="linked_'.$thehousehold->id.'"><input type="checkbox" value="'.$thehousehold->id.'" checked name="linked_hh[]" id="linked_'.$thehousehold->id .'" />
                <span class="ml-2">'.$thehousehold->full_address.'</span></label>
            </div>
            ';
        }

    }

    public function linkPerson($case_id, $person_id)
    {
        $thecase = WorkCase::find($case_id);
        $theperson = findPersonOrImportVoter($person_id, $thecase->team_id);

        $this->authorize('basic', $thecase);
        $this->authorize('basic', $theperson);

        if(!$thecase->people->contains($theperson->id)) {

            $thecase->people()->attach($theperson->id, ['team_id' => $thecase->team_id]);
            $thecase->save();

            return '
            <div>
                <label for="linked_'.$theperson->id.'"><input type="checkbox" value="'.$theperson->id.'" checked name="linked[]" id="linked_'.$theperson->id .'" />
                <span class="ml-2">'.$theperson->full_name.'</span></label>
            </div>
            ';
        }

    }

    public function editContact(Request $request, $case_id, $contact_id)
    {
        $thecase = WorkCase::find($case_id);
        $thecontact = Contact::find($contact_id);

        $this->authorize('basic', $thecase);
        $this->authorize('basic', $thecontact);

        $form_action = '/cases/'.$thecase->id.'/contacts/'.$thecontact->id;

        return view('u.contacts.edit', compact('thecontact','form_action'));
    }

    public function scopeResolved($scope = null, $resolved = null)
    {
        $cases = $this->getCases($scope, $resolved);

        $cases_count = $cases->count();

        $scope_resolved = "";
        if ($scope == 'mine') {
            $scope_resolved .= "My ";
        }
        if ($resolved == 'open') {
            $scope_resolved .= "Open ";
        }
        if ($resolved == 'resolved') {
            $scope_resolved .= "Resolved ";
        }
        $scope_resolved .= "Cases";

        return view('u.cases.index', compact('cases', 'cases_count', 'scope_resolved'));
    }


    public function edit($case_id) {

        $thecase = WorkCase::find($case_id);

        $this->authorize('basic', $thecase);

        $contacts = Contact::where('case_id',$thecase->id)->orderBy('created_at','desc')->get();

        $available_types = WorkCase::select('type')
                                   ->where('team_id',Auth::user()->team->id)
                                   ->whereNotNull('type')
                                   ->groupBy('type')
                                   ->orderBy('type')
                                   ->get()
                                   ->pluck('type');

        return view('u.cases.edit', compact('thecase','contacts','available_types'));
    }

    public function save(Request $request, $case_id, $close = null)
    {
        $validator = Validator::make($request->all(), [
                'date' => ['required', 'date'],
                'subject' => ['required', 'max:255'],
                'notes' => ['required', 'max:255'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        } else {

            $case = WorkCase::find($case_id);

            $this->authorize('basic', $case);

            $case->subject = request('subject');
            $case->date = request('date');
            $case->notes = request('notes');
            if (trim(request('type_new'))) {
                $case->type = request('type_new');
            } else {
                $case->type = request('type');
            }

            $case->save();

            if ($close) {
              return redirect('u/cases/'.$case_id);
            } else {
              return redirect('u/cases_edit/'.$case_id);
            }
        }
    }

    public function undo($case_id, $previous_url)
    {
      $case = WorkCase::find($case_id);

      $this->authorize('basic', $case);

      foreach ($case->people as $theperson) {
        $case->people()->detach($theperson);
      }
      $contacts = Contact::where('case_id', $case->id)->get();
      foreach ($contacts as $thecontact) {
        $thecontact->case_id = 0;
        $thecontact->save();
      }
      $case->delete();

      $previous_url = base64_decode($previous_url);
      return redirect($previous_url);
    }

    public function assignUser($case_id, $user_id)
    {
        $case = WorkCase::where('id', $case_id)->first();

        $this->authorize('basic', $case);

        $case->user_id = $user_id;
        $case->save();
        $theuser = User::where('id',$user_id)->first();
        session()->flash('msg', 'Now Assigned to '.$theuser->name);

        return redirect('u/cases/'.$case_id);
    }

    public function resolved($case_id, $tof)
    {
        $case = Workcase::find($case_id);

        $this->authorize('basic', $case);

        $case->resolved = $tof;
        $case->save();
        ($tof) ? $status = 'Resolved' : $status = 'Open';
        session()->flash('msg', 'Marked as '.$status);

        return redirect('u/cases/'.$case_id);
    }

    public function addContact(Request $request, $case_id) {
        // VALIDATE
        $validator = Validator::make($request->all(), [
                'date' => ['required', 'date'],
                // 'subject' => ['required', 'max:255'],
                'notes' => ['required', 'max:255'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        } else {

          $thecase = WorkCase::find($case_id);

          $this->authorize('basic', $thecase);

          $contact = new Contact;
          $contact->case_id = $case_id;
          $contact->team_id = $thecase->team_id;
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

          $contact->subject = request('subject');
          $contact->notes = request('notes');
          $contact->save();
          //Pivot: ContactPerson
          $people = $request->input('include_people');
          if ($people) {
            foreach ($people as $theperson) {
              $contact->people()->attach($theperson, ['team_id' => $contact->team_id]);
            }
          }
          //Mark Case Itself as Updated
          $thecase->touch();

          return redirect('u/cases/'.$case_id);
        }
    }

    public function index($scope, $resolved)
    {
        $cases = $this->getCases($scope, $resolved);

        $cases_count = $cases->count();

        return view('u.cases.index', compact('cases', 'cases_count'));
    }

    public function show($id)
    {
        $thecase = WorkCase::find($id);

        $this->authorize('basic', $thecase);

        $contacts = Contact::where('case_id',$thecase->id)->orderBy('created_at','desc')->get();
        $team_users = User::where('current_team_id',$thecase->team_id)
                          ->where('id','<>',$thecase->user_id)
                          ->get();

        $commonlyContacted = Contact::select('contact_person.person_id', 'people.full_name', DB::raw('count(distinct contact_person.person_id) as person_count'), DB::raw('0 as checked'), DB::raw('any_value(contacts.date) as thedate'))
                              ->where('case_id',$thecase->id)
                              ->whereNotIn('people.id', CasePerson::select('person_id')->where('case_id',$id))
                              ->join('contact_person', 'contact_person.contact_id', 'contacts.id')
                              ->join('people', 'contact_person.person_id','people.id')
                              ->groupBy('contact_person.person_id')
                              ->orderBy('person_count', 'desc')
                              ->orderBy('thedate', 'desc')
                              ->limit(3);

        $commonlyContacted = CasePerson::select('people.id', 'people.full_name', DB::raw('0 as person_count'), DB::raw('1 as checked'), DB::raw('null as thedate'))
                                       ->where('case_id',$id)
                                       ->join('people', 'case_person.person_id', 'people.id')
                                       ->union($commonlyContacted)
                                       ->get();


        return view('u.cases.show', compact('thecase',
                                                        'contacts',
                                                        'commonlyContacted',
                                                        'team_users'));
    }

    public function search($scope, $resolved, $v)
    {
        $files  = null;
        $v = trim($v);


        if (($v == null) || ($v == '') || (strlen($v) < 2)) {

          $cases = $this->getCases($scope, $resolved);

        } else {

          $cases = WorkCase::where('team_id', Auth::user()->team->id);

          if ($scope == 'mine')         { $cases = $cases->where('user_id',Auth::user()->id); }
          if ($resolved == 'resolved')  { $cases = $cases->where('resolved',1);               }
          if ($resolved == 'open')      { $cases = $cases->where('resolved',0);               }

          if ($v != null) {
               $cases = $cases->where(function($q) use ($v){
                            $q->orWhere('subject','like','%'.$v.'%');
                            $q->orWhere('notes','like','%'.$v.'%');
                            $q->orWhereHas('people', function($query) use($v) {
                                  $query->where('full_name', 'like', '%'.$v.'%');
                                  // $query->orWhere('first', 'like', '%'.$v.'%');
                                  // $query->orWhere('last', 'like', '%'.$v.'%');
                              });
                            $q->orWhereHas('contacts', function($query) use($v) {
                                  $query->where('subject', 'like', '%'.$v.'%');
                                  $query->orWhere('notes', 'like', '%'.$v.'%');
                            });
                          });
            $files = WorkFile::where('team_id', Auth::user()->team->id);
            $files = $files->where('name', 'like','%'.$v.'%')
                           ->orWhere('type', 'like','%'.$v.'%');
            $files = $files->orderBy('name')->get();
          }

          $cases_count = $cases->count();

          $cases = $cases->orderBy('date','desc')->get();

        }

        return view('u.cases.list-cases', compact('cases',
                                                              'files',
                                                              'v',
                                                              'cases_count'));
    }

    public function getCases($scope, $resolved)
    {
        $cases = WorkCase::where('team_id', Auth::user()->team->id);

        if ($scope == 'mine')         { $cases = $cases->where('user_id',Auth::user()->id); }
        if ($resolved == 'resolved')  { $cases = $cases->where('resolved',1);               }
        if ($resolved == 'open')      { $cases = $cases->where('resolved',0);               }

        $cases = $cases->orderBy('date','desc')
                       ->get();

        return $cases;
    }



}
