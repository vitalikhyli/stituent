<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Action;
use App\Participant;
use Auth;

class Actions extends Component
{
	public $new_action = null;
	public $existing_action = null;
	public $action_details = null;
	public $action_date = null;

	public $participant = null;
	public $adding_new = false;
	public $team_actions = null;

	public function mount($participant)
	{
		$this->participant = $participant;
		$this->team_actions = Action::select('name')
								    ->where('team_id', Auth::user()->team_id)
									->groupBy('name')
									->orderBy('name')
									->pluck('name');
		if (request()->input('action')) {
			$this->adding_new = true;
		}
	}
	public function toggleNew()
	{
		$this->adding_new = !$this->adding_new;
	}
	public function delete($action_id)
	{
		$action = Action::find($action_id);
		$action->delete();
	}
	public function addAction()
	{
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
		}

	}
    public function render()
    {
        return view('livewire.actions');
    }
}
