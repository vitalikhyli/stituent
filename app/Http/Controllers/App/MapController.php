<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Voter;
use App\WorkCase;
use App\Person;
use Auth;

class MapController extends Controller
{
    use LinksTrait; 

    public function index()
    {

        if (request('top')) {
            $top = request('top');
            $left = request('left');
            $bottom = request('bottom');
            $right = request('right');

            //dd($top, $left, $bottom, $right);
            $voters = Auth::user()->people()
                            ->where('address_lat', '<', $top)
                            ->where('address_long', '>', $left)
                            ->where('address_lat', '>', $bottom)
                            ->where('address_long', '<', $right)
                            //->take(20)
                            ->get();

        } else {
            $voters = Auth::user()->people()->take(100)->get();
        }

    	//dd($voters, $top, $bottom, $left);
        $data = [];


        $by_location = $voters->groupBy('lat_long');

    	$pins = [];
    	foreach ($by_location as $latlong => $location_voters) {

            $first = $location_voters->first();

    		if ($first->address_lat == 0) {
    			continue;
    		}

            $pin = [];
            $pin['id'] = ''.$latlong;
            $pin['lat'] = ''.$first->address_lat;
            $pin['lon'] = ''.$first->address_long;
            $pin['address'] = '';
            $pin['text'] = 'Voter description, activity';
            $pin['name'] = ''.$first->just_street_num_town;

            $color = '#3b82f6';  // blue

            $household_voters = $location_voters->groupBy('household_id');

            $sections = [];

            

            foreach ($household_voters as $hh_id => $hh_voters) {

                $people_links = $this->getPersonLinks($hh_voters);
                foreach ($hh_voters as $voter) {
                    
                    if ($voter->cases()->count() > 0) {
                        $color = '#22c55e';  // green
                        if ($voter->cases()->unresolved()->count() > 0) {
                            $color = '#dc2626';  // red
                        }
                    }
                    
                }

                $othervoters = Voter::where('household_id', $hh_id)
                                    ->whereNotIn('id', $hh_voters->pluck('voter_id'))
                                    ->get();

                $voter_links = $this->getVoterLinks($othervoters);
                
                $rows = [];
                $rows[] = ['title' => 'Linked',
                       'text'  => '',
                       'links' => $people_links];

                if ($othervoters->count() > 0) {
                    $rows[] = ['title' => 'Voter File',
                               'text'  => '',
                               'links' => $voter_links];
                }


                $hh_first = $hh_voters->first();
                $sections[] = [
                    'title'    => ''.$hh_first->full_address,
                    'subtitle' => ''.($hh_voters->count() + $othervoters->count())." people",
                    'rows'     => $rows,
                ];

                
            }
            //$sections = [];

    		$pin['color'] = ''.$color;
            $pin['sections'] = $sections;
    		$pins[] = $pin;
    	}

        //dd($pins);

        $filters = [
            0 => [
                'name' =>  'Open Cases',
                'color' => '#dc2626',   // red
            ],
            1 => [
                'name' =>  'Resolved',
                'color' => '#22c55e',  // green
            ],
            2 => [
                'name' =>  'Other Activity',
                'color' => '#3b82f6',  // blue
            ],

            // red    #dc2626
            // purple #a855f7
            // orange #f59e0b
            // pink   #e879f9
        ];

        $params = [
            0 => [
                'name' => 'Date Range',
                'key'  => 'daterange',
                'values' => [
                    0 => [
                        'name' =>  'Last 30 Days',
                        'val'  => 'last30',
                    ],
                    1 => [
                        'name' =>  'This Year',
                        'val'  => 'thisyear',
                    ],
                    2 => [
                        'name' =>  'All Time',
                        'val' => 'alltime',
                    ],
                ],
            ],
            1 => [
                'name' => 'Party',
                'key'  => 'party',
                'values' => [
                    0 => [
                        'name' =>  'Dem',
                        'val'  => 'D',
                    ],
                    1 => [
                        'name' =>  'Rep',
                        'val'  => 'R',
                    ],
                ],
            ],
            2 => [
                'name' => 'Age',
                'key'  => 'age',
                'values' => [
                    0 => [
                        'name' =>  'Over 60',
                        'val'  => '60',
                    ],
                ],
            ],
        ];

        $data['filters'] = $filters;
        $data['params'] = $params;
        $data['pins'] = $pins;

        

    	return json_encode($data);
    }

}
