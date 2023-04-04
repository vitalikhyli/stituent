<?php

namespace App\Http\Livewire\CallLog;

use Livewire\Component;

use App\CallLogViewModel;

use Auth;


class Report extends Component
{
    public function render()
    {
        $call_log = new CallLogViewModel(Auth::user());

        return view('livewire.call-log.report', ['call_log' => $call_log]);
    }
}
