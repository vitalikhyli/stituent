<?php

namespace App\Http\Livewire\CallLog;

use Livewire\Component;

use App\Contact;
use App\Person;

use Auth;

use Livewire\WithPagination;

    

class Contacts extends Component
{
    use WithPagination;
    // protected $paginationTheme = 'bootstrap';

    public $loaded = false;
    public $search_term;

    protected $listeners = [
        'sync_search' => 'syncSearch',
        'render' => 'render',
    ];

    public function linkEmail($email, $person_id)
    {
        $person = Person::where('team_id', Auth::user()->team->id)
                        ->where('id', $person_id)
                        ->first();

        if ($person) {

            if (!$person->primary_email) {
                $person->primary_email = $email;
            } else {
                $other_emails = $person->other_emails;
                $other_emails[] = [$person->primary_email, null];
                $person->other_emails = $other_emails;
                $person->primary_email = $email;
            }

        }

        $person->save();
    }

    public function syncSearch($value)
    {
        $this->search_term = $value;
    }

    public function loadCallLog()
    {
        $this->loaded = true;
    }

    public function showContact($contact_id)
    {
        $this->emitTo('contact-modal', 'set_show_to_true', $contact_id);
        $this->skipRender();
    }

    public function render()
    {
        if (!$this->search_term) {

            $contacts = Contact::where('team_id', Auth::user()->team->id)
                        ->where('source', 'call_log')
                        ->where('subject', 'like', '%'.$this->search_term.'%')
                        ->orderBy('created_at', 'desc')
                        ->paginate(100);

        } elseif (strlen($this->search_term)) {

            $a = Contact::where('team_id', Auth::user()->team->id)
                        ->where('source', 'call_log')
                        ->where('subject', 'like', '%'.$this->search_term.'%')
                        ->orderBy('created_at', 'desc')
                        ->take(100)
                        ->get();

            $b = Contact::where('team_id', Auth::user()->team->id)
                        ->where('source', 'call_log')
                        ->where('notes', 'like', '%'.$this->search_term.'%')
                        ->orderBy('created_at', 'desc')
                        ->take(100)
                        ->get();

            $contacts = $a->merge($b);

        }

        return view('livewire.call-log.contacts', ['contacts' => $contacts]);
    }
}
