<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkEmailQueue extends Model
{
    protected $table = 'bulk_email_queue';

    public function markAsSent()
    {
        $this->sent = true;
        $this->save();
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function markAsProcessing()
    {
        $this->processing = true;
        $this->processing_start = now();
        $this->save();
    }

    public function noLongerProcessing()
    {
        $this->processing = false;
        $this->processing_start = null;
        $this->save();
    }

    public function bulkEmail()
    {
        return $this->belongsTo(BulkEmail::class);
    }
}
