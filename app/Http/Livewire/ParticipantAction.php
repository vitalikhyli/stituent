<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Action;
use App\Participant;
use Auth;

class ParticipantAction extends Component
{
	    // ACTIONS
    public $new_action = null;
    public $existing_action = null;
    public $action_details = null;
    public $team_actions = null;
    public $top3_actions = null;
    public $voter = null;
    public $participant = null;

    public function mount($voter_or_participant)
    {
    	$this->voter = $voter_or_participant;
    	$this->team_actions = Action::select('name')
                                    ->where('team_id', Auth::user()->team_id)
                                    ->groupBy('name')
                                    ->orderBy('name')
                                    ->pluck('name');
                     //dd($this->team_actions);
        $this->top3_actions = Action::selectRaw('name, COUNT(*) as count')
                                    ->where('team_id', Auth::user()->team_id)
                                    ->where('auto', false)
                                    ->groupBy('name')
                                    ->orderByDesc('count')
                                    ->take(3)
                                    ->pluck('name');
    }

    public function render()
    {
        return view('livewire.participant-action');
    }

    public function deleteAction($action_id)
    {
        $action = Action::find($action_id);
        $action->delete();
    }
    public function clickAction($name)
    {
    	$this->existing_action = $name;
    }
    public function addAction()
    {

    	$this->participant = findParticipantOrImportVoter($this->voter->id, Auth::user()->team_id);
    	//dd($this->participant);
        $action_name = null;
        if ($this->existing_action) {
            $action_name = $this->existing_action;
        } else {
            $action_name = $this->new_action;
        }
        if ($action_name) {
            addCustomActionToParticipant($this->participant, $action_name, $this->action_details);
            $this->team_actions = Action::select('name')
                                    ->where('team_id', Auth::user()->team_id)
                                    ->groupBy('name')
                                    ->orderBy('name')
                                    ->pluck('name');
            $this->new_action = null;
            $this->existing_action = null;
            $this->action_details = null;
            $this->adding_new = false;
            $this->participant = Participant::find($this->participant->id);

            $this->emit('action_added');
        }

    }
}
