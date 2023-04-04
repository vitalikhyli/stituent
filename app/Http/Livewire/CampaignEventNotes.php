<?php

namespace App\Http\Livewire;
use App\CampaignEventNote;
use Auth;

use Livewire\Component;

class CampaignEventNotes extends Component
{
	public $event;
	public $notes;
	public $new_note;
	public $trashed;

	public function mount($event)
	{
		$this->event = $event;
		// $this->notes = $this->event->notes
	}
	public function addNote()
	{

		$note = new CampaignEventNote;
		$note->team_id = Auth::user()->team_id;
		$note->user_id = Auth::user()->id;
		$note->campaign_event_id = $this->event->id;
		$note->content = $this->new_note;
		$note->save();

		// clear new note
		$this->new_note = null;

	}
	public function deleteNote($id)
	{
		$note = CampaignEventNote::find($id);
		$note->delete();
	}
	public function restoreNote($id)
	{
		$note = CampaignEventNote::withTrashed()->find($id);
		$note->restore();
	}
    public function render()
    {
    	//dd($this->event);

	    $this->notes = $this->event->campaignEventNotes()->latest()->get();
	    $this->trashed = $this->event->campaignEventNotes()->onlyTrashed()->get();
    	//dd($this->notes);
        return view('livewire.campaign-event-notes');
    }
}
