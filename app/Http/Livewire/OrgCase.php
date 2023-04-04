<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Entity;
use Auth;
use App\EntityCase;
use App\WorkCase;
use Carbon\Carbon;

class OrgCase extends Component
{
	public $case;
	public $entities;

	public function mount($case_id)
	{
		$this->case = WorkCase::find($case_id);
	}
    public function render()
    {
    	$this->entities = $this->case->entities;
        return view('livewire.org-case');
    }
}
