<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        [
        	'slug' => 'shared-cases',
            'name' => 'Shared Cases',
            'vimeo_id' => '473191108',
            'length' => '8m',
            'description' => 'In this video we explore a new feature that allows for cross-account collaboration on cases between offices and individuals.',
            'thumb' => null,
        ],
    ];

    protected $schema = [
        'vimeo_id' => 'unsignedInteger',
    ];
}
