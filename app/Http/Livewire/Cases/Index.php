<?php

namespace App\Http\Livewire\Cases;

use Livewire\Component;

use App\WorkCase;
use App\GroupPerson;
use App\WorkCasePerson;
use App\User;
use App\Municipality;
use App\Person;
use App\EntityCase;

use Auth;
use DB;

use App\Traits\ExportTrait;
use Carbon\Carbon;

use Livewire\WithPagination;


class Index extends Component
{
	use ExportTrait;
    use WithPagination;

	public $search;
	public $owner;
	public $status= [];
	public $type;
    public $subtype;
	public $group;
	public $city;
	public $priority = [];
	public $user;
	public $resolvedStart;
	public $resolvedEnd;
	public $openedStart;
	public $openedEnd;
	public $reportShowNotes;
	public $available_cities;
    public $orgs_only;

    protected $paginationTheme = 'bootstrap';


	public function export()
    {
        $cases = $this->getCases();



        $cases = $cases->each(function ($item) {
            $people = [];
            foreach($item->people as $person) {
                $people[] = $person->full_name
                            .' ('
                            .$person->address_city
                            .')';
            }
            $item['constituents'] = (!empty($people)) ? implode(', ', $people) : null;
        });

        $cases = $cases->map(function ($item) {
            return collect($item->toArray())
                ->only(['user_id', 'date', 'priority', 'subject', 'notes', 'resolved_date', 'closing_remarks', 'status', 'type', 'constituents'])
                ->all();
        });

        $cases = $cases->map(function ($item) {
                    $user = User::find($item['user_id']);
                    $collection = $item;
                    $collection['user_id'] = $user->shortName;
                    return $collection;
                });

        return $this->createCSV($cases);
    }
    
	public function restorePresets()
	{
		$this->search 			= null;
		$this->owner 			= 'team';
        $this->status           = ['open', 'held']; //, 'resolved'];
		$this->type             = null;
        $this->subtype          = null;
		$this->group 			= null;
		$this->priority 		= ['none', 'high', 'medium', 'low'];
		$this->user 			= null;
		$this->resolvedStart	= null;
		$this->resolvedEnd		= null;
		$this->openedStart		= null;
		$this->openedEnd		= null;
	}

	public function updatedOwner()
	{
		if($this->owner == 'team') {

			$this->user = null;

		} else {

			if ($this->owner == 'mine') $this->user = Auth::user()->id;

		}
	}

	public function updatedUser()
	{
		if(!$this->user) $this->owner = 'team';
		if ($this->user == Auth::user()->id) $this->owner = 'mine';
	}

	public function mount()
	{
		$this->restorePresets();

		$this->reportShowNotes = true;


    	$person_ids = WorkCasePerson::where('team_id', Auth::user()->team->id)
						            ->pluck('person_id');
        $team_cities_ids = Person::whereIn('id', $person_ids)
        						  ->pluck('city_code')
        						  ->unique();
        $this->available_cities = Municipality::whereIn('code', $team_cities_ids)
        								->where('state', session('team_state'))
        								->orderBy('name')
        								->get();

	}

    public function getCases($options = null)
    {
    	$cases = WorkCase::where('team_id', Auth::user()->team->id);

    	if ($this->search) {
    		$cases = $cases->where(function ($q) {
    							$q->orWhere('subject', 'like', '%'.$this->search.'%');
    							$q->orWhere('notes', 'like', '%'.$this->search.'%');
    						});
    	}

    	if ($this->owner == 'mine') {
			$cases = $cases->where('user_id', Auth::user()->id);
    	}

    	if ($this->owner == 'team') {
			$cases = $cases->where(function ($q) {
    							$q->orWhere('private', false);
    							$q->orWhere('user_id', Auth::user()->id);
    						});
    	}

		$cases = $cases->where(function ($q) {
							if (in_array('open', $this->status)) $q->orWhere('status', 'open');
							if (in_array('held', $this->status)) $q->orWhere('status', 'held');
							if (in_array('resolved', $this->status)) $q->orWhere('status', 'resolved');
							if (!$this->status) $q->orWhere('status', 'no_status_at_all');
						});

    	if ($this->type) {
            $cases = $cases->where('type', $this->type);
        }

        if ($this->subtype) {
            $cases = $cases->where('subtype', $this->subtype);
        }
        if ($this->orgs_only) {
            $case_ids = EntityCase::where('team_id', Auth::user()->team_id)
                                  ->pluck('case_id');
            $cases = $cases->whereIn('id', $case_ids);
        }
        if ($this->city) {
        	$people_ids = Person::where('city_code', $this->city)
        						->where('team_id', Auth::user()->team->id)
        						->pluck('id');
        	$case_ids = WorkCasePerson::whereIn('person_id', $people_ids)
							          ->pluck('case_id')
							          ->unique();
            $cases = $cases->whereIn('id', $case_ids);
        }

		$cases = $cases->where(function ($q) {
							if (in_array('high', $this->priority)) $q->orWhere('priority', 'High');
							if (in_array('medium', $this->priority)) $q->orWhere('priority', 'Medium');
							if (in_array('low', $this->priority)) $q->orWhere('priority', 'Low');
							if (in_array('none', $this->priority)) $q->orWhere('priority', null);
						});

		if (empty($this->priority)) $cases = $cases->where('priority', 'no_priority_set');

    	if ($this->group) {
    		$group_people = GroupPerson::where('group_id', $this->group)
    								   ->pluck('person_id')
    								   ->toArray();
    		if ($group_people) {
    			$group_cases = WorkCasePerson::whereIn('person_id', $group_people)
    									 ->pluck('case_id')
    									 ->toArray();

    			if ($group_cases) {
    				$cases = $cases->whereIn('id', $group_cases);
    			}
    		}

    		if (!$group_people || !$group_cases) {
				$cases = $cases->where('id', 'nothing_for_this_group_so_return_nothing');
    		}

    	}

    	if ($this->user) {
			$cases = $cases->where('user_id', $this->user);
    	}

    	if ($this->openedStart) {
    		try {
    			$cases = $cases->whereDate('date', '>=', Carbon::parse($this->openedStart)->toDateString());
    		} catch (\Exception $e) {
    			// Date not in right format yet
    		}
    	}

    	if ($this->openedEnd) {
    		try {
    			$cases = $cases->whereDate('date', '<=', Carbon::parse($this->openedEnd)->toDateString());
    		} catch (\Exception $e) {
    			// Date not in right format yet
    		}	
    	}

    	if ($this->resolvedStart) {
    		try {
    			$cases = $cases->whereDate('updated_at', '>=', Carbon::parse($this->resolvedStart)->toDateString())->where('status', 'resolved');
    		} catch (\Exception $e) {
    			// Date not in right format yet
    		}	
    	}
    	
    	if ($this->resolvedEnd) {
    		try {
    			$cases = $cases->whereDate('updated_at', '<=', Carbon::parse($this->resolvedEnd)->toDateString())->where('status', 'resolved');
    		} catch (\Exception $e) {
    			// Date not in right format yet
    		}	
    	}

    	if ($options =='unpaginated') {

            $cases = $cases->get();
            return $cases;

    	} else {

            $perpage = 500;
            if (request('perpage')) {
                $perpage = request('perpage');
            }
            $cases = $cases->orderByDesc('created_at')->paginate($perpage);

        }

        if ($options =='get_group_people_too') {

    		return [
    				'cases' => $cases,
    				'group_people' => (isset($group_people)) ? $group_people : []
    			   ];

    	} else {

            return $cases;

        }

    }

	public function synchronizeResolvedProperties()
	{
		if ($this->resolvedStart || $this->resolvedEnd) {
			$status = $this->status;
			$status[] = 'resolved';
			$this->status = collect($status)->unique()->toArray();
		}
	}


    public function render()
    {
    	$this->synchronizeResolvedProperties();

    	//////////////////////////////////////////////////////////////////////////////////////

    	$data = $this->getCases($options = 'get_group_people_too');
    	$cases = $data['cases'];
    	$group_people = $data['group_people'];

    	//////////////////////////////////////////////////////////////////////////////////////

		$case_types = WorkCase::StaffOrPrivateAndMine()
                      ->where('team_id', Auth::user()->team->id)
                      ->select('type')
                      ->whereNotNull('type')
                      ->groupBy('type')
                      ->orderBy('type')
                      ->pluck('type');

        $case_subtypes = WorkCase::StaffOrPrivateAndMine()
                      ->where('team_id', Auth::user()->team->id)
                      ->select('subtype')
                      ->whereNotNull('subtype')
                      ->groupBy('subtype')
                      ->orderBy('subtype')
                      ->pluck('subtype');

		$available_groups = Auth::user()->team->groups()->orderBy('category_id')->orderBy('name')->get();

		if (!isset($group_people)) $group_people = [];

		$unresolved_cases = WorkCase::where('team_id', Auth::user()->team->id)
								    ->where('status', '!=', 'resolved');
		if ($this->user) {
			$unresolved_cases = $unresolved_cases->where('user_id', $this->user);
		}
		$unresolved_cases_count = $unresolved_cases->count();
		$unresolved_cases = $unresolved_cases->orderBy('created_at')->take(5)->get();

		$all_cases_count = WorkCase::where('team_id', Auth::user()->team->id)->count();

        $cases_unpaginated = $this->getCases($options = 'unpaginated');

        $available_cities = Municipality::where('state', session('team_state'))
        								->orderBy('name')
        								->get();

		
		//////////////////////////////////////////////////////////////////////////////////////

		//////////////////////////////////////////////////////////////////////////////////////

		$report_json = base64_encode(
						json_encode(
							collect(['ids' => $cases->pluck('id'), 
									 'notes' => $this->reportShowNotes])
						   )
					   );

		//////////////////////////////////////////////////////////////////////////////////////

        return view('livewire.cases.index', ['cases' 					=> $cases,
                                             'cases_unpaginated'        => $cases_unpaginated,
    										 'case_types'               => $case_types,
                                             'case_subtypes'            => $case_subtypes,
    										 'available_groups' 		=> $available_groups,
    										 // 'available_cities' 		=> $available_cities,
    										 'group_people' 		 	=> collect($group_people),
    										 'all_cases_count'			=> $all_cases_count,
    										 'unresolved_cases_count'	=> $unresolved_cases_count,
    										 'unresolved_cases' 		=> $unresolved_cases,
    										 'report_json' 				=> $report_json
    										]);
    }

    public function paginationView()
    {
        return 'livewire.list-paginate-links-old-tailwind';
    }

}
