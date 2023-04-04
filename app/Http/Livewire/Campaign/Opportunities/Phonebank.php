<?php

namespace App\Http\Livewire\Campaign\Opportunities;

use Livewire\Component;

use App\CampaignList;
use \App\Models\Campaign\Opportunity;
use \App\Models\Campaign\Volunteer;
use \App\Models\Campaign\OpportunityVolunteer;

use Carbon\Carbon;


class Phonebank extends Component
{
	public $opp;

	public $filter_lists;
	public $filter_volunteers;

	public $name;
	public $script;
	public $starts_at;
	public $ends_at;
	public $subscribable;

	public function mount($opp)
	{
		$this->opp 			= $opp;
		$this->name 		= $opp->name;
		$this->script 		= $opp->script;
		$this->subscribable 		= $opp->subscribable;
		$this->starts_at 	= ($opp->starts_at) ? $opp->starts_at->format('n/d/y h:i a') : null;
		$this->ends_at 		= ($opp->ends_at) ? $opp->ends_at->format('n/d/y h:i a') : null;
	}

	public function updated()
	{
		$this->opp->name 			= ($this->name) ? $this->name : '(Unnamed)';
		$this->opp->script 			= $this->script;	
		$this->opp->subscribable 	= $this->subscribable;		
		$this->opp->starts_at 		= $this->starts_at;
		$this->opp->ends_at 		= $this->ends_at;
		$this->opp->save();
	}

	public function selectList($id)
	{
		if ($this->opp->list_id == $id) {
			$this->opp->list_id = null;
		} else {
			$this->opp->list_id = $id;
		}

		$this->opp->save();

		$this->filter_lists = null;
	}

	public function invite($volunteer_id)
	{
		$volunteer = Volunteer::find($volunteer_id);
		$pivot = new OpportunityVolunteer($this->opp, $volunteer);
		$pivot->save();

		$this->filter_volunteers = null;
	}

	public function uninvite($volunteer_id)
	{
		$volunteer = Volunteer::find($volunteer_id);
		OpportunityVolunteer::where('opportunity_id', $this->opp->id)
							->where('volunteer_id', $volunteer->id)
							->delete();
	}

    public function render()
    {
    	$this->opp = Opportunity::find($this->opp->id);	// Refresh this

    	$lists = CampaignList::thisTeam();
    	if($this->filter_lists) {
    		$lists = $lists->where('name', 'like', '%'.$this->filter_lists.'%');
    	}
    	$lists = $lists->where('id', '!=', $this->opp->list_id)
    				   ->orderBy('created_at')
    				   ->get()
    				   ->each(function($item) {
    				   		$item['selected'] = false;
    				   });

    	$selected_list = CampaignList::find($this->opp->list_id);
    	if ($selected_list) {
    		$selected_list->selected = true;
    	}
    	
    	if ($selected_list) {
    		$lists = $lists->prepend($selected_list);
    	}

		$volunteer_options = Volunteer::thisTeam()
									  ->whereNotIn('id', $this->opp->invited->pluck('id'))
									  ->whereNotNull('email');

		if ($this->filter_volunteers) {
			$volunteer_options = $volunteer_options->where(function ($q) {
														$q->orWhere('email', 'like', '%'.$this->filter_volunteers.'%');
														$q->orWhere('username', 'like', '%'.$this->filter_volunteers.'%');
													});
		}

		$volunteer_options = $volunteer_options->get();

        return view('livewire.campaign.opportunities.phonebank',
        					['lists' => $lists,
    						 'volunteer_options' => $volunteer_options]);
    }
}
