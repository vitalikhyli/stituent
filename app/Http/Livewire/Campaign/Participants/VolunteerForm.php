<?php

namespace App\Http\Livewire\Campaign\Participants;

use Livewire\Component;

use App\Models\Campaign\Volunteer;

use Auth;


class VolunteerForm extends Component
{
	public $participant;
	public $current;
	public $volunteer_options = [];
	public $types = [];

	public function mount($model = null)
	{
		$this->participant = $model;

        $this->volunteer_options = Volunteer::thisTeam()->withTrashed()
        												->pluck('types')
        												->flatten()
        												->unique()
        												->sort()
        												->values();	

		$volunteer = $this->participant->volunteerModel()->withTrashed()->first();
        $this->types = ($volunteer) ? $volunteer->types : [];

	}

	public function updatedCurrent()
	{
		$volunteer = $this->participant->volunteerModel()->withTrashed()->first();

		if (!$this->current) {

			if ($volunteer) {
				$volunteer->delete();
			} 

		}

		if ($this->current) {

			if ($volunteer) {
				if ($volunteer->trashed()) $volunteer->restore();	
			}

			if (!$volunteer) {
				$v = new Volunteer;
				$v->team_id = Auth::user()->team->id;
				$v->participant_id = $this->participant->id;
				$v->save();
			}

		}
	}

	public function updatedTypes()
	{
		$volunteer = $this->participant->volunteerModel;
		$volunteer->types = $this->types;
		$volunteer->save();
	}

    public function render()
    {
        return view('livewire.campaign.participants.volunteer-form', [
        				'model' => $this->participant
        			]);
    }
}
