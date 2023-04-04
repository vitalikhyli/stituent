<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    public function signers()
    {
        // return $this->belongsToMany(User::class)->withPivot('user_name', 'accepted_at');
        return $this->hasMany(TermSigner::class, 'term_id', 'id')->orderBy('accepted_at', 'desc');
    }
}
