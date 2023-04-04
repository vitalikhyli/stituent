<?php

namespace App\Http\Livewire\Bulkemail;

use Livewire\Component;

use App\BulkEmailQueue;

use Auth;

use Carbon\Carbon;


class Progress extends Component
{
	public $bulkemail;

    public function render()
    {
        $queue = BulkEmailQueue::where('bulk_email_id', $this->bulkemail->id);

        $total = $queue->count();

        $sent = $queue->where('sent', true)->count();

        $percentage = ($total == 0) ? 0 : round($sent / $total * 100);

        $oldest_processing = $queue->get()->min('processing_start');

        $done = false;
        if (Carbon::parse($oldest_processing)->diffInDays() > 7) $done = true;
        if ($percentage == 100) $done = true;

        return view('livewire.bulkemail.progress', [
        											'percentage' => $percentage,
        											'done' => $done,
        											'total' => $total,
        										   ]);
    }
}
