<?php

namespace App\Http\Livewire;
use App\Donation;
use Livewire\Component;
use Auth;
use Carbon\Carbon;

class Contribution extends Component
{
	public $participant;
	public $new_donation;

	protected $rules = [
        'new_donation.team_id' => '',
        'new_donation.user_id' => '',
        'new_donation.date' => '',
        'new_donation.amount' => '',
        'new_donation.fee' => '',
        'new_donation.occupation' => '',
        'new_donation.employer' => '',
        'new_donation.notes' => '',
        'new_donation.first_name' => '',
        'new_donation.last_name' => '',
        'new_donation.campaign_event_id' => '',
        'new_donation.street' => '',
        'new_donation.city' => '',
        'new_donation.state' => '',
        'new_donation.zip' => '',
        'new_donation.method' => '',
        'new_donation.amount' => '',
        'new_donation.participant_id' => '',
    ];
	public function mount()
	{
		$this->resetNewDonation();
	}
	public function resetNewDonation()
	{
		$this->new_donation = new Donation;
		$this->new_donation->team_id = Auth::user()->team->id;
        $this->new_donation->user_id = Auth::user()->id;
        $this->new_donation->participant_id = ($this->participant) ? $this->participant->id : null;
        $this->new_donation->first_name = $this->participant->first_name;
        $this->new_donation->last_name = $this->participant->last_name;
        $this->new_donation->street = $this->participant->street;
        $this->new_donation->city = $this->participant->city;
        $this->new_donation->state = $this->participant->state;
        $this->new_donation->zip = $this->participant->zip;

        $this->new_donation->date = Carbon::today()->format('n/j/Y');
        $this->new_donation->fee = 0;
	}
    public function render()
    {
        return view('livewire.contribution');
    }
    public function save()
    {
    	$this->new_donation->date = Carbon::parse($this->new_donation->date)->toDateString();
        $this->new_donation->save();
        $this->resetNewDonation();

    }
}
