<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use App\Category;
use App\Group;
use App\GroupPerson;
use Auth;

class Constituent extends Component
{
	public $group_person;
	public $existing;
	public $existing_filter;

	public $existing_group_id;
	public $existing_group;
	public $existing_group_notes;
	public $existing_group_title;
	public $existing_group_position;

	public $new_group_category_id;
	public $new_group_category;
	public $new_group_name;
	public $new_group_notes;
	public $new_group_title;
	public $new_group_position;


	public function mount($group_person)
	{
		$this->group_person = $group_person;
		$this->existing = true;
	}

	public function clearForm()
	{
		$this->new_group_category_id = null;
		$this->new_group_category = null;
		$this->new_group_name = null;
		$this->new_group_notes = null;
		$this->new_group_position = null;
		$this->new_group_title = null;
		$this->existing_group_id = null;
		$this->existing_group = null;
		$this->existing_group_notes = null;
		$this->existing_group_position = null;
		$this->existing_group_title = null;
	}

	public function makePersonIfVoter()
	{
		if (!is_numeric($this->group_person->id)) {
			$this->group_person = findPersonOrImportVoter($this->group_person->id, Auth::user()->team_id);
		}
	}

	public function createGroup()
	{

		$this->makePersonIfVoter();

		$group = new Group;
		$group->created_by 	= Auth::user()->id;
		$group->team_id 	= Auth::user()->team_id;
		$group->category_id = $this->new_group_category_id;
		$group->name 		= $this->new_group_name;
		$group->save();

		$group_person = new GroupPerson;
		$group_person->group_id   = $group->id;
		$group_person->person_id  = $this->group_person->id;
		$group_person->team_id    = $this->group_person->team_id;
		$group_person->position   = $this->new_group_position;
		$group_person->title   	  = $this->new_group_title;
		$group_person->notes   	  = $this->new_group_notes;
		$group_person->save();

		return redirect(Auth::user()->app_type.'/constituents/'.$this->group_person->id);
	}
	
	public function addToGroup()
	{
		$this->makePersonIfVoter();

		$group = $this->existing_group;
		$group_person = GroupPerson::where('person_id', $this->group_person->id)
									   ->where('group_id', $group->id)
									   ->first();

		if (!$group_person) {
			$group_person = new GroupPerson;
			$group_person->group_id   = $group->id;
			$group_person->person_id  = $this->group_person->id;
			$group_person->team_id    = $this->group_person->team_id;
		}

		$group_person->position   = $this->existing_group_position;
		$group_person->title   	  = $this->existing_group_title;
		$group_person->notes   	  = $this->existing_group_notes;
		$group_person->save();

		return redirect(Auth::user()->app_type.'/constituents/'.$this->group_person->id);
	}
	public function toggleExisting()
	{
		$this->existing = !$this->existing;
	}
    public function render()
    {

    	if ($this->new_group_category_id) {
    		if (!$this->new_group_category) {
    			$this->new_group_category = Category::find($this->new_group_category_id);
    		}
    		if ($this->new_group_category->id != $this->new_group_category_id) {
    			$this->new_group_category = Category::find($this->new_group_category_id);
    		} 
    	} else {
    		$this->new_group_category = null;
    	}
    	if ($this->existing_group_id) {
    		if (!$this->existing_group) {
    			$this->existing_group = Group::find($this->existing_group_id);
    		}
    		if ($this->existing_group->id != $this->existing_group_id) {
    			$this->existing_group = Group::find($this->existing_group_id);
    		} 
    	} else {
    		$this->existing_group = null;
    	}
        return view('livewire.groups.constituent');
    }
}
