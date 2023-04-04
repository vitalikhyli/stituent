<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Municipality;
use Carbon\Carbon;
use App\Voter;
use Auth;

use App\Traits\ExportTrait;


class Birthdays extends Component
{
	use WithPagination;
	use ExportTrait;

	public $editing;

	public $year;
	public $date;
	public $year_interval = 5;
	public $sort_by = 'dob';
	public $past_birthdays = false;
	public $show = 500;
	public $municipality;
	public $linked = "";

	protected $queryString = [
		'year_interval' => ['except' => 5], 
		'sort_by' => ['except' => 'dob'],
		'past_birthdays' => ['except' => false],
		'show' => ['except' => 500],
		'municipality' => ['except' => ''],
		'linked' => ['except' => ''],
		'date', 
	];

	public function __construct()
	{
		if (!request('date')) {
			$this->year = Carbon::today()->format('Y');
			$this->date = Carbon::today()->format('Y-m-d');
		} else {
			$this->year = Carbon::parse(request('date'))->format('Y');
			$this->date = Carbon::parse(request('date'))->format('Y-m-d');
		}
	}
	public function toggleEditing()
	{
		$this->editing = !$this->editing;
	}
	public function nextMonth()
	{
		$this->date = Carbon::parse($this->date)->addMonth()->format('Y-m-d');
	}
	public function prevMonth()
	{
		$this->date = Carbon::parse($this->date)->subMonth()->format('Y-m-d');
	}

	public function export()
	{
		$birthdays = $this->getBirthdays();
		return $this->createCSV($birthdays);
	}

	public function getBirthdays($paginate = null)
	{
		$end_of_month = Carbon::parse($this->date)->endOfMonth();
    	$start_of_month = Carbon::parse($this->date)->startOfMonth()->format('Y-m-d');
    	$this->date = $start_of_month;
    	$days_until_end_of_month = Carbon::parse($this->date)->diffInDays($end_of_month);
    	$this->year = Carbon::parse($this->date)->format('Y');
    	
    	if ($this->past_birthdays == 'false') {
    		$this->past_birthdays = false;
    	}

    	if (Carbon::parse($this->date) < Carbon::today()->startOfMonth()) {
    		$this->past_birthdays = true;
    	}
    	if (Carbon::parse($this->date) > Carbon::today()->endOfMonth()) {
    		$this->past_birthdays = false;
    	}
    	$past_birthday_sql = "DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT('$start_of_month', '%m-%d') and";
    	if (Carbon::parse($this->date)->startOfMonth() == Carbon::today()->startOfMonth()) {
	    	if ($this->past_birthdays) {
	    		$past_birthday_sql = "DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT('$start_of_month', '%m-%d') and";
	    		$this->date = $start_of_month;
	    	} else {
	    		$past_birthday_sql = "DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(NOW(), '%m-%d') and";
	    		//dd($past_birthday_sql);
	    	}
	    }

	    //dd($past_birthday_sql, $this->past_birthdays, $this->date);
    	
    	$voters = Voter::selectRaw("
				    	id,full_name, full_address, dob, TIMESTAMPDIFF(YEAR, dob, ('$this->date' + INTERVAL + $days_until_end_of_month DAY)) AS upcoming_age, DATE_FORMAT(dob, '%m-%d') as birth_date")
    				   ->whereRaw(" 
							$past_birthday_sql 
							DATE_FORMAT(dob, '%m-%d') <= DATE_FORMAT(('$this->date' + INTERVAL + $days_until_end_of_month DAY), '%m-%d')
							and MOD($this->year - YEAR(dob), ".$this->year_interval.") = 0
							and archived_at is null
							and dob < '2020-01-01'")
				->orderByRaw($this->sort_by);
		if ($this->municipality) {
			$voters->where('city_code', $this->municipality);
		}

		if ($this->linked) {
			$people_ids = Auth::user()->team->people()
                                 ->whereRaw(
                                      "DATE_FORMAT(dob, '%m-%d') <= DATE_FORMAT(('$this->date' + INTERVAL + $days_until_end_of_month DAY), '%m-%d')
										and MOD($this->year - YEAR(dob), ".$this->year_interval.") = 0")
                                 ->pluck('voter_id');
            $voters->whereIn('id', $people_ids);
            //dd($people_ids);
		}
		if ($paginate) {
			$voters = $voters->simplePaginate($this->show);
		} else {
			$voters = $voters->take($this->show)->get();	
		}

        return $voters;
	}

    public function render()
    {
    	$voters = $this->getBirthdays(true);
    	$city_codes =  Voter::withTrashed()->distinct()->pluck('city_code')->unique();
        $municipalities = Municipality::whereIn('id', $city_codes)
                                      ->orderBy('name')
                                      ->get();

        return view('livewire.birthdays', compact('voters', 'municipalities'));
    }
}
