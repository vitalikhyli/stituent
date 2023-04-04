<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Entity;

class OrganizationsController extends Controller
{
    use LinksTrait;

    public function index()
    {
    	$allorgs = Entity::where('team_id', Auth::user()->team_id)
    				  ->orderBy('type')
    				  ->get();
    	$grouped = $allorgs->groupBy('type');
    	
    	$rows = [];
    	foreach ($grouped as $type => $orgs) {
    		$orgs = $orgs->sortBy('name');

    		$links = $this->getOrganizationsLinks($orgs);

    		$rows[] = [
    			'title' 	=> ($type ? $type : 'No Type'),
    			'links'		=> $links,
    			'text'		=> "",
    		];
    	}

    	$sections[] = [
    		'title' => $allorgs->count().' Organizations',
    		'subtitle' => '',
    		'rows' => $rows,
    	];

        $data = [
    		'header'   => 'Organizations',
    		'next_url' => '',
    		'sections' => $sections,
    	];

    	return json_encode($data);
    }

    public function show($id)
    {

        $organization = Entity::find($id);

        $people_links = $this->getPersonLinks($organization->people);
        $cases_links = $this->getCasesLinks($organization->cases);
          

        $data = [
            'header' => $organization->name,
            'next_url' => '',
            'sections' => [
                0 => [
                    'title'    => $organization->name,
                    'subtitle' => ''.$organization->type,
                    'rows'     => [
                        0 => [
                            'title'  => 'Description',
                            'text'   => ''.$organization->description,
                            'links'  => '',
                        ],
                        1 => [
                            'title'  => 'Address',
                            'text'   => ''.$organization->address,
                            'links'  => '',
                        ],
                        2 => [
                            'title'  => 'People',
                            'text'   => '',
                            'links'  => $people_links,
                        ],
                        3 => [
                            'title'  => 'Cases',
                            'text'   => '',
                            'links'  => $cases_links,
                        ],
                    ]
                ],
            ],
        ];
        return json_encode($data);
    }
}
