<?php

namespace App\Http\Livewire\Campaign\Volunteers;

use Livewire\Component;

use App\Models\Campaign\Volunteer;


class Volunteers extends Component
{
	public $search;
	public $types = [];
	public $types_any;
	public $volunteer_options = [];

	public function mount()
	{
        $this->volunteer_options = Volunteer::thisTeam()->withTrashed()
        												->pluck('types')
        												->flatten()
        												->unique()
        												->sort()
        												->values();
	}

	public function updatedTypesAny()
	{
		if (!$this->types_any) {
			$this->types = [];
		}

		if ($this->types_any) {
			$this->types = $this->volunteer_options;
		}
	}

    public function render()
    {
    	$volunteers = Volunteer::thisTeam();

    	if ($this->search) {
			$volunteers = $volunteers->where(function ($q) {
										$q->orWhere('email', 'like', '%'.$this->search.'%');
										$q->orWhere('username', 'like', '%'.$this->search.'%');
									});
    	}

    	if ($this->types) {
			$volunteers = $volunteers->where(function ($q) {
									foreach($this->types as $type) {
										$q->orWhere('types', 'like',  '%'.$type.'%');
									}
								});
    	}

    	$volunteers = $volunteers->get();

        return view('livewire.campaign.volunteers.volunteers', 
    				['volunteers' => $volunteers]);
    }
}
