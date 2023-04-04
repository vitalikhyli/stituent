<?php

namespace App\Http\Livewire\CallLog;

use Livewire\Component;

use App\Contact;
use App\Person;

use Auth;


class ContactModal extends Component
{
    // https://blog.codecourse.com/clean-reusable-livewire-modals/
    
    public $show = false;
    
    public $edit = true;

    public $contact_id;
    public $contact_date;
    public $contact_subject;
    public $contact_notes;

    public $contact_just_updated_at;

    public $person_search;

    protected $listeners = [
        'set_show_to_true' => 'setShowToTrue',
    ];

    public function setShowToTrue($id)
    {
        $this->show = true;
        $this->edit = true;
        $this->contact_id       = $id;
        $this->contact_date     = null;
        $this->contact_subject  = null;
        $this->contact_notes    = null;
        $this->person_search    = null;

        $this->contact_just_updated_at    = null;
    }

    public function updatedEdit()
    {
        if ($this->edit == true) {
            $this->contact_just_updated_at = null;
        }
    }

    public function updateContact()
    {
        if ($this->contact_id) {

            $contact = Contact::find($this->contact_id);

            $contact->date = $this->contact_date;
            $contact->subject = $this->contact_subject;
            $contact->notes = $this->contact_notes;

            if ($contact->isDirty()) {
                $this->contact_just_updated_at = now();
            }

            $contact->save();

            $this->edit = false;
            $this->show = false;
            $this->emitTo('contacts', 'render');

        }

    }

    public function connectPerson($id)
    {
        if ($this->contact_id && $contact = Contact::find($this->contact_id)) {

            $person = Person::where('id', $id)->where('team_id', Auth::user()->team->id)->first();

            if ($person) {

                $contact->people()->attach($person, ['team_id' => Auth::user()->team->id]);

            }

            $this->person_search = null;

        }

    }

    public function disconnectPerson($id)
    {
        if ($this->contact_id && $contact = Contact::find($this->contact_id)) {

            $person = Person::where('id', $id)->where('team_id', Auth::user()->team->id)->first();

            if ($person) {

                $contact->people()->detach($person);

            }

        }

    }

    public function render()
    {
        $contact = null;

        if ($this->contact_id) {
            $contact = Contact::find($this->contact_id);
            $this->contact_date     = $this->contact_date ?? $contact->date->format('n/j/Y g:i A');
            $this->contact_subject  = $this->contact_subject ?? $contact->subject;
            $this->contact_notes    = $this->contact_notes ?? $contact->notes;
        }

        $people_options = collect([]);

        if($this->person_search && strlen($this->person_search) > 2) {
            $people_options = Person::where('team_id', Auth::user()->team->id)
                                    ->whereNotIn('id', $contact->people->pluck('id'))
                                    ->where('full_name', 'like', '%'.$this->person_search.'%')
                                    ->take(20)
                                    ->get();
        }

        return view('livewire.call-log.contact-modal', ['contact' => $contact, 'people_options' => $people_options]);
    }
}
