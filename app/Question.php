<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function assignedToUser()
    {
        return User::find($this->assigned_to);
    }
}
