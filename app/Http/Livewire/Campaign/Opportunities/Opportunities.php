<?php

namespace App\Http\Livewire\Campaign\Opportunities;

use Livewire\Component;

use App\Models\Campaign\Opportunity;
use App\Models\Campaign\Volunteer;

use Auth;
use Carbon\Carbon;

class Opportunities extends Component
{
	public $new_name;
	public $new_type;
	public $new_starts_at;
	public $new_ends_at;
	public $new_group;

	public $volunteer_options;

	public $filter;
	public $types;

	public function mount()
	{
		$this->filter = 'all';
		$this->new_type = null; //'canvass';
		$this->new_starts_at = \Carbon\Carbon::today()->format('n/j/y');
	}

	public function addNew()
	{
		$opp = new Opportunity;
		$opp->campaign_id = CurrentCampaign()->id;
		$opp->team_id 	= Auth::user()->team->id;
		$opp->user_id 	= Auth::user()->id;
		$opp->name 		= $this->new_name;
		$opp->type 		= $this->new_type;
		$opp->starts_at = ($this->new_starts_at) ?? Carbon::now();
		$opp->ends_at 	= $this->new_ends_at;
		$opp->group 	= $this->new_group;
		$opp->save();

		$this->new_name = null;
		$this->new_type = null;
	}

	public function filterType($filter)
	{
		$this->filter = $filter;
	}

    public function render()
    {
    	$opps = Opportunity::thisTeam()->thisCampaign()->activeAndNotExpired()->orderBy('starts_at', 'desc');

    	if($this->filter && $this->filter != 'all') {
    		$opps = $opps->where('type', $this->filter);
    	}

    	$opps = $opps->get();

		$this->types = Opportunity::thisTeam()->pluck('type')->unique()->prepend('all');

		$opps = $opps->groupBy('group');

		$this->volunteer_options = Volunteer::thisTeam();
		$this->volunteer_options = $this->volunteer_options->get();

        return view('livewire.campaign.opportunities.opportunities', ['opps' => $opps]);
    }
}
