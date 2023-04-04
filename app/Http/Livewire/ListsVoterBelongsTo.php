<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Voter;

class ListsVoterBelongsTo extends Component
{
	public $voter;

	public $readyToLoad = false;

    public function loadPosts()
    {
        $this->readyToLoad = true;
    }

	public function mount($voter)
	{
		$this->voter = $voter;
	}

    public function render()
    {
		// ini_set('memory_limit','2047M');

    	$lists = ($this->readyToLoad) ? $this->voter->listsTheyBelongTo() : collect([]);

        return view('livewire.lists-voter-belongs-to', compact('lists'));

    }
}
