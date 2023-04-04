<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;

use App\Person;
use App\GroupPerson;
use App\Group;
use App\User;

use Auth;

use Carbon\Carbon;


class Instance extends Component
{

	//////////////////////////////////[ PROPERTIES ]////////////////////////////////////////

	public $instance; // Passed in from blade
	public $was_just_created; // Passed in from blade -- optionally

	public $search;
	public $show_group_email = false;

	public $primary_email;
	public $group_email;
	public $title;
	public $notes;
	public $position;

	public $just_changed = [];


	//////////////////////////////////[ FUNCTIONS ]////////////////////////////////////////

	public function togglePosition($position)
	{
		if ($this->position == $position) {
			$this->position = null;
		} else {
			$this->position = $position;
		}

		$this->updated();

	}

    public function removePersonFinal($id)
    {
        $person = Person::find($id);
        if (Auth::user()->cannot('basic', $person)) abort(403);
        if (Auth::user()->cannot('basic', $this->instance->group)) abort(403);

        $pivot = GroupPerson::where('group_id', $this->instance->group_id)
                            ->where('person_id', $person->id)
                            ->first();

        if ($pivot) {
            $pivot->delete();
        }
        $group = Group::find($pivot->group_id);
		$group->updatePeopleCounts();

        $this->emitUp('refresh'); // Reresh parent component
    }
	//////////////////////////////////[ LIFE CYCLE ]////////////////////////////////////////

	public function mount()
	{
		$person = $this->instance->person;
		$this->primary_email 	= $person->primary_email;
		$this->group_email 		= $this->instance->group_email;
		$this->notes 			= $this->instance->notes;
		$this->title 			= $this->instance->title;
		$this->position 		= $this->instance->position;

		foreach(['group_email', 
				 'primary_email', 
			 	 'title', 
			 	 'position', 
			 	 'notes'] as $field) {

			$this->just_changed[$field] = null;
		}

	}

	public function updated()
	{
    	$person = $this->instance->person;
		$instance = GroupPerson::find($this->instance->id);


		$an_email_changed = false;
		$now = Carbon::now()->toDateTimeString();

		if ($person->primary_email != $this->primary_email) {
			$this->just_changed['primary_email'] = $now;
			$an_email_changed = true;
		}
		
		if ($person->group_email != $this->group_email) {
			$this->just_changed['group_email'] = $now;
			$an_email_changed = true;
		}
		
		if ($instance->notes != $this->notes) {
			$this->just_changed['notes'] = $now;
		}
		
		if ($instance->title != $this->title) {
			$this->just_changed['title'] = $now;
		}
		
		if ($instance->position != $this->position) {
			$this->just_changed['position'] = $now;
		}

		if ($an_email_changed) $this->emitUp('refresh'); // So list of emails is refreshed

		$person->primary_email 	= $this->primary_email;
		
		$person->save();
		$instance->notes 	= $this->notes;
		$instance->title 	= $this->title;
		$instance->position = $this->position;
		$instance->group_email 	= $this->group_email;
		$instance->save();

		$group = Group::find($instance->group_id);
		$group->updatePeopleCounts();

	}

    public function render()
    {
		foreach($this->just_changed as $field => $time) {
			if (Carbon::parse($time)->diffInSeconds() > 5) $this->just_changed[$field] = null;
		}
		if ($this->instance->group_email) {
			$this->show_group_email = true;
		}

    	$this->instance['user_who'] = $this->instance->created_by_name;
    	$this->instance['user_when'] = $this->instance->created_at;

        return view('livewire.groups.instance');
    }
}
