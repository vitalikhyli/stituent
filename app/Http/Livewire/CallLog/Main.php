<?php

namespace App\Http\Livewire\CallLog;

use Livewire\Component;

use App\Contact;
use App\WorkCase;

use Auth;
use Carbon\Carbon;



class Main extends Component
{


    public $loaded = false;
    public $search;

    public $newWho;
    public $newNotes;
    public $typeOptions;
    public $newType;

    public $newDate;
    public $newDateValidated = false;
    public $newTime;

    public $newPrivate;
    public $newFollowUp;
    public $newFollowUpOn;

    public $contactFormFields = [];

    public $showAddNew = false;
    public $showLogReport = false;


    public function mount()
    {
        $this->updateTypeOptions();
        $this->contactFormFields = ['newContact', 'newWho', 'newNotes', 'newType', 'newDate'];
        if(!$this->newDate) {
            $this->newDate = Carbon::today()->format('n/j/Y');
        }
    }

    public function updatedSearch()
    {
        $this->emitTo('contacts', 'sync_search', $this->search);
    }

    public function updatedNewDate()
    {
        try {

            $is_the_date_valid = Carbon::parse($this->newDate);
            
        } catch (\Exception $e) {

            $this->newDateValidated = false;
            return;

        }

        $this->newDateValidated = true;
    }

    public function updateTypeOptions()
    {
        $this->typeOptions = Contact::where('team_id', Auth::user()->team->id)
                                    ->whereNotNull('type')
                                    ->pluck('type')
                                    ->unique()
                                    ->map(function ($item) {
                                        return strtoupper($item);
                                    })
                                    ->unique()
                                    ->map(function ($item) {
                                        return ucfirst(strtolower($item));
                                    });
    }

    public function toggleLogReport()
    {
        if (!$this->showLogReport) {
            $this->closeNewContact();
            $this->showLogReport = true;
        } else {
            $this->showLogReport = false;
        }
    }

    public function storeCase($thenRender = true)
    {
        $errors = [];

        $contact = $this->storeContact($thenRender = false);

        $new = new WorkCase;
        $new->team_id   = Auth::user()->team->id;
        $new->user_id   = Auth::user()->id;
        $new->subject   = $contact->subject;
        $new->notes     = $contact->notes;
        $new->date      = $this->newDate;
        $new->private   = ($this->newPrivate) ? true : false;
        $new->save();

        $contact->case_id = $new->id;
        $contact->save();

        if (!$errors) {
            $this->closeNewContact();
        }

        $this->clearSearch();
        $this->clearNewContactForm();
        $this->updateTypeOptions();

        if ($thenRender) {
            $this->showAddNew = false;
            $this->emitTo('contacts', 'render');
        }

        return $new;
    }

    public function storeContact($thenRender = true)
    {
        $errors = [];

        if (!$this->newDate) {
            $this->newDate = now();
        }

        $new = new Contact;
        $new->team_id   = Auth::user()->team->id;
        $new->user_id   = Auth::user()->id;
        $new->type      = $this->newType;
        $new->subject   = $this->newWho;
        $new->notes     = $this->newNotes;
        $new->date      = $this->newDate;
        $new->source    = 'call_log';
        $new->followup  = ($this->newFollowUp) ? true: false;
        if ($new->followup) {
            $new->followup_on  = $this->newFollowUpOn;
        }
        $new->private   = ($this->newPrivate) ? true : false;
        $new->save();

        if (!$errors) {
            $this->closeNewContact();
        }

        $this->clearSearch();
        $this->clearNewContactForm();
        $this->updateTypeOptions();

        $this->newContact = false;

        if ($thenRender) {
            $this->showAddNew = false;
            $this->emitTo('contacts', 'render');
        }

        return $new;
    }

    public function clearNewContactForm()
    {
        foreach($this->contactFormFields as $field) {
            $this->$field = null;
        }
        $this->newDate = Carbon::now()->format('n/j/Y');
    }

    public function clearSearch()
    {
        $this->search = null;
        $this->updatedSearch();
    }

    public function closeNewContact()
    {
        $this->showAddNew = false;
        $this->clearNewContactForm();
    }

    public function render()
    {
        return view('livewire.call-log.main');
    }
}
