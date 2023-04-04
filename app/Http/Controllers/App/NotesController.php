<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contact;
use App\ContactPerson;
use App\ContactEntity;
use App\WorkCase;
use Auth;
use Carbon\Carbon;

class NotesController extends Controller
{
    use LinksTrait; 

    public function index()
    {
    	$recent_contacts = Auth::user()->contacts()
                                      ->orderBy('date', 'desc')
                                      ->paginate(100);

        $grouped = $recent_contacts->groupBy('month_readable');

        $sections = [];

        $sections[] = [
            'title'    => '',
            'subtitle' => 'Highlighted notes require followup.',
            'rows'     => [],
        ];

        foreach ($grouped as $month_readable => $notes) {

        	$rows = $this->getNotesRows($notes, 'date_readable');

	    	$sections[] = [
	    		'title' => $month_readable,
	    		'subtitle' => '',
	    		'rows' => $rows,
        	];
	    			
        }

        $next = '';
        $title = 'All Notes';
        if ($recent_contacts->currentPage() != $recent_contacts->lastPage()) {
        	$next = 'app/notes?page='.($recent_contacts->currentPage() + 1);
        }
        if ($recent_contacts->currentPage() > 1) {
        	$title = 'All Notes (cont.)';
        }

    	$data = [
    		'header'   => 'Notes',
    		'next_url' => $next,
    		'sections' => $sections,
    	];

    	return json_encode($data);
    }

    public function edit($id)
    {
        $note = Contact::with(['people', 'entities', 'case'])->find($id);
        return $note;
    }

    public function show($id)
    {
    	$note = Contact::find($id);

        $cases = [];
    	if ($note->case) {
    		$cases = $this->getCasesLinks([$note->case]);
	    }
    	$people = $this->getPersonLinks($note->people);
    	$organizations = $this->getOrganizationsLinks($note->entities);

    	$same_person = [];
    	foreach ($note->people as $person) {
            $same_person_notes = $person->contactsAuth()
                                           ->whereNotIn('id', [$note->id])
                                           ->get();
    		$same_person = $this->getNotesLinks($same_person_notes);
    	}
    	$same_day = [];
    	$same_day_contacts = Auth::user()
    							 ->contacts()
    							 ->where('date', $note->date)
                                 ->whereNotIn('id', [$note->id])
    							 ->get();
    	
    	$same_day = $this->getNotesLinks($same_day_contacts);

    	$data = [
            'header' => ''.$note->name,
            'sections' => [
                0 => [
                    'title'    => ''.$note->name,
                    'subtitle' => ''.$note->date->diffForHumans(),
                    'rows'     => [
                    	0 => ['title' => 'People',
                    		  'links' => $people,
                    		  'text'  => ''],
                    	
                    	1 => ['title' => 'Cases',
                    		  'links' => $cases,
                    		  'text'  => ''],
                    	
                        2 => ['title' => 'Orgs',
                    		  'links' => $organizations,
                    		  'text'  => ''],
                    	3 => ['title' => 'Type',
                    		  'links' => [],
                    		  'text'  => ''.$note->type],
                    	4 => ['title' => 'Date',
                    		  'links' => [],
                    		  'text'  => ''.$note->date->format('n/j/Y')],
                    	5 => ['title' => 'Followup',
                    		  'links' => [],
                    		  'text'  => ''.$note->followup_text],
                    	6 => ['title' => 'Notes',
                    		  'links' => [],
                    		  'text'  => ''.$note->notes],
                    ],
                ],
                1 => [
                    'title'    => 'Related Notes',
                    'subtitle' => '',
                    'rows'     => [
                        0 => ['title' => 'Same person',
                    		  'links' => $same_person,
                    		  'text'  => ''],
                    	1 => ['title' => 'Same day',
                    		  'links' => $same_day,
                    		  'text'  => ''],
                    ]
                ],
            ],
        ];
        return json_encode($data);
    }

    public function types()
    {
        $types = [];

        foreach (Auth::user()->contactTypes() as $type) {
            $types[] = ucwords($type);
        }
        return json_encode($types);
    }

    public function update($id)
    {
        $note = Contact::find($id);
        return $this->updateNote($note);
    }

    public function store()
    {
        
        $note = new Contact;
        $note->user_id = Auth::user()->id;
        $note->team_id = Auth::user()->team->id;
        $note->source = 'app';

        return $this->updateNote($note);
    }

    public function updateNote($note) {
        // $data = [
        //     'type' => 'note',
        //     'name' => 'Laz TEst',
        //     'id'   => '12',
        // ];
        // return json_encode($data);
        /*

            type-other
            type
            time
            date
            subject
            notes
            followup
            followup_on
            private

            constituent_ids

            page
            page_id
            page_name

        */
        if (request('type-other')) {
            $note->type = request('type-other');
        } else {
            $note->type = request('type');
        }

        $timestr = str_replace('Set Time', '', request('time'));
        $timestr = str_replace(' ', '', $timestr);

        //dd(request()->input());
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
        }

        $note->date = $formatted_time;
        $note->subject = request('subject');
        $note->notes = request('notes');
        if (request('followup')) {
            $note->followup = 1;
        }
        if (request('followup_on')) {
            $followup_on = request('followup_on');
            $note->followup_on = Carbon::parse($followup_on)->format('Y-m-d');
        }
        if (request('private')) {
            $note->private = 1;
        }

        //dd($note);

        $note->save();

        $constituent_ids = request('constituent_ids');

        $ids = explode(',', $constituent_ids);
        // Constituent

        // =============================> Clear out contact_persons not used
        $current_cps = ContactPerson::where('contact_id', $note->id)
                                    ->whereNotIn('person_id', $ids)
                                    ->get();
        foreach ($current_cps as $curr_cp) {
            $curr_cp->delete();
        }
        $current_cps = ContactPerson::where('contact_id', $note->id)
                                    ->whereNotIn('voter_id', $ids)
                                    ->get();
        foreach ($current_cps as $curr_cp) {
            $curr_cp->delete();
        }
        foreach ($ids as $constituent_id) {
            if ($constituent_id) {

                $person = findPersonOrImportVoter($constituent_id, Auth::user()->team->id);
                if ($person) {
                    $cp = new ContactPerson;
                
                    $cp->team_id = Auth::user()->team_id;
                    $cp->person_id = $person->id;
                    if (!is_numeric($constituent_id)) {
                        $cp->voter_id = $constituent_id;
                    }
                    $cp->contact_id = $note->id;
                    $cp->save();
                }
            
            }
        }

        if (request('page_link') && request('page_id')) {
            $type = request('page');
            switch($type) {
                case 'case':
                    $case = WorkCase::find(request('page_id'));
                    if ($case->team_id != Auth::user()->team_id) {
                        break;
                    }
                    $note->case_id = $case->id;
                    $note->save();
                    break;
                case 'organization':
                    $co = new ContactEntity;
                    $co->team_id = Auth::user()->team_id;
                    $co->entity_id = request('page_id');
                    $co->contact_id = $note->id;
                    $co->save();
                    break;
                case 'note':
                    $original_note = Contact::find(request('page_id'));
                    if ($original_note) {
                        if ($original_note->case_id > 0) {
                            $note->case_id = $original_note->case_id;
                            $note->save();
                        }

                        foreach ($original_note->contactPersons as $on_cp) {

                            $existing = ContactPerson::where('contact_id', $note->id)
                                                     ->where('person_id', $on_cp->person_id)
                                                     ->first();
                            if (!$existing) {
                                $new_cp = new ContactPerson;
                                $new_cp->person_id =  $on_cp->person_id;
                                $new_cp->voter_id =   $on_cp->voter_id;
                                $new_cp->contact_id = $note->id;
                                $new_cp->team_id =    Auth::user()->team_id;
                                $new_cp->save();
                            }
                        }
                        foreach ($original_note->contactEntities as $on_ce) {
                            $new_ce = new ContactEntity;
                            $new_ce->entity_id =  $on_ce->entity_id;
                            $new_ce->contact_id = $note->id;
                            $new_ce->team_id =    Auth::user()->team_id;
                            $new_ce->save();
                        }
                    }
            }
        }
        $data = [
            'type' => 'note',
            'name' => ''.$note->subject,
            'id'   => ''.$note->id,
        ];
        return json_encode($data);
    }
}
