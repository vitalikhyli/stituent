<?php

namespace App\Http\Livewire\Constituents;

use Livewire\Component;

use App\BulkEmail;

use Auth;


class EmailsForm extends Component
{

//////////////////////////////////[ LISTENERS ]////////////////////////////////////////

    protected $listeners = [
        'clear_all'                 => 'clearAllEmails'
    ];

    public function clearAllEmails()
    {
        $this->selected_emails = [];
        $this->lookup_email    = null;

        $this->is_open['emails'] = false;
    }

    //////////////////////////////////[ VARIABLES ]////////////////////////////////////////

	public $selected_emails = [];
	public $is_open         = [];
    public $lookup_email;

    //////////////////////////////////[ FUNCTIONS ]////////////////////////////////////////

    public function cleanUpSelected($array)
    {
        foreach($array as $id => $value) {
            if (!$value) unset($array[$id]);
        }
        return $array;
    }    

	public function toggleOpen($key)
	{
		$this->is_open[$key] = ($this->is_open[$key]) ? false : true;
		// $this->dispatchBrowserEvent('focus-search', ['field' => $key]);
	}

    public function clearLookup($code)
    {
        $this->{'lookup_'.$code} = null;
    }

    ////////////////////////////////////[ LIFECYCLE ]////////////////////////////////////////

    public function mount()
    {
        $this->is_open['emails'] = false;
    }

    public function updatedSelectedEmails()
    {
        $this->selected_emails = $this->cleanUpSelected($this->selected_emails);
        $this->emit('pass_selected_emails', $this->selected_emails);        
    }

    public function render()
    {
    	$emails = BulkEmail::where('team_id', Auth::user()->team->id)
    					   ->whereNotNull('completed_at');
    	if ($this->lookup_email) {
    		$emails = $emails->where('subject', 'like', '%'.$this->lookup_email.'%');
    	}
    	$emails = $emails->orderBy('completed_at', 'desc')->get();

    	$selected_emails_chosen = BulkEmail::whereIn('id', $this->selected_emails)
    									   ->where('team_id', Auth::user()->team->id)
    									   ->get();

        return view('livewire.constituents.emails-form',
    			[
    				'emails' => $emails,
    				'selected_emails_chosen' => $selected_emails_chosen
    			]);
    }
}
