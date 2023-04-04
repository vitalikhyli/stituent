<?php

namespace App;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoterSliceCount extends Model
{
    // use HasFactory;

    protected $table = 'voter_slices_counts';

    protected $casts = [
    		'slice'  				=> 'array',
            'municipalities'  		=> 'array',
            'counties' 				=> 'array',
            'congress_districts'  	=> 'array',
            'house_districts'  		=> 'array',
            'senate_districts'  	=> 'array',
        ];
}
