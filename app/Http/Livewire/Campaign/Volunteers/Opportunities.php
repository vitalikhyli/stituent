<?php

namespace App\Http\Livewire\Campaign\Volunteers;

use Livewire\Component;

use App\Models\Campaign\Opportunity;
use App\Models\Campaign\Volunteer;

use Auth;

class Opportunities extends Component
{
	public $new_name;
	public $new_type;
	public $new_starts_at;
	public $new_ends_at;

	public $volunteer_options;

	public $filter;
	public $types;
	public $selected;

	public function mount()
	{
		$this->filter = 'all';
	}

	public function addNew()
	{
		$opp = new Opportunity;
		$opp->team_id = Auth::user()->team->id;
		$opp->name 		= $this->new_name;
		$opp->type 		= $this->new_type;
		$opp->starts_at = $this->new_starts_at;
		$opp->ends_at 	= $this->new_ends_at;
		$opp->save();

		$this->new_name = null;
		$this->new_type = null;
	}

	public function filterType($filter)
	{
		$this->filter = $filter;
	}

	public function selectOpportunity($id)
	{
		$selected = Opportunity::find($id);
		if (!$this->selected || $this->selected->id != $selected->id) {
			$this->selected = $selected;
		} else {
			$this->selected = null;
		}
	}

    public function render()
    {
    	$opps = Opportunity::thisTeam()->orderBy('starts_at', 'desc');

    	if($this->filter && $this->filter != 'all') {
    		$opps = $opps->where('type', $this->filter);
    	}

    	$opps = $opps->get();

		$this->types = Opportunity::thisTeam()->pluck('type')->unique()->prepend('all');

		$this->volunteer_options = Volunteer::thisTeam();
		if ($this->selected) {
			$this->volunteer_options = $this->volunteer_options->whereNotIn('id', $this->selected->invited->pluck('id'));
		}
		$this->volunteer_options = $this->volunteer_options->get();

        return view('livewire.campaign.volunteers.opportunities', ['opps' => $opps]);
    }
}
