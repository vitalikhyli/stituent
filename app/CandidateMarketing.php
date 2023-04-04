<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CandidateMarketing extends Model
{
    protected $connection = 'main';
    protected $table = 'candidate_marketing';

    public function __construct($candidate_id = null)
    {
        parent::__construct();

        $this->candidate_id = $candidate_id;
    }
}
