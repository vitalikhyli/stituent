<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Entity;
use Auth;
use App\EntityCase;
use App\WorkCase;
use Carbon\Carbon;

class CaseOrg extends Component
{
	public $org;
	public $link_case_id;
	public $cases;
	public $allcases;

	public $creating_new_case = false;
	public $new_case_name;

	public function mount($org_id)
	{
		$this->org = Entity::find($org_id);
	}
    public function render()
    {
    	if ($this->link_case_id) {
    		if (!$this->cases->pluck('id')->contains($this->link_case_id)) {
    			$ec = new EntityCase;
    			$ec->team_id = Auth::user()->team_id;
    			$ec->user_id = Auth::user()->id;
    			$ec->case_id = $this->link_case_id;
    			$ec->entity_id = $this->org->id;
    			$ec->save();
    			$this->link_case_id = null;
    		}
    		
    	}
    	$this->refreshCases();
        return view('livewire.case-org');
    }
    public function refreshCases()
    {
    	$case_ids = EntityCase::where('team_id', Auth::user()->team_id)
    						  ->where('entity_id', $this->org->id)
    						  ->pluck('case_id');
    	$this->cases = WorkCase::where('team_id', Auth::user()->team_id)
    					       ->whereIn('id', $case_ids)
    					       ->orderByDesc('date')
    					       ->get();
    	$this->allcases = WorkCase::where('team_id', Auth::user()->team_id)
								  ->orderByDesc('created_at')
								  ->get()
								  ->groupBy('status')
								  ->toBase();
    }
    public function creatingCase()
    {
    	$this->creating_new_case = true;
    }
    public function removeCase($case_id)
    {
    	$case = WorkCase::find($case_id);
    	$ec = EntityCase::where('case_id', $case->id)
    					->where('entity_id', $this->org->id)
    					->first();
   		if ($ec) {
	    	$ec->delete();
	    }
    }
    public function newCase()
    {
    	$case = new WorkCase;
    	$case->team_id = Auth::user()->team_id;
		$case->user_id = Auth::user()->id;
    	$case->subject = $this->new_case_name;
    	$case->date = Carbon::today();
    	$case->save();

    	$ec = new EntityCase;
		$ec->team_id = Auth::user()->team_id;
		$ec->user_id = Auth::user()->id;
		$ec->case_id = $case->id;
		$ec->entity_id = $this->org->id;
		$ec->save();

		return redirect(Auth::user()->team->app_type.'/cases/'.$case->id.'/edit');
    }
}
