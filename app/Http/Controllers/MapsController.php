<?php

namespace App\Http\Controllers;

use App\CasePerson;
use App\Contact;
use App\ContactPerson;
use App\GroupPerson;
use App\Traits\ConstituentQueryTrait;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MapsController extends Controller
{
    use ConstituentQueryTrait;

    public function index($app_type)
    {
        $map_route = 'activity';

        return view('shared-features.maps.index', compact('map_route'));
    }

    public function voters($app_type)
    {
        $map_route = 'voters';
        $people = $this->constituentQuery(request()->input());
        $municipalities = $this->getMunicipalities();
        $districts = $this->getDistricts();
        $zips = $this->getZips();
        $categories = Auth::user()->team->categories;

        $total_count = $this->total_count;

        $input = request()->input();
        $limit = 200;
        if (request('limit')) {
            $limit = request('limit');
        }

        return view('shared-features.maps.voters', compact('input', 'people', 'total_count', 'municipalities', 'districts', 'zips', 'categories', 'map_route', 'limit'));
    }

    public function groups($app_type)
    {
        return view('shared-features.maps.groups');
    }

    public function jsonActivity($app_type)
    {
        $timeframe = 30;
        if (request('timeframe')) {
            $timeframe = request('timeframe');
        }

        $case_person_ids = CasePerson::where('team_id', Auth::user()->team_id)
                                     ->where('created_at', '>', Carbon::today()->subDays($timeframe))
                                     ->pluck('person_id');

        //dd($case_person_ids);

        $contact_ids = Contact::where('team_id', Auth::user()->team_id)
                             ->where('date', '>', Carbon::today()->subDays($timeframe))
                             ->pluck('id');

        $person_ids = ContactPerson::whereIn('contact_id', $contact_ids)
                                   ->pluck('person_id');

        $person_ids = $person_ids->merge($case_person_ids)->unique();
        //dd($person_ids);

        $people = Auth::user()->team->people()
                                    ->whereNotNull('address_lat')
                                    ->whereIn('id', $person_ids)
                                    ->get();

        $activity = collect([]);
        $activity['households'] = collect([]);
        foreach ($people as $person) {
            $household = [];
            $household['name'] = $person->name;
            $household['address'] = $person->full_address;
            $household['contacts'] = $person->contacts()->count();
            $household['groups'] = $person->groups->implode('name', '<br>');
            $household['url'] = '/'.Auth::user()->team->app_type.'/constituents/'.$person->id;
            $household['phone'] = $person->primary_phone;
            $household['lat'] = $person->address_lat;
            $household['lng'] = $person->address_long;
            if ($person->cases()->unresolved()->first()) {
                $household['color'] = 'red';
            } elseif ($person->cases()->resolved()->first()) {
                $household['color'] = 'green';
            } else {
                $household['color'] = 'blue';
            }
            $activity['households'][] = $household;
        }

        $gp_ids = GroupPerson::where('team_id', Auth::user()->team->id)
                             ->where('created_at', '>', Carbon::today()->subDays($timeframe))
                             ->pluck('person_id');

        $people = Auth::user()->team->people()
                                    ->whereNotNull('address_lat')
                                    ->whereIn('id', $gp_ids)
                                    ->whereNotIn('id', $person_ids)
                                    ->get();

        foreach ($people as $person) {
            $household = [];
            $household['name'] = $person->name;
            $household['address'] = $person->full_address;
            $household['contacts'] = $person->contacts()->count();
            $household['groups'] = $person->groups->implode('name', '<br>');
            $household['url'] = '/'.Auth::user()->team->app_type.'/constituents/'.$person->id;
            $household['phone'] = $person->primary_phone;
            $household['lat'] = $person->address_lat;
            $household['lng'] = $person->address_long;
            $household['color'] = 'yellow';

            $activity['households'][] = $household;
        }

        $bounds = [];
        $bounds['max_lat'] = $activity['households']->max('lat');
        $bounds['min_lat'] = $activity['households']->min('lat');
        $bounds['max_lng'] = $activity['households']->max('lng');
        $bounds['min_lng'] = $activity['households']->min('lng');
        $activity['bounds'] = $bounds;

        return $activity->toJson();
    }

    public function jsonVoters($app_type)
    {
        $input = request()->input();
        $people = $this->constituentQuery(request()->input());

        $activity = collect([]);
        $activity['households'] = collect([]);
        foreach ($people as $person) {
            if ((int) $person->address_lat > 0 && (int) $person->address_long < 0) {
                $household = [];
                $household['name'] = $person->name;
                $household['address'] = $person->full_address;

                if (get_class($person) == 'Person') {
                    if ($person->contacts) {
                        $household['contacts'] = $person->contacts()->count();
                    } else {
                        $household['contacts'] = 0;
                    }
                    if ($person->groups) {
                        $household['groups'] = $person->groups->implode('name', '<br>');
                    } else {
                        $household['groups'] = 'None';
                    }
                } else {
                    $household['contacts'] = 0;
                    $household['groups'] = 'None';
                }
                $household['url'] = '/'.Auth::user()->team->app_type.'/constituents/'.$person->id;
                $household['phone'] = $person->primary_phone;
                $household['lat'] = $person->address_lat;
                $household['lng'] = $person->address_long;
                if ($person->person) {
                    $household['color'] = 'green';
                } else {
                    $household['color'] = 'blue';
                }

                $activity['households'][] = $household;
            }
        }

        $bounds = [];
        $bounds['max_lat'] = $activity['households']->max('lat');
        $bounds['min_lat'] = $activity['households']->min('lat');
        $bounds['max_lng'] = $activity['households']->max('lng');
        $bounds['min_lng'] = $activity['households']->min('lng');
        $activity['bounds'] = $bounds;

        return $activity->toJson();
    }
}
