<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VoterMaster;
use Carbon\Carbon;
use App\Voter;
use App\Person;
use Auth;

class DashboardController extends Controller
{
    use LinksTrait;

	public $page;

    public function index()
    {

    	if (request('next')) {
    		if (request('next') == 'bigBirthdays') {
    			return $this->bigBirthdays();
    		}
    		if (request('next') == 'birthdays') {
    			return $this->birthdays();
    		}
    	}

        // ===========================================> LAST 5 NOTES

        $recent_notes = Auth::user()->contacts()
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();

        $grouped = $recent_notes->groupBy('date_readable');

        $rows = [];
        foreach ($grouped as $groupdate => $notes) {

            $links = $this->getNotesLinks($notes);

            $rows[] = ['title' => $groupdate,
                       'text'  => '',
                       'links' => $links];
                    
        }
        

        // ===========================================> LAST 5 UPDATED CASES


        $recent_cases = Auth::user()->cases()
                             ->orderBy('updated_at')
                             ->take(100)
                             ->get()
                             ->sortByDesc('last_activity_date_sort')
                             ->take(5);

        //dd($cases);
        $grouped = $recent_cases->groupBy('last_activity_month');

        $case_rows = [];
        foreach ($grouped as $groupdate => $cases) {

            $links = $this->getCasesLinks($cases);

            $case_rows[] = ['title' => $groupdate,
                       'text'  => '',
                       'links' => $links];
                    
        }

        



        // ===========================================> LAST 5 UPDATED GROUPS

        // $recent_cases = Auth::user()->cases()
     //                                ->orderBy('created_at', 'desc')
     //                                ->take(5)
     //                                ->get();
        $recent_groups = Auth::user()->groupPerson()
                                     ->with('group')
                                     ->selectRaw('group_id, MAX(updated_at) as ud')
                                     ->orderByDesc('ud')
                                     ->groupBy('group_id')
                                     ->take(5)
                                     ->get();
                             
        //dd($recent_groups);
        //dd($cases);
        $grouped = $recent_groups->groupBy('ud');
        //dd($grouped);
        $group_rows = [];
        foreach ($grouped as $groupdate => $gps) {

            $groupdate = Carbon::parse($groupdate)->format('n/j/Y');

            $links = $this->getGroupPersonLinks($gps);

            $group_rows[] = ['title' => $groupdate,
                       'text'  => '',
                       'links' => $links];
                    
        }

    	$data = [
    		'header'   => 'Dashboard',
    		'next_url' => 'app/dashboard?next=bigBirthdays',
    		'sections' => [
    			0 => [
                    'title'    => '',
                    'subtitle' => 'Welcome to the App',
                    'rows'     => [
                        0 => ['title' => 'February 2023',
                                   'text'  => "Please contact Peri at 617.699.4553 if you have any questions or come across any issues.",
                                   'links' => []],
                        ],
                ],
                1 => [
                    'title'    => 'Last 5 Notes',
                    'subtitle' => '',
                    'rows'     => $rows,
                ],
                2 => [
                    'title'    => 'Last 5 Updated Cases',
                    'subtitle' => '',
                    'rows'     => $case_rows,
                ],
                3 => [
                    'title'    => 'Last 5 Updated Groups',
                    'subtitle' => '',
                    'rows'     => $group_rows,
                ],
    		],
    	];
        //dd("Laz");
    	return json_encode($data);
    }
    public function birthdays()
    {
    	$date = Carbon::today();
    	if (request('date')) {
    		$date = Carbon::parse(request('date'));
    	}
    	$voter_rows = $this->getBirthdayRows(Voter::query(), $date);

    	$next = 'app/dashboard?next=birthdays&date='.$date->clone()->addDay()->format('Y-m-d');

    		
		if ($this->page) {
    		$next = 'app/dashboard?next=birthdays&date='.$date->format('Y-m-d').'&page='.($this->page+1);
    	}
		$data = [
    		'header'   => '',
    		'next_url' => $next,
    		'sections' => [
	    		0 => [
	    			'title'    => $date->format('D n/j').' Birthdays',
	    			'subtitle' => ($this->page == 1 ? 'All birthdays in the district on '.$date->format('l n/j/Y') : ''),
	    			'rows'     => $voter_rows,
	    		],
    		],
    	];


    	return json_encode($data);
    }
    public function bigBirthdays()
    {
    	
    	
    	$voter_rows = $this->getBigBirthdayRows(Voter::query(), 'idx_master_birthday', null);

    	$next = 'app/dashboard?next=birthdays';
    	if (request('page')) {
    		
    		if ($this->page) {
	    		$next = 'app/dashboard?next=bigBirthdays&page='.($this->page+1);
	    	}
    		$data = [
	    		'header'   => 'Upcoming Big Birthdays',
	    		'next_url' => $next,
	    		'sections' => [
		    		0 => [
		    			'title'    => 'Upcoming Big Birthdays (cont.)',
		    			'subtitle' => '',
		    			'rows'     => $voter_rows,
		    		],
	    		],
	    	];
    	} else {

    		if ($this->page) {
	    		$next = 'app/dashboard?next=bigBirthdays&page='.($this->page+1);
	    	}
    		$person_rows = $this->getLinkedBirthdayRows();

	    	$data = [
	    		'header'   => 'Upcoming Big Birthdays'.($this->page ? ' (cont.)': ''),
	    		'next_url' => $next,
	    		'sections' => [
	    			0 => [
		    			'title'    => 'Linked Birthdays',
		    			'subtitle' => 'People who have contacted the office with upcoming birthdays',
		    			'rows'     => $person_rows,
		    		],
		    		1 => [
		    			'title'    => 'Upcoming Big Birthdays (district)',
		    			'subtitle' => 'Birthdays in the next few days on the big 10s',
		    			'rows'     => $voter_rows,
		    		],
	    		],
	    	];
    	}

    	return json_encode($data);

    }
    public function getLinkedBirthdayRows()
    {
    	$date = Carbon::today();
    	if (request('date')) {
    		$date = Carbon::parse(request('date'));
    	}
    	$year = Carbon::today()->format('Y');
    	//dd($year); 
    	$year_interval = 10;
    	$constituent_count = Auth::user()->team->constituents_count;

	    $numdays = 7;
	    $birthday = $date->format('m-d');
    	$birthdays_arr = [];
    	for ($b = 0; $b < $numdays; $b++) {
    		$birthdays_arr[] = "'".$date->clone()->addDays($b)->format('m-d')."'";
    	}
    	$birthdays = implode(', ', $birthdays_arr);
	    $count = Person::where('team_id', Auth::user()->team_id)
    					       ->selectRaw("
				    	id,full_name, full_address, address_street, address_number, address_apt, address_city, dob, TIMESTAMPDIFF(YEAR, dob, ('$date' + INTERVAL + $numdays DAY)) AS age")
    				   ->whereRaw("birthday IN ($birthdays) and archived_at is null and dob < '2020-01-01'")
    				   ->useIndex('people_birthday_index')->count();
    	if ($count < 5) {
    		$numdays = 30;
    	}
        if ($count > 50) {
            $numdays = 2;
        }
	    

    	$birthday = $date->format('m-d');
    	$birthdays_arr = [];
    	for ($b = 0; $b < $numdays; $b++) {
    		$birthdays_arr[] = "'".$date->clone()->addDays($b)->format('m-d')."'";
    	}
    	$birthdays = implode(', ', $birthdays_arr);
    	//dd($birthdays);
    	
    	$big_birthdays = Person::where('team_id', Auth::user()->team_id)
    					       ->selectRaw("
				    	id,full_name, full_address, address_street, address_number, address_apt, address_city, dob, TIMESTAMPDIFF(YEAR, dob, ('$date' + INTERVAL + $numdays DAY)) AS age")
    				   ->whereRaw("birthday IN ($birthdays) and archived_at is null and dob < '2020-01-01'")
    				   ->useIndex('people_birthday_index');


    	if (request('debug')) {
	    	dd($big_birthdays->count(), $big_birthdays->toSql());
	    }

    	$people = $big_birthdays->orderBy('address_city')
    							   ->get();

    	//dd($grouped);


    	$grouped = $people->groupBy('address_city');

    	$rows = [];
    	foreach ($grouped as $groupname => $voters) {
    		
            $links = $this->getVoterBirthdayLinks($voters);

			$rows[] = [
				'title' => $groupname,
				'text'  => '',
				'links' => $links,
			];
		}
		return $rows;
  
    }
    public function getBigBirthdayRows($query, $index, $numdays)
    {
    	$date = Carbon::today();
    	if (request('date')) {
    		$date = Carbon::parse(request('date'));
    	}
    	$year = Carbon::today()->format('Y');
    	//dd($year); 
    	$year_interval = 10;
    	$constituent_count = Auth::user()->team->constituents_count;
    	if (!$numdays) {
	    	$numdays = 7;
	    	if ($constituent_count > 100000) {
		    	$numdays = 3;
		    }
		    if ($constituent_count > 1000000) {
		    	$numdays = 1;
		    }
		}

    	$birthday = $date->format('m-d');
    	$birthdays_arr = [];
    	for ($b = 0; $b < $numdays; $b++) {
    		$birthdays_arr[] = "'".$date->clone()->addDays($b)->format('m-d')."'";
    	}
    	$birthdays = implode(', ', $birthdays_arr);
    	//dd($birthdays);
    	
    	$big_birthdays = $query->selectRaw("
				    	id,full_name, full_address, address_street, address_number, address_apt, address_city, dob, TIMESTAMPDIFF(YEAR, dob, ('$date' + INTERVAL + $numdays DAY)) AS age")
    				   ->whereRaw("birthday IN ($birthdays) and MOD($year - YEAR(dob), ".$year_interval.") = 0 and archived_at is null and dob < '2020-01-01'")
    				   ->useIndex($index);


    	if (request('debug')) {
	    	dd($big_birthdays->count(), $big_birthdays->toSql());
	    }

    	$paginated = $big_birthdays->orderBy('address_city')
    							   ->paginate(100);

    	$this->page = null;
    	if ($paginated->hasMorePages()) {
    		$this->page = $paginated->currentPage();
    	}
    	//dd($grouped);


    	$grouped = $paginated->groupBy('address_city');

    	$rows = [];
    	foreach ($grouped as $groupname => $voters) {

    		$links = $this->getVoterBirthdayLinks($voters);
			
            $rows[] = [
				'title' => $groupname,
				'text'  => '',
				'links' => $links,
			];
		}
		return $rows;
    }
    public function getBirthdayRows($query, $date)
    {


    	$birthday = $date->format('m-d');
    	
    	$birthdays = $query->selectRaw("
				    	id,full_name, full_address, address_street, address_number, address_apt, address_city, dob, TIMESTAMPDIFF(YEAR, dob, ('$date')) AS age")
    				   ->whereRaw("birthday = '$birthday' and archived_at is null and dob < '2020-01-01'")
    				   ->useIndex('idx_master_birthday');


    	if (request('debug')) {
	    	dd($birthdays->count(), $birthdays->toSql());
	    }

    	$paginated = $birthdays->orderBy('address_city')
    							   ->paginate(100);

    	$this->page = null;
    	if ($paginated->hasMorePages()) {
    		$this->page = $paginated->currentPage();
    	}
    	//dd($grouped);


    	$grouped = $paginated->groupBy('address_city');

    	$rows = [];
    	foreach ($grouped as $groupname => $voters) {
    		$this->getVoterBirthdayLinks($voters);
			$rows[] = [
				'title' => $groupname,
				'text'  => '',
				'links' => $links,
			];
		}
		return $rows;
    }
}
