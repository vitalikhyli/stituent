<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Voter;
use App\VoterMaster;
use App\Person;
use Auth;

class SearchController extends Controller
{
    protected $start_time;
    protected $data;

    public function __construct()
    {
        $this->start_time = microtime(-1);
        $this->data = [];
    }

    public function index()
    {
         

        if (!request('q')) {
            $voters = $this->votersQuery()->take(20); 
            $people_ids = Person::where('team_id', Auth::user()->team_id)
                                ->whereNotNull('voter_id')
                                ->latest('updated_at')
                                ->take(10)
                                ->pluck('voter_id');
            $voters->whereIn('id', $people_ids);
            $voters = $voters->get();
            $this->addSection('last_name', $voters);
            return $this->mobileJson();
        }
		/*

			Lookup options:
			* last name
			* first name
			* address
			* last name, city
			* first name, city
			* street

            $data return:
            [
                recent:    [],  // people -- cases, contacts, groups, etc
                linked:    [],  // people
                last_name: [],  // 
                first_name:[],
                address:   [],
                city:      [],  // 
                groups:    [],  // people in group name
                cases:     [],  // people in matching cases
                contacts:  [],  // people in matching contacts
            ]

		*/

        // =======================================> RECENT
        // =======================================> LINKED


        // =======================================> LASTNAME

        $voters = $this->votersQuery()->take(20);              

        if (strlen(request('q')) == 1) {
            $people_ids = Person::where('team_id', Auth::user()->team_id)
                                ->where('last_name', 'LIKE', request('q').'%')
                                ->whereNotNull('voter_id')
                                ->take(100)
                                ->pluck('voter_id');
            $voters->whereIn('id', $people_ids);
            //dd($people_ids);
        } else {
            $split = explode(' ', request('q'));
            if (count($split) > 1) {
                $first = $split[1];
                $last = $split[0];
                $voters->where('last_name', 'LIKE', $last."%");
                $voters->where('first_name', 'LIKE', $first."%");
            } else {
                $voters->where('last_name', 'LIKE', request('q')."%");
            }
            
        }

        if (strlen(request('q')) > 2) {
            $voters->orderBy('last_name')
                   ->orderBy('first_name');
        }

        $voters = $voters->get();
        $this->addSection('last_name', $voters);



        // =======================================> HYPHENATED

        $split = explode(' ', request('q'));
        if (count($split) > 1) {
            $voters = $this->votersQuery()->take(20); 

            $first = $split[0];
            $last = $split[1];
            $voters->where('last_name', 'LIKE', '%-'.$last."%");
            $voters->where('first_name', 'LIKE', $first."%");

            
            $voters = $voters->get();
            $this->addSection('hyphenated_name', $voters);
        }
        
        // =======================================> FIRSTNAME

        $voters = $this->votersQuery()->take(20);                 

        if (strlen(request('q')) == 1) {
            $people_ids = Person::where('first_name', 'LIKE', request('q').'%')
                                ->whereNotNull('voter_id')
                                ->take(100)
                                ->pluck('voter_id');
            $voters->whereIn('id', $people_ids);
            //dd($people_ids);
        } else {
            $split = explode(' ', request('q'));
            if (count($split) > 1) {
                $first = $split[0];
                $last = $split[1];
                $voters->where('last_name', 'LIKE', $last."%");
                $voters->where('first_name', 'LIKE', $first."%");
            } else {
                $voters->where('first_name', 'LIKE', request('q')."%");
            }
            
        }

        if (strlen(request('q')) > 2) {
            $voters->orderBy('last_name')
                   ->orderBy('first_name');
        }

        $voters = $voters->get();
        $this->addSection('first_name', $voters);
        // =======================================> ADDRESS

        if (1 === preg_match('~[0-9]~', request('q'))) {
            $voters = $this->votersQuery()->take(20); 
            $voters->where('full_address', 'LIKE', request('q').'%');
            $voters = $voters->get();
            $this->addSection('address', $voters);
        }
        // =======================================> CITY
        // =======================================> GROUPS
        // =======================================> CONTACTS
        // =======================================> CASES
		
		
		
		return $this->mobileJson();

    }
    public function show($id)
    {
    	
	    return Voter::find($id);
	    
    }
    public function recents()
    {
        $voters = $this->votersQuery()
                       ->where('id', 'LIKE', 'MA_01ST%')
                       ->take(10)
                       ->get();
        return $this->mobileJson($voters);

    }

    public function votersQuery()
    {
        return Voter::select('id', 
                           'first_name', 'last_name',
                           'dob', 'yob', 
                           'full_address',
                           'address_prefix', 'address_fraction', 
                           'address_number', 'address_street', 'address_apt',
                           'address_city', 'address_zip');
    }

    public function addSection($section, $voters)
    {
        if (count($voters) > 0) {
            $this->data[$section] = $voters;
        }
    }

    public function mobileJson()
    {
        $formatted = [];
        $count = 0;
        foreach ($this->data as $section => $voters) {
            $one_section = [];
            foreach ($voters as $ind => $voter) {
                $voter_temp = [];
                foreach ($voter->getAttributes() as $attr => $val) {
                    if ($attr == 'id') {
                        $voter_temp[$attr] = ''.$val;
                    } else if ($voter->$attr) {
                        $voter_temp[$attr] = ''.ucwords(strtolower($val));
                    } else {
                        $voter_temp[$attr] = '';
                    }
                }
                $voter_temp['age'] = "".$voter->age;
                $voter_temp['street'] = "".$voter->address_line_street;
                $one_section[] = $voter_temp;
                $count++;
            }
            //return $one_section;
            $formatted[$section] = $one_section;
        }
        //dd($voters);

        $complete = [];
        $complete['results'] = $formatted;
        $complete['time'] = number_format(microtime(-1) - $this->start_time, 2);
        $complete['count'] = $count;
        
        return json_encode($complete);
    }
}
