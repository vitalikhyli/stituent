<?php

namespace App\Http\Livewire\Bulkemail;

use Livewire\Component;

use App\BulkEmailQueue;

use Auth;

use Carbon\Carbon;


class Processing extends Component
{
    public function render()
    {

		$processing = BulkEmailQueue::where('team_id', Auth::user()->team->id)
									->whereDate('processing_start', '>=', Carbon::now()->subDays(7))
									->where('processing', true)->where('sent', false)->count();

        return view('livewire.bulkemail.processing', [
        												'processing' => $processing
        											 ]);
    }
}
