<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Team;

class TermSigner extends Model
{
    use HasFactory;

    protected $table = 'term_signer';

    protected $casts = ['user_teams' 		=> 'array'];

    public function teams()
    {
    	if (!$this->user_teams) return [];
    	return Team::whereIn('id', $this->user_teams)->get();
    }

}
