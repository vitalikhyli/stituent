<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserUploadData extends Model
{
    protected $table = 'user_uploads_data';

    protected $casts = [
            'data'  => 'array',
        ];

    public function voter()
    {
        return $this->hasOne(Voter::class, 'id', 'voter_id');
    }

    public function participant()
    {
        return $this->hasOne(Participant::class, 'id', 'participant_id');
    }

    public function person()
    {
        return $this->hasOne(Person::class, 'id', 'person_id');
    }
}
