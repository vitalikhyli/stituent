<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\SharedCase;
use App\WorkCase;
use App\User;
use Auth;

class CaseShare extends Component
{
	public $allcases;
	public $case;
	public $case_id;

	public $new_shared_email;
	public $new_shared_type = 'user';
	public $new_shared_team;
	public $new_shared_user;

    public $editing = false;

    public function toggleEditing()
    {
        $this->editing = !$this->editing;
    }

    public function render()
    {
        if ($this->case_id) {
            $this->editing = true;
        }
    	if (!$this->case && !$this->case_id) {
    		$shared_cases = [];
    		return view('livewire.case-share', compact('shared_cases'));
    	}
    	if (!$this->case) {
    		$this->case = Workcase::find($this->case_id);
    	}
    	$this->new_shared_user = null;
    	$this->new_shared_team = null;
    	if ($this->new_shared_email) {
    		$user = User::where('email', $this->new_shared_email)
    				    ->latest()->first();
    		//dd($user);
    		if ($user) {
    			if ($user->office_team) {
	    			$this->new_shared_user = $user;
	    			$this->new_shared_team = $user->office_team;
	    		}
    		}
    	}
    	$shared_cases = SharedCase::where('case_id', $this->case->id)->get();
    	//dd($shared_cases);
        return view('livewire.case-share', compact('shared_cases'));
    }

    public function share()
    {
    	$shared_case = new SharedCase;
    	$shared_case->team_id = Auth::user()->team_id;
    	$shared_case->user_id = Auth::user()->id;
    	$shared_case->case_id = $this->case->id;

    	$shared_case->shared_type = $this->new_shared_type;
    	$shared_case->shared_user_id = $this->new_shared_user->id;
    	$shared_case->shared_team_id = $this->new_shared_team->id;

    	$shared_case->save();

    	$this->new_shared_email = null;
    }
    public function switchCase()
    {
    	$this->case = null;
    	$this->case_id = null;
    }
    public function delete($id)
    {
    	$shared_case = SharedCase::find($id);
    	if ($shared_case) {
    		$shared_case->delete();
    	}
    }
}
