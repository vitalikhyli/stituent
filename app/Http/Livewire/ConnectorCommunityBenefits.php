<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Entity;
use App\CommunityBenefit;
use App\CommunityBenefitEntity;

use Auth;


class ConnectorCommunityBenefits extends Component
{
	public $search;
	public $community_benefit;
	public $link_as;
	public $key;

    protected $listeners = [
        'clear_other_searches'      => 'clearSearch',
    ];

    public function updatedSearch()
    {
    	if ($this->search) {
    		$this->emit('clear_other_searches', $this->key); 
    	}
    }
    
    public function clearSearch($except_key)
    {
    	if ($except_key != $this->key) {
    		$this->search = null;
    	}
    }

    public function mount()
    {
    	for ($digit = 0; $digit < 20; $digit++) { 
    		$this->key .= rand(0,9);
    	}
    	$this->key = base64_encode($this->key);
    }

	public function link($id)
	{
		$entity = Entity::find($id);
		if (Auth::user()->cannot('basic', $entity)) abort(403);
		// if (Auth::user()->cannot('basic', $this->community_benefit)) abort(403);

		$pivot = CommunityBenefitEntity::where('entity_id', $id)
									   ->where('community_benefit_id', $this->community_benefit->id)
									   ->first();

		if ($pivot) {

			$pivot->{$this->link_as} = true;
			$pivot->save();

		} else {

			$pivot = new CommunityBenefitEntity;
			$pivot->team_id					= Auth::user()->team->id;
			$pivot->user_id					= Auth::user()->id;
			$pivot->community_benefit_id 	= $this->community_benefit->id;
			$pivot->entity_id 				= $entity->id;
			$pivot->{$this->link_as} 		= true;
			$pivot->save();

		}

	}

	public function unlink($id)
	{
		$entity = Entity::find($id);
		if (Auth::user()->cannot('basic', $entity)) abort(403);
		// if (Auth::user()->cannot('basic', $this->community_benefit)) abort(403);

		$pivot = CommunityBenefitEntity::where('entity_id', $id)
									   ->where('community_benefit_id', $this->community_benefit->id)
									   ->first();

		if ($pivot) {

			$pivot->{$this->link_as} = false;
			$pivot->save();

			if (!$pivot->beneficiary && !$pivot->initiator && !$pivot->partner) {
				$pivot->delete();
			}

		}

	}

    public function render()
    {
		$entities = Entity::where('team_id', Auth::user()->team->id)
					->where('name', 'like', '%'.$this->search.'%')
					->orderBy('name')
					->take(10)
					->get();

		$linked = CommunityBenefit::find($this->community_benefit->id)
								  ->entities()
								  ->wherePivot($this->link_as, true)
								  ->get();

        return view('livewire.connector-community-benefits', [
        														'entities' => $entities,
    														    'linked' => $linked
    														 ]);
    }
}
