<?php

namespace App\Http\Livewire\Campaign\Opportunities;

use Livewire\Component;

class Schedule extends Component
{
	public $opp;

	public function mount($opp)
	{
		$this->opp = $opp;
	}

    public function render()
    {
        return view('livewire.campaign.opportunities.schedule');
    }
}
