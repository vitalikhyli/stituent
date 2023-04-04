<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Auth;
use App\Voter;
use App\Participant;

class WebSignup extends Component
{
	public $websignup;
	
    public function render()
    {
        return view('livewire.web-signup');
    }

    public function addAsVolunteer()
    {
    	//dd($this->websignup);
    	// 1) Check for voter file match
    	$votermatches = Voter::where('full_name', $this->websignup->name)
    					     ->get();

    	$voterid = null;

    	if ($votermatches->count() == 1) {
    		$voterid = $votermatches->first()->id;
    	} else {
    		$name_split = explode(' ', $this->websignup->name);
    		if (count($name_split) == 2) {
    			$votermatches = Voter::where('first_name', $name_split[0])
    								 ->where('last_name', $name_split[1])
    								 ->get();
    			if ($votermatches->count() == 1) {
    				$voterid = $votermatches->first()->id;
    			}				 
    		}
    	}

    	$participant = null;
    	if ($voterid) {
    		$participant = findParticipantOrImportVoter($voterid, Auth::user()->team_id);
    	} else {
    		$participant = new Participant;
            $participant->team_id = Auth::user()->team_id;
            $participant->user_id = Auth::user()->id;

            $participant->full_name = titleCase($this->websignup->name);
            $name_split = explode(' ', $this->websignup->name);
            $participant->first_name = titleCase($name_split[0]);
            if (isset($name_split[1])) {
	            $participant->last_name = titleCase($name_split[1]);
	        }
	        if (isset($this->websignup->data['location'])) {
	            $participant->address_street = ucwords(strtolower($this->websignup->data['location']));
	        }

            if ($this->websignup->email) {
            	$participant->primary_email = $this->websignup->email;
            }

            $participant->save();
    	}

    	if (isset($this->websignup->data['volunteer'])) {
	    	foreach ($this->websignup->data['volunteer'] as $voption) {
	    		$participant->markAsVolunteer($voption);
	    	}
	    } else {
	    	$participant->markAsVolunteer('general');
	    }
    	$this->websignup->voter_id = $voterid;
    	$this->websignup->participant_id = $participant->id;
    	$this->websignup->save();

    }
}
