<?php

namespace App\Http\Livewire\Merge;

use Livewire\Component;



class MergePeople extends Component
{
	public $keep;

    public function render()
    {
        return view('livewire.merge.merge-people');
    }
}
