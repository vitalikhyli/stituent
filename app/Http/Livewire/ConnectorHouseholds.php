<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Person;
use App\Voter;

use App\WorkCase;


use Auth;


class ConnectorHouseholds extends Component
{
	public $model; 			// For now, only a Case -- could extend later
	public $search;
	public $createNew;
	public $revised_search;
	public $guess;

	public $editing = false;
	public $details = true;

	//////////////////////////////////////////////////////////////////////////////////////////

	private $stateAbbreviations = ['AK', 'AL', 'AR', 'AS', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'GU', 'HI', 'IA', 'ID', 'IL', 'IN', 'KS', 'KY', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO', 'MP', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM', 'NV', 'NY', 'OH', 'OK', 'OR', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UM', 'UT', 'VA', 'VI', 'VT', 'WA', 'WI', 'WV', 'WY'];

	private $streetSuffixes = [
						'ALLEY' => 'ALY',
						'ANNEX' => 'ANX',
						'ARCADE' => 'ARC',
						'AVENUE' => 'AVE',
						'BAYOU' => 'YU',
						'BEACH' => 'BCH',
						'BEND' => 'BND',
						'BLUFF' => 'BLF',
						'BOTTOM' => 'BTM',
						'BOULEVARD' => 'BLVD',
						'BRANCH' => 'BR',
						'BRIDGE' => 'BRG',
						'BROOK' => 'BRK',
						'BURG' => 'BG',
						'BYPASS' => 'BYP',
						'CAMP' => 'CP',
						'CANYON' => 'CYN',
						'CAPE' => 'CPE',
						'CAUSEWAY' => 'CSWY',
						'CENTER' => 'CTR',
						'CIRCLE' => 'CIR',
						'CLIFFS' => 'CLFS',
						'CLUB' => 'CLB',
						'CORNER' => 'COR',
						'CORNERS' => 'CORS',
						'COURSE' => 'CRSE',
						'COURT' => 'CT',
						'COURTS' => 'CTS',
						'COVE' => 'CV',
						'CREEK' => 'CRK',
						'CRESCENT' => 'CRES',
						'CROSSING' => 'XING',
						'DALE' => 'DL',
						'DAM' => 'DM',
						'DIVIDE' => 'DV',
						'DRIVE' => 'DR',
						'ESTATES' => 'EST',
						'EXPRESSWAY' => 'EXPY',
						'EXTENSION' => 'EXT',
						'FALL' => 'FALL',
						'FALLS' => 'FLS',
						'FERRY' => 'FRY',
						'FIELD' => 'FLD',
						'FIELDS' => 'FLDS',
						'FLATS' => 'FLT',
						'FORD' => 'FOR',
						'FOREST' => 'FRST',
						'FORGE' => 'FGR',
						'FORK' => 'FORK',
						'FORKS' => 'FRKS',
						'FORT' => 'FT',
						'FREEWAY' => 'FWY',
						'GARDENS' => 'GDNS',
						'GATEWAY' => 'GTWY',
						'GLEN' => 'GLN',
						'GREEN' => 'GN',
						'GROVE' => 'GRV',
						'HARBOR' => 'HBR',
						'HAVEN' => 'HVN',
						'HEIGHTS' => 'HTS',
						'HIGHWAY' => 'HWY',
						'HILL' => 'HL',
						'HILLS' => 'HLS',
						'HOLLOW' => 'HOLW',
						'INLET' => 'INLT',
						'ISLAND' => 'IS',
						'ISLANDS' => 'ISS',
						'ISLE' => 'ISLE',
						'JUNCTION' => 'JCT',
						'KEY' => 'CY',
						'KNOLLS' => 'KNLS',
						'LAKE' => 'LK',
						'LAKES' => 'LKS',
						'LANDING' => 'LNDG',
						'LANE' => 'LN',
						'LIGHT' => 'LGT',
						'LOAF' => 'LF',
						'LOCKS' => 'LCKS',
						'LODGE' => 'LDG',
						'LOOP' => 'LOOP',
						'MALL' => 'MALL',
						'MANOR' => 'MNR',
						'MEADOWS' => 'MDWS',
						'MILL' => 'ML',
						'MILLS' => 'MLS',
						'MISSION' => 'MSN',
						'MOUNT' => 'MT',
						'MOUNTAIN' => 'MTN',
						'NECK' => 'NCK',
						'ORCHARD' => 'ORCH',
						'OVAL' => 'OVAL',
						'PARK' => 'PARK',
						'PARKWAY' => 'PKY',
						'PASS' => 'PASS',
						'PATH' => 'PATH',
						'PIKE' => 'PIKE',
						'PINES' => 'PNES',
						'PLACE' => 'PL',
						'PLAIN' => 'PLN',
						'PLAINS' => 'PLNS',
						'PLAZA' => 'PLZ',
						'POINT' => 'PT',
						'PORT' => 'PRT',
						'PRAIRIE' => 'PR',
						'RADIAL' => 'RADL',
						'RANCH' => 'RNCH',
						'RAPIDS' => 'RPDS',
						'REST' => 'RST',
						'RIDGE' => 'RDG',
						'RIVER' => 'RIV',
						'ROAD' => 'RD',
						'ROW' => 'ROW',
						'RUN' => 'RUN',
						'SHOAL' => 'SHL',
						'SHOALS' => 'SHLS',
						'SHORE' => 'SHR',
						'SHORES' => 'SHRS',
						'SPRING' => 'SPG',
						'SPRINGS' => 'SPGS',
						'SPUR' => 'SPUR',
						'SQUARE' => 'SQ',
						'STATION' => 'STA',
						'STRAVENUES' => 'STRA',
						'STREAM' => 'STRM',
						'STREET' => 'ST',
						'SUMMIT' => 'SMT',
						'TERRACE' => 'TER',
						'TRACE' => 'TRCE',
						'TRACK' => 'TRAK',
						'TRAIL' => 'TRL',
						'TRAILER' => 'TRLR',
						'TUNNEL' => 'TUNL',
						'TURNPIKE' => 'TPKE',
						'UNION' => 'UN',
						'VALLEY' => 'VLY',
						'VIADUCT' => 'VIA',
						'VIEW' => 'VW',
						'VILLAGE' => 'VLG',
						'VILLE' => 'VL',
						'VISTA' => 'VIS',
						'WALK' => 'WALK',
						'WAY' => 'WAY',
						'WELLS' => 'WLS'];

	//////////////////////////////////////////////////////////////////////////////////////////

	public function link($id)
	{
		$this->model->people()->attach($id, ['team_id' => Auth::user()->team->id]);
	}

	public function unlink($id)
	{
		$this->model->people()->detach($id);
	}
	public function toggleEditing()
	{
		$this->editing = !$this->editing;
	}

	public function createNewHousehold()
	{
		$this->createNew = true;
	}

	public function confirmCreateNewHousehold()
	{
		$hh = $this->createHHFromParts($this->guess);
		$this->link($hh->id);

		$this->search 			= null;
		$this->revised_search	= null;
		$this->createNew 		= null;
		$this->guess 			= [];
	}

	//////////////////////////////////////////////////////////////////////////////////////////

	public function createHHFromPerson($person)
	{
		$hh = new Person;
		$hh->team_id 		= Auth::user()->team->id;
		$hh->is_household 	= true;

		foreach(['number', 
				 'fraction', 
				 'street', 
				 'apt', 
				 'city', 
				 'state', 
				 'zip', 
				 'lat', 
				 'long'] as $field) {

			$hh->{'address_'.$field} = $person->{'address_'.$field};

		}

		$hh->save();

		return $hh;
	}

	public function createHHFromVoter($voter)
	{
		$hh = new Person;
		$hh->team_id 		= Auth::user()->team->id;
		$hh->is_household 	= true;

		foreach(['number', 
				 'fraction', 
				 'street', 
				 'apt', 
				 'city', 
				 'state', 
				 'zip', 
				 'lat', 
				 'long'] as $field) {

			$hh->{'address_'.$field} = $voter->{'address_'.$field};

		}
		
		$hh->save();

		return $hh;
	}

	public function createHHFromParts($array)
	{
		$hh = new Person;
		$hh->is_household 	= true;
		$hh->team_id 		= Auth::user()->team->id;
		foreach(['number', 
				 'fraction', 
				 'street', 
				 'apt', 
				 'city', 
				 'state', 
				 'zip', 
				] as $field) {

			if (isset($array[$field])) {
				$hh->{'address_'.$field} = $array[$field];
			}

		}

		$hh->save();

		return $hh;
	}

	//////////////////////////////////////////////////////////////////////////////////////////

	public function linkHousehold($address) {

		$address = base64_decode($address);

		// Does a Household Already Exist?

		$lookHH = Person::where('team_id', Auth::user()->team->id)
					 	->where('is_household', true)
						->where('full_address', $address)
						->first();

		if ($lookHH) {

			$this->link($lookHH->id);

		} else {

			// If not, Does a Person Exists Matching this Address?

			$lookPerson = Person::where('team_id', Auth::user()->team->id)
								->where('is_household', false)
								->where('full_address', $address)
								->first();

			if ($lookPerson) {

				$new_hh = $this->createHHFromPerson($lookPerson);

			} else {

				// If not, Does a Voter Exist Matching this Address?

				$lookVoter = Voter::where('full_address', $address)->first();	

				if ($lookVoter) {

					$new_hh = $this->createHHFromVoter($lookVoter);

				} else {

					// Strange problem

				}

			}

			$this->link($new_hh->id);

		}

		$this->editing = false;

	}

	public function parseAddress($string)
	{
		// 240 Old Man Dr Provincetown, MA 01202
		$guess = ['number' 		=> null,
				  'fraction'	=> null,
				  'street'		=> null,
				  'apt'			=> null,
				  'city' 		=> null,
				  'state' 		=> null,
				  'zip' 		=> null];

		foreach (['.', ',', '#'] as $remove) {
			$string = str_replace($remove, '', $string);
		}

		$string = strtoupper($string);

		$words = explode(' ', $string);

		////////////////////////////////////////////////////////////////

		foreach($words as $c => $word) {

			if (in_array($word, $this->streetSuffixes) ||
				in_array($word, collect($this->streetSuffixes)->flip()->toArray())) {

				$first_line_words = array_slice($words, 0, $c + 1);
				$first_line = implode(' ', $first_line_words);

				$second_line_words = array_slice($words, $c + 1);
				$second_line = implode(' ', $second_line_words);

				break;
			}
		}

		////////////////////////////////////////////////////////////////

		$i = 0;
		if (is_numeric($words[$i])) {
			$guess['number'] = $words[$i];
		}

		////////////////////////////////////////////////////////////////

		$i = count($words) - 2;
		if (in_array($words[$i], $this->stateAbbreviations)) {
			$guess['state'] = $words[$i];
		}

		////////////////////////////////////////////////////////////////

		$i = count($words) - 1;
		if (is_numeric($words[$i])) {
			$guess['zip'] = $words[$i];
		}

		////////////////////////////////////////////////////////////////

		$guess['city'] = trim(substr($second_line, 0, strpos($second_line, $guess['state'])));

		////////////////////////////////////////////////////////////////

		// Change Avenue -> Ave
		$suffix = end($first_line_words);
		$short_suffix = (isset($this->streetSuffixes[$suffix])) ? $this->streetSuffixes[$suffix] : null; 
		if ($short_suffix) {
			$first_line_words[count($first_line_words) - 1] = $short_suffix;
		}

		$street = implode(' ', array_slice($first_line_words, 1));;


		$guess['street'] = $street;

		////////////////////////////////////////////////////////////////


		$this->revised_search = $guess['number'].$guess['fraction'].' '.$guess['street'].' '.$guess['fraction'].' '.$guess['city'].', '.$guess['state'].' '.$guess['zip'];

		return $guess;

	}

	//////////////////////////////////////////////////////////////////////////////////////////
	//
	//	LIFECYCLE FUNCTIONS
	//

	public function updatedGuess()
	{
		$this->revised_search = $this->guess['number'].$this->guess['fraction'].' '.$this->guess['street'].' '.$this->guess['fraction'].' '.$this->guess['city'].', '.$this->guess['state'].' '.$this->guess['zip'];
	}

    public function render()
    {
    	////// Build Search

    	$people = Person::where('team_id', Auth::user()->team->id)
    					->where('is_household', false)
    				    ->where('full_address', 'like', '%'.$this->search.'%')
    				  	->take(10)
    				  	->get();

    	$hhs = Person::where('team_id', Auth::user()->team->id)
    				 ->where('is_household', true)
    				 ->where('full_address', 'like', '%'.$this->search.'%')
    				 ->take(10)
    				 ->get();


    	$voter_hhs = Voter::where('full_address', 'like', '%'.$this->search.'%')
    					  ->take(10)
    				  	  ->get();

    	$hhs = $people->merge($hhs)
    				  ->merge($voter_hhs)
    				  ->pluck('full_address')
    				  ->unique()
    				  ->sortBy('full_address');


    	////// Existing Links

    	$linked = WorkCase::find($this->model->id)->people()->where('is_household', true)->get();

    	$linked_list = $linked->pluck('full_address')->unique()->toArray();

		////// Guess Components of a Novel Address

		if (!$this->createNew) {

			$this->guess = [];

		} elseif(empty($this->guess)) {

			$this->guess = $this->parseAddress($this->search);
		}

    	////// Return View

        return view('livewire.connector-households', [
        												'hhs' 			=> $hhs,
        												'linked' 		=> $linked,
        												'linked_list'	=> $linked_list
        											 ]);
    }
}
