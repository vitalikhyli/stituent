<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\VoterSlice;
use App\Municipality;
use App\County;
use App\District;
use App\Person;

use Auth;


class JurisdictionsController extends Controller
{

	public function show($app_type, $state, $type, $code)
	{
		// if ($type == 'city') {
		// 	$model = Municipality::where('state', session('team_state'))
		// 						 ->where('code', $code)
		// 						 ->first();
		// 	$people = Person::where('team_id', Auth::user()->team->id)
		// 					->where('city_code', $model->code)
		// 					->count();
		// }

		// if ($type == 'county') {
		// 	$model = County::where('state', session('team_state'))
		// 				   ->where('code', $code)
		// 				   ->first();
		// 	$people = Person::where('team_id', Auth::user()->team->id)
		// 					->where('county_code', $model->code)
		// 					->count();
		// }

		// if ($type == 'congress') {
		// 	$model = District::where('state', session('team_state'))
		// 					 ->where('type', 'F')
		// 					 ->where('code', $code)
		// 					 ->first();
		// 	$people = Person::where('team_id', Auth::user()->team->id)
		// 					->where('congress_district', $model->code)
		// 					->count();
		// }



		// return view('shared-features.jurisdictions.show', [
		// 			    									'model' 		=> $model,
		// 			    									'people'		=> $people,
		// 			    								   ]);
	}

	public function index($app_type)
	{	
		//////////////////////////

		$mine =  (!isset($_GET['show']) || $_GET['show'] == 'mine') ? true : false;
		$count_record = VoterSlice::where('name', session('team_table'))->first()->countRecord;

		if ($count_record) {

			//////////////////////////
			
			$cities = Municipality::where('state', session('team_state'))->get();
			$cities = $cities->each(function ($item) use ($count_record) {
				$item['the_count'] = $item->voterCount($count_record);
				// $item['democrats'] = $item->voterCount($count_record, 'democrats');
				// $item['republicans'] = $item->voterCount($count_record, 'republicans');
				// $item['unenrolled'] = $item->voterCount($count_record, 'unenrolled');
				// $item['men'] = $item->voterCount($count_record, 'men');
				// $item['women'] = $item->voterCount($count_record, 'women');
			});
			if ($mine) {
				$cities = $cities->reject(function ($item) {
					return $item->the_count <= 0;
				});
			}

			//////////////////////////

			$counties = County::where('state', session('team_state'))->get();
			$counties = $counties->each(function ($item) use ($count_record) {
				$item['the_count'] = $item->voterCount($count_record);
			});
			if ($mine) {
				$counties = $counties->reject(function ($item) {
					return $item->the_count <= 0;
				});
			}

			//////////////////////////

			$congress = District::where('state', session('team_state'))->where('type', 'F')->get();
			$congress = $congress->each(function ($item) use ($count_record) {
				$item['the_count'] = $item->voterCount($count_record);
			});
			if ($mine) {
				$congress = $congress->reject(function ($item) {
					return $item->the_count <= 0;
				});
			}


			//////////////////////////

			$senate = District::where('state', session('team_state'))->where('type', 'S')->get();
			$senate = $senate->each(function ($item) use ($count_record) {
				$item['the_count'] = $item->voterCount($count_record);
			});
			if ($mine) {
				$senate = $senate->reject(function ($item) {
					return $item->the_count <= 0;
				});
			}

			//////////////////////////

			$house = District::where('state', session('team_state'))->where('type', 'H')->get();
			$house = $house->each(function ($item) use ($count_record) {
				$item['the_count'] = $item->voterCount($count_record);
			});
			if ($mine) {
				$house = $house->reject(function ($item) {
					return $item->the_count <= 0;
				});
			}

			//////////////////////////

		} else {

			$cities 	= collect([]);
			$counties 	= collect([]);
			$congress 	= collect([]);
			$house 		= collect([]);
			$senate 	= collect([]);

		}

	    return view('shared-features.jurisdictions.index', [
					    									'cities' 		=> $cities,
					    									'counties'		=> $counties,
					    									'congress'		=> $congress,
					    									'house'			=> $house,
					    									'senate'		=> $senate,
					    									'count_record'	=> $count_record
					    								   ]);
	}
}
