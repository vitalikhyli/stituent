<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use Livewire\WithPagination;


use App\User;
use App\Person;
use App\GroupPerson;
use App\Voter;

use Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class Show extends Component
{
    use WithPagination;

    //////////////////////////////////[ LISTENERS ]////////////////////////////////////////

    protected $listeners = [
        'refresh'      => 'refreshRequest'  // Emited up from a nested component
    ];

    public function refreshRequest()
    {
        $this->render();
    }

	//////////////////////////////////[ PROPERTIES ]////////////////////////////////////////

	public $group; // Passed in from blade
	public $show_position;
	public $search;
	public $add_person_lookup;
    public $add_person;
    public $add_person_primary_email;
    public $add_person_position;
    public $add_person_title;
    public $add_person_notes;
    public $just_created = [];
    public $file_mode = false;
    public $show_emails_mode = false;

	//////////////////////////////////[ FUNCTIONS ]////////////////////////////////////////

    public function getEmailListProperty()
    {
        $array = $this->getEmails()->toArray();
        return implode(', ', $array);
    }

    public function getMissingEmailListProperty()
    {
        if ($this->show_position) {
            $missing_emails = $this->group->people()
                                   ->wherePivot('position', $this->show_position)
                                   ->where('primary_email', null)
                                   ->wherePivot('group_email', null)
                                   ->pluck('full_name')
                                   ->toArray();
        } else {
            $missing_emails = $this->group->people()
                                   ->where('primary_email', null)
                                   ->wherePivot('group_email', null)
                                   ->pluck('full_name')
                                   ->toArray();
        }
        return implode("\n", $missing_emails);
    }

    public function getEmails()
    {
        if ($this->show_position) {
            $group_person = GroupPerson::where('group_id', $this->group->id)
                                       ->where('position', $this->show_position)
                                       ->get();
        } else {
            $group_person = GroupPerson::where('group_id', $this->group->id);
        }

        $person_ids = [];
        $group_emails = collect([]);
        foreach ($group_person as $gp) {
            if ($gp->group_email) {
                $group_emails[] = $gp->group_email;
                $person_ids[] = $gp->person_id;
            }
        }

        if ($this->show_position) {

            $primary_emails = $this->group->people()
                                       ->wherePivot('position', $this->show_position)
                                       ->whereNotIn('person_id', $person_ids)
                                       ->whereNotNull('primary_email')
                                       ->where('primary_email', '<>', '')
                                       ->pluck('primary_email');

        } else {

            $primary_emails = $this->group->people()
                                       ->whereNotIn('person_id', $person_ids)
                                       ->whereNotNull('primary_email')
                                       ->where('primary_email', '<>', '')
                                       ->pluck('primary_email');

        }

        return $primary_emails->merge($group_emails);
    }

	public function togglePosition($position)
	{
		if ($this->show_position == $position) {
			$this->show_position = null;
		} else {
			$this->show_position = $position;
		}

	}

    public function addPersonInitial($data)
    {
        if(substr($data, 0, 4) == 'NEW_') {

            $person = base64_decode(substr($data, 4)); // Actually the lookup string

        } elseif (!is_numeric($data)) {

            $person = Voter::find($data);

        } else {

            $person = Person::find($data);
            if (Auth::user()->cannot('basic', $person)) abort(403);

            $this->add_person_primary_email = $person->primary_email;   // Show existing email

        }

        $this->add_person = $person;

    }

    public function addPersonFinal()
    {
        if (gettype($this->add_person) != 'object') {

            // Create new Person from lookup string
            $person = new Person;
            $words = explode(' ', $this->add_person);
            $person->last_name = $words[count($words) -1];
            unset($words[count($words) -1]);
            $person->first_name = implode(' ', $words);
            $person->team_id = Auth::user()->team->id;
            $person->save();

        } elseif(get_class($this->add_person) == 'App\Person') {

            $person = $this->add_person;

        } elseif(get_class($this->add_person) == 'App\Voter') {

            $person = findPersonOrImportVoter($this->add_person->voter_id, Auth::user()->team->id);

        }

        if (Auth::user()->cannot('basic', $person)) abort(403);
        if (Auth::user()->cannot('basic', $this->group)) abort(403);

        $pivot = GroupPerson::where('group_id', $this->group->id)
                            ->where('person_id', $person->id)
                            ->first();
        if (!$pivot) {
            $pivot = new GroupPerson;
            $pivot->team_id     = Auth::user()->team->id;
            $pivot->person_id   = $person->id;
            $pivot->group_id    = $this->group->id;
            $pivot->position    = $this->add_person_position;
            $pivot->title       = $this->add_person_title;
            $pivot->notes       = $this->add_person_notes;
            $pivot->save();

            $person->primary_email = $this->add_person_primary_email;
            $person->save();
        }

        $this->add_person_lookup = null;
        $this->add_person = null;

        $this->just_created[] = $pivot->id;
    }

    public function cancelAddPerson()
    {
        $this->add_person_lookup = null;
        $this->add_person = null;
    }

	//////////////////////////////////[ LIFE CYCLE ]////////////////////////////////////////

	public function mount()
	{
        //
	}

    public function updatedAddPersonLookup()
    {
        $this->add_person                = null;
        $this->add_person_primary_email  = null;
        $this->add_person_position       = null;
        $this->add_person_title          = null;
        $this->add_person_notes          = null;
    }

    public function render()
    {
        //dd($this->getEmails());
		$instances = $this->group->groupPerson();

    	if ($this->show_position) {
    		$instances = $instances->where('position', $this->show_position);
    	}

        $count = $instances->count();
    	$instances = $instances->get();
        
        $instances = $instances->each(function ($item) {
            $item['sort_by'] = $item->person->last_name;
        })->sortBy('sort_by');

    	if ($this->search) {
    		$instances = $instances->filter(function ($item) {

                foreach(['full_name', 'primary_email', 'full_address', 'notes', 'title'] as $field) {
                    if (stripos($item->person->$field, $this->search) !== false) return true;
                }

    			// if (stripos($item->person->full_name, $this->search) !== false) return true;
    			// if (stripos($item->person->primary_email, $this->search) !== false) return true;
    			// if (stripos($item->person->full_address, $this->search) !== false) return true;
    			// if (stripos($item->notes, $this->search) !== false) return true;
    			// if (stripos($item->title, $this->search) !== false) return true;

    		});
    	}

    	$instances = $instances->each(function ($item) {
            if ($item->person->created_by) {
                $item['user_who'] = User::find($item->person->created_by)->short_name;
            }
        })->each(function ($item) {
            if ($item->person->created_at) {
                $item['user_when'] = $item->person->created_at;
            }
        });

        if ($this->add_person_lookup && strlen($this->add_person_lookup) > 1) {

            $people_in_group_ids = $this->group->people->pluck('id')->unique();
            $voter_ids = Person::where('team_id', Auth::user()->team->id)
                               ->whereNotNull('voter_id')->pluck('voter_id')
                               ->unique();

            $words = explode(' ', $this->add_person_lookup);

            if (count($words) > 1) {

                $last_name = $words[count($words) -1];
                unset($words[count($words) -1]);
                $first_name = implode(' ', $words);

                $people = Person::where('team_id', Auth::user()->team->id)
                                ->whereNotIn('id', $people_in_group_ids)
                                ->where('first_name', 'like', $first_name.'%')
                                ->where('last_name', 'like', $last_name.'%')
                                ->take(10)
                                ->get();

                $voters = Voter::whereNotIn('id', $voter_ids)
                                     ->where('first_name', 'like', $first_name.'%')
                                     ->where('last_name', 'like', $last_name.'%')
                                     ->take(10)
                                     ->get();

            } else {


                $first = Person::where('team_id', Auth::user()->team->id)
                                ->whereNotIn('id', $people_in_group_ids)
                                ->where('first_name', 'like', $this->add_person_lookup.'%')
                                ->take(10)
                                ->get();

                $last = Person::where('team_id', Auth::user()->team->id)
                                ->whereNotIn('id', $people_in_group_ids)
                                ->where('last_name', 'like', $this->add_person_lookup.'%')
                                ->take(10)
                                ->get();

                $voters_first = Voter::whereNotIn('id', $voter_ids)
                                     ->where('first_name', 'like', $this->add_person_lookup.'%')
                                     ->take(10)
                                     ->get();

                $voters_last = Voter::whereNotIn('id', $voter_ids)
                                     ->where('last_name', 'like', $this->add_person_lookup.'%')
                                     ->take(10)
                                     ->get();

                $people = $first->merge($last);
                $voters = $voters_first->merge($voters_last);

            }

            $people = $people->merge($voters)->sortBy('last_name')->take(20);

        } else {

            $people = collect([]);

        }
        if ($count > $paginate_minimum = 100) {
            $page = $_GET['page'] ?? null;
            $instances = $this->paginateCollection($instances, $paginate_minimum, $page);
        }

        return view('livewire.groups.show', [
        										'instances' => $instances,
                                                'people' => $people,
                                                'count' => $count
        									]);
    }

    public function paginateCollection($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function paginationView()
    {
        return 'livewire.list-paginate-links';
    }
}
