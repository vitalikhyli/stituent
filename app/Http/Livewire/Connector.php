<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Participant;
use App\Person;
use App\Voter;
use App\WorkCase;
use App\Tag;
use App\Group;
use App\Contact;
use App\GroupPerson;

use Auth;


class Connector extends Component
{
	public $editing; 
	public $model;
	public $class;
	public $description;
	public $description_singular;
	public $lookup;
	public $show_linked;
	public $url_dir;
	public $search_goes_at_the_end;
	public $display_count;
	public $details = true;
	public $groups_loaded = false;

	////////////////////////////////////////////////////////////////////////////////////////

	public function link($id)
	{
		if (Auth::user()->cannot('basic', $this->model)) abort(403);

		if ($this->class == 'App\Person') {
			$person = FindPersonOrImportVoter($id, Auth::user()->team->id);
			if (Auth::user()->cannot('basic', $person)) abort(403);
			$this->model->people()->attach($person->id, ['team_id' => Auth::user()->team->id, 'voter_id' => $person->voter_id]);

		}

		if ($this->class == 'App\Participant') {
			$participant = FindParticipantOrImportVoter($id, Auth::user()->team->id);
			if (Auth::user()->cannot('basic', $participant)) abort(403);
			$this->model->participants()->attach($participant->id, ['team_id' => Auth::user()->team->id, 'user_id' => Auth::user()->id, 'voter_id' => $participant->voter_id]);

		}

		$this->show_results = null;
	}

	public function unlink($id)
	{
		if (Auth::user()->cannot('basic', $this->model)) abort(403);

		if ($this->class == 'App\Person') {
			$person = Person::find($id);
			if (Auth::user()->cannot('basic', $person)) abort(403);
			$this->model->people()->detach($id);
		}
		if ($this->class == 'App\Participant') {
			$participant = Participant::find($id);
			if (Auth::user()->cannot('basic', $participant)) abort(403);
			$this->model->participants()->detach($id);
		}
		$this->show_results = null;
	}

	public function createNew()
	{
		if (Auth::user()->team->app_type == 'office' 
			&& !Auth::user()->permissions->createconstituents) return;

    	$words = explode(' ', $this->lookup);
        $first_name = array_shift($words);
        $last_name = implode(' ', $words);

		if ($this->class == 'App\Person') {

			$new = new Person;
			$new->first_name = $first_name;
			$new->last_name = $last_name;
			$new->team_id = Auth::user()->team->id;
			$new->save();
		}

		if ($this->class == 'App\Participant') {
			$new = new Participant;
			$new->first_name = $first_name;
			$new->last_name = $last_name;
			$new->team_id = Auth::user()->team->id;
			$new->user_id = Auth::user()->id;
			$new->save();
		}

		$this->link($new->id);

		$this->lookup = null;
	}

	////////////////////////////////////////////////////////////////////////////////////////

	public function mount($class, $model, $search_goes_at_the_end = null, $editing = false)
	{
		$this->model = $model;
		$this->class = $class;
		$this->editing = $editing;

		if ($class == 'App\Person') {
			$this->description = 'People';
			$this->description_singular = 'Person';
			$this->url_dir = 'constituents';
		}

		if ($class == 'App\Participant') {
			$this->description = 'Participants';
			$this->description_singular = 'Participant';
			$this->url_dir = 'participants';
		}

		if ($class == 'App\Household') {
			$this->description = 'Households';
			$this->description_singular = 'Household';
			$this->url_dir = 'households';
		}		

		$this->search_goes_at_the_end = ($search_goes_at_the_end) ? true : false;

	}
	public function loadGroups()
	{
		$this->groups_loaded = true;
	}
	public function toggleEditing()
	{
		$this->editing = !$this->editing;
	}

	public function toggleGroupMembership($person_id, $group_id)
	{
		$person = Person::find($person_id);
		if (Auth::user()->cannot('basic', $person)) abort(403);

		$group  = Group::find($group_id);
		if (Auth::user()->cannot('basic', $group)) abort(403);

		if ($person && $group) {
			if ($person->memberOfGroup($group->id)) {
				$person->groups()->detach($group);
			} else {
				$person->groups()->attach($group, ['team_id' => Auth::user()->team->id]);
			}
		}
	}

	public function setGroupSupport($person_id, $group_id, $position)
	{
		$person = Person::find($person_id);
		if (Auth::user()->cannot('basic', $person)) abort(403);

		$group  = Group::find($group_id);
		if (Auth::user()->cannot('basic', $group)) abort(403);

		if ($person && $group) {
			if ($person->memberOfGroup($group->id)) {
				$pivot = GroupPerson::where('group_id', $group->id)
									->where('person_id', $person->id)
									->first();
				$pivot->position = ($pivot->position == $position) ? null : $position;
				$pivot->save();
			}
		}
	}

    public function render()
    {

    	$words = explode(' ', $this->lookup);

    	$results = collect([]);

    	if ($this->lookup) {

			if ($this->class == 'App\Person') {
	    		$class = Person::where('team_id', Auth::user()->team->id);
	    	}
			if ($this->class == 'App\Participant') {
	    		$class = Participant::where('team_id', Auth::user()->team->id);
	    	}

	    	$voters = Voter::query();

	    	// $class = $class->where('full_address', 'LIKE', '%'.$this->lookup.'%');
	    	// $voters = $voters->where('full_address', 'LIKE', '%'.$this->lookup.'%');
	    	// 1. Households added to collection
	    	// 2. link() : if household -> createIfDoesNotExist with team
	    	// 3. link households
	    	// 4. Display: @foreach($results->groupBy('full_address') as $household => $hh_results)
	    	// 5. Show residents for each household

			if (count($words) > 1) {

	            $first_name = array_shift($words);
	            $last_name = implode(' ', $words);

		    	$class = $class->where('first_name', 'LIKE', $first_name.'%');
		    	$voters = $voters->where('first_name', 'LIKE', $first_name.'%');

				$voters = $voters->where('last_name', 'LIKE', $last_name.'%');
		    	$class = $class->where('last_name', 'LIKE', $last_name.'%');

			} else {

		    	$class = $class->where('full_name', 'LIKE', '%'.$this->lookup.'%');
		    	$voters = $voters->where('full_name', 'LIKE', '%'.$this->lookup.'%');

			}

	        $valid_voterids = [];
	        foreach ($class->pluck('voter_id') as $vid) {
	            if ($vid) {
	                $valid_voterids[] = $vid;
	            }
	        }

	        $voters = $voters->whereNotIn('id', $valid_voterids);

			$class = $class->take(10)->get();
			$voters = $voters->take(10)->get();

			if (get_class($this->model) == 'App\WorkCase') {
				$linked_ids = WorkCase::find($this->model->id)->people->pluck('id')->toArray();
				$linked_voter_ids = WorkCase::find($this->model->id)->people()->whereNotNull('people.voter_id')->pluck('people.voter_id')->toArray();
			}

			if (get_class($this->model) == 'App\Contact') {
				$linked_ids = Contact::find($this->model->id)->people->pluck('id')->toArray();
				$linked_voter_ids = Contact::find($this->model->id)->people()->whereNotNull('people.voter_id')->pluck('people.voter_id')->toArray();
			}

			if (get_class($this->model) == 'App\Tag') {
				$linked_ids = Tag::find($this->model->id)->participants->pluck('id')->toArray();
				$linked_voter_ids = Tag::find($this->model->id)->participants()->whereNotNull('participants.voter_id')->pluck('participants.voter_id')->toArray();
			}

			$class = $class->each(function ($item) use ($linked_ids) {
				$item['linked'] = (in_array($item['id'], $linked_ids)) ? true : false;
			});

			$voters = $voters->each(function ($item) use ($linked_voter_ids) {
				$item['linked'] = (in_array($item['id'], $linked_voter_ids)) ? true : false;
			});

			$results = $class->merge($voters);
			
		}

		switch (get_class($this->model)) {

			case 'App\WorkCase':
				$linked = WorkCase::find($this->model->id)->people()->where('is_household', false)->get()->sortBy('last_name');
				break;

			case 'App\Contact':
				$linked = Contact::find($this->model->id)->people()->where('is_household', false)->get()->sortBy('last_name');
				break;

			case 'App\Tag':
				$linked = Tag::find($this->model->id)->participants->sortBy('last_name');
				break;
		}

		if (!$this->show_linked) {

			$this->display_count = $linked->count();
			$linked = collect([]);

		} else {

			$linked = $linked->each(function ($item) {
				$item['in_team'] = ($item->team_id == Auth::user()->team->id) ? true : false;
				$item['in_voter_file'] = (Voter::find($item->voter_id)) ? true : false;
			});

		}

        return view('livewire.connector', [
        									'results' => $results,
        									'linked' => $linked,
        								  ]);
    }
}
