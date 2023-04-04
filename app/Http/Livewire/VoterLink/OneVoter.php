<?php

namespace App\Http\Livewire\VoterLink;

use Livewire\Component;

use App\Voter;

use App\District;
use App\Municipality;
use App\County;
use App\Participant;
use App\Person;

use Auth;

class OneVoter extends Component
{

	public $model;
	public $readyToLoad = false;
	public $searchMode;
	public $lookup;
	public $missing_data = [];

    public function loadPosts()
    {
        $this->readyToLoad = true;
    }

	public function mount($model)
	{
		$this->model = $model; // Could be either a Person or Participant ...whatever you want, man!!!!
	}

	public function linkVoter()
	{
		$model = $this->model;
		$model->voter_id = $voter_id;
		$model->save();

		$this->missing_data = [];
	}

	public function linkVoterAndImport()
	{
		$missing_data = $this->getMissingData($this->model->voter_id);

		// dd($missing_data);

		$model = $this->model;
		$model->voter_id = $voter_id;
		foreach($missing_data as $field => $value) {
			$model->$field = $value;
		}
		$model->save();

		$this->missing_data = [];
	}

	public function getMissingData($voter_id, $mode = null)
	{
		$model = $this->model;
		$import = findPersonOrImportVoter($voter_id, Auth::user()->team->id, $dontsave = true);

		$model_fields = collect($model->getAttributes());
		$import_fields = collect($import->getAttributes());

		$missing_data = [];


		foreach($import_fields as $key => $value) {
			if (isset($model_fields[$key]) && !$model_fields[$key] && ($value)) {

				//Skip certain fields
				$skip = ['household_id', 'full_address', 'voter_id'];
				if (in_array($key, $skip)) continue;

				// Do not replace only portions of address if they have an address
				if((substr($key, 0, 8) == 'address_') && ($model->full_address)) continue;

				if ($mode == 'display') {

					if ($key == 'congress_district') {
						$value = District::where('state', session('team_state'))
										 ->where('type', 'F')
										 ->where('code', $value)
										 ->first()
										 ->name;
					}
					if ($key == 'senate_district') {
						$value = District::where('state', session('team_state'))
										 ->where('type', 'S')
										 ->where('code', $value)
										 ->first()
										 ->name;
					}
					if ($key == 'house_district') {
						$value = District::where('state', session('team_state'))
										 ->where('type', 'H')
										 ->where('code', $value)
										 ->first()
										 ->name;
					}
					if ($key == 'county_code') {
						$value = County::where('state', session('team_state'))
									   ->where('code', $value)
									   ->first()
									   ->name;
					}
					if ($key == 'city_code') {
						$value = Municipality::where('state', session('team_state'))
									   		 ->where('code', $value)
									   		 ->first()
									   		 ->name;
					}

				}

				$missing_data[$key] = $value;
			}
		}


		return $missing_data;

	}

	public function pickID($voter_id)
	{
		$this->missing_data = $this->getMissingData($voter_id, $mode = 'display');

		$this->chosen_voter_id = $voter_id;

		// dd($this->model, $this->missing_data, $this->chosen_voter_id);

		$model = $this->model;
		$model->voter_id = $this->chosen_voter_id;
		$model->save();
	}

	public function unPickID()
	{
		$model = $this->model;
		$model->voter_id = null;
		$model->save();
	}

    public function render()
    {
    	if (get_class($this->model) == 'App\Participant') {
    		$already_linked_voter_ids = Participant::thisTeam()
    											   ->whereNotNull('voter_id')
    										  	   ->pluck('voter_id');
    	}

    	if (get_class($this->model) == 'App\Person') {
    		$already_linked_voter_ids = Person::thisTeam()
    										  ->whereNotNull('voter_id')
    										  ->pluck('voter_id');
    	}

    	//--------------------------------------------------------------------------------

        $words = explode(' ', trim($this->model->address_street));
        $words = array_splice($words, 0, 3); 
        if (isset($words[2])) {
            $words[2] = substr($words[2],0,1); // Only first two of last word ("Avenue" -> "A")
        }
        $the_short_address = implode(' ', $words);

        if ($the_short_address) {

	        $matches =  Voter::where('last_name', $this->model->last_name)
	                         ->where('full_address', 'LIKE', $the_short_address.'%')
	                         ->whereNotIn('id', $already_linked_voter_ids)
	                         ->get();

	        $matches2 =  Voter::where('last_name', $this->model->last_name)
	                          ->where('first_name', $this->model->first_name)
	                          ->whereNotIn('id', $already_linked_voter_ids)
	                          ->get();	

	        $matches = $matches->merge($matches2);

	    } else {

	        $matches =  Voter::where('last_name', $this->model->last_name)
	                         ->where('first_name', $this->model->first_name)
	                         ->whereNotIn('id', $already_linked_voter_ids)
	                         ->get();	    	
	    }

        $matches = $matches->each(function ($item) {
            $item->match_score = similar_text($item->full_address, $this->model->full_address);
            if (isset($item->full_name_middle)) {
            	$item->match_score += similar_text($item->full_name_middle, $this->model->full_name) * 5;
            }
        })->sortByDesc('match_score');

    	// $matches = ($this->readyToLoad) ? $matches : collect([]);

    	$results = collect([]);
		$words = explode(' ', $this->lookup);

    	if ($this->lookup) {

			if (count($words) > 1) {

	            $first_name = array_shift($words);
	            $last_name = implode(' ', $words);

	            $results = Voter::where('first_name', 'LIKE', $first_name.'%')
								->where('last_name', 'LIKE', $last_name.'%')
								->whereNotIn('id', $already_linked_voter_ids)
								->limit(20)
								->get();

			} else {

		    	$results1 = Voter::where('first_name', 'LIKE', $this->lookup.'%')
		    					 ->whereNotIn('id', $already_linked_voter_ids)
		    					 ->limit(10)
		    					 ->get();

		    	$results2 = Voter::where('last_name', 'LIKE', $this->lookup.'%')
		    					 ->whereNotIn('id', $already_linked_voter_ids)
		    					 ->limit(10)
		    					 ->get();

		    	$results = $results1->merge($results2);

			}

			

		}


        return view('livewire.voter-link.one-voter', [
        												'possible_matches' => $matches,
        												'results' => $results,
        											]);
    }
}
