<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CandidateContact extends Model
{
    protected $connection = 'main';
    protected $casts = ['clicks' => 'array',
                        'to_emails' => 'array',
                        ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
