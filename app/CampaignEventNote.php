<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignEventNote extends Model
{
	use SoftDeletes;
    use HasFactory;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
