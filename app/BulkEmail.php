<?php

namespace App;

use App\Search;
// use App\BulkEmailList;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class BulkEmail extends Model
{
    protected $casts = ['recipients_form' => 'array'];

    // public function emailList()
    // {
    // 	return $this->HasOne(BulkEmailList::class, 'id', 'list_id');
    // }

    public function getIsTemplateAttribute()
    {
        return (strtolower(trim(substr(trim($this->name), 0, 8))) == 'template') ? true : false;
    }

    public function search()
    {
        return $this->HasOne(Search::class, 'id', 'search_id');
    }

    public function queuedRecipients()
    {
        return $this->hasMany(BulkEmailQueue::class);
    }

    public function code()
    {
        return $this->belongsTo(BulkEmailCode::class);
    }

    public function queuedNotSentCount()
    {
        return DB::table('bulk_email_queue')
                 ->where('bulk_email_id', $this->id)
                 ->where('sent', false)
                 ->count();
    }

    public function queuedAndSentCount()
    {
        return DB::table('bulk_email_queue')
                 ->where('bulk_email_id', $this->id)
                 ->where('sent', true)
                 ->count();
    }

    public function queuedAndProcessingNotSent()
    {
        return $this->queuedRecipients()
                    ->where('processing', true)
                    ->where('test', '<>', true)
                    ->where('sent', false);
    }

    public function queuedAndProcessing()
    {
        return $this->queuedRecipients()
                    ->where('test', '<>', true)
                    ->where('processing', true);
    }

    public function queuedCount()
    {
        return DB::table('bulk_email_queue')
                 ->where('bulk_email_id', $this->id)
                 ->where('test', '<>', true)
                 ->count();
    }

    public function status()
    {
        if ($this->queued) {
            if ($this->completed_at != null) {
                return 'Completed '.Carbon::parse($this->completed_at)->diffForHumans();
            } else {
                return 'Queued and sending';
            }
        } else {
            return 'Draft mode';
        }
    }
}
