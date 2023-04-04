<?php

namespace App\Http\Livewire\CallLog;

use Livewire\Component;

use App\Person;

use Auth;


class EmailLink extends Component
{
	public $thecall;
	public $email;
	public $person_id;

	public function linkEmail()
	{
		$person = Person::find($this->person_id);
		if (Auth::user()->cannot('basic', $person)) abort(403);

		if (!$person->primary_email) {
			$person->primary_email = $this->email;
			$person->save();
		}
	}

	public function mount($thecall)
	{
		$this->thecall = $thecall;
		$this->person_id = null;
		$this->reset(['person_id']);
	}

    public function render()
    {
    	

    	$string = $this->thecall['subject'].' '.$this->thecall['notes'];
        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
        preg_match_all($pattern, $string, $matches);
		$detected_emails = ($matches[0]) ? $matches[0] : [];

        return view('livewire.call-log.email-link', ['thecall' => $this->thecall,
    												 'detected_emails' => $detected_emails]);
    }
}
