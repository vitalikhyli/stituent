<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\GroupPerson;
use App\Person;
use Auth;

class ConstituentGroups extends Component
{
	public $person;
	public $new_group;

	public function mount($person)
	{
		$this->person = $person;
	}
    public function render()
    {
    	if ($this->new_group) {
    		$person_group = new GroupPerson;
    		$person_group->team_id = Auth::user()->team_id;
    		$person_group->group_id = $this->new_group;
    		$person_group->person_id = $this->person->id;
    		$person_group->save();
    		$this->new_group = null;

    		$this->person = Person::find($this->person->id);
    	}
        return view('livewire.constituent-groups');
    }
}
