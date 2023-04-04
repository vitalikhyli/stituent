<?php
/* NOT SURE THIS CONTROLLER IS NEEDED -- ALREADY SHARED CONTROLLER

namespace App\Http\Controllers\Office;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Carbon\Carbon;


use Auth;
use App\Contact;
use App\ContactPerson;
use App\WorkCase;
use App\Category;
use App\HistoryItem;
use App\Person;
use App\GroupPerson;

use Faker\Factory as Faker;

use Log;

use DB;
use App\Traits\CalendarTrait;

class DashboardController extends Controller
{
  use CalendarTrait;
    //
    //  OFFICIAL
    ////////////////////////////////////////////////////////////////////////////////////////

    public function dashboard()
    {
      //dd("laz");

      $logtime = logTime([], 'START');

        $date               = Carbon::today();

        $contacts_total     = Auth::user()->contacts()->count();

        $followups          = Auth::user()->contacts()
                                     ->where('followup',1)
                                     ->where('followup_done',0)
                                     ->where(function ($q) {
                                         $q->orwhere('followup_on', null);
                                         $q->orwhereDate('followup_on', '>=', Carbon::now());
                                     });


        $followups_total    = $followups->count();
        $followups          = $followups->orderBy('created_at', 'desc')
                                     ->take(5)
                                     ->get();

        $followups_overdue        = Auth::user()->contacts()
                                           ->where('followup',1)
                                           ->where('followup_done',0)
                                           ->whereDate('followup_on', '<', Carbon::now());

        $followups_overdue_total  = $followups_overdue->count();
        $followups_overdue        = $followups_overdue->orderBy('followup_on')
                                                      ->take(5)
                                                      ->get();

        $recent_contacts    = Auth::user()->contacts()
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();
        $logtime = logTime($logtime, 'CONTACTS');


        $cases    = Auth::user()->cases()->whereDate('date', $date)->get();

        $case_type = "";
        $cases_count = 0;
        $my_cases = WorkCase::where('user_id', Auth::user()->id)
                              ->where('team_id', Auth::user()->team->id)
                              ->where('status','!=','resolved')
                              ->with('people')
                              ->orderBy('date', 'desc')
                              ->take(5)
                              ->get();

        if ($my_cases->count() <= 0) {

            $open_cases   = WorkCase::where('team_id', Auth::user()->team->id)
                              ->where('status','!=','resolved')
                              ->with('people')
                              ->orderBy('date', 'desc')
                              ->limit(5)
                              ->get();

            $cases_total  = WorkCase::where('team_id', Auth::user()->team->id)
                              ->where('status','!=','resolved')
                              ->count();

            $case_type    = "team";

        } else {

            $cases_total  = WorkCase::where('user_id', Auth::user()->id)
                              ->where('team_id', Auth::user()->team->id)
                              ->where('status','!=','resolved')
                              ->count();

            $open_cases   = $my_cases;

            $case_type  = "user";

        }

        $logtime = logTime($logtime, 'CASES');

        $categories = Category::with(['groups' => function ($q) {
                                        $q->whereNull('archived_at');
                                        $q->orderBy('name');
                                   }])
                                   ->where('team_id',Auth::user()->team->id)
                                   ->whereIn('name',['Issue Groups','Constituent Groups','Legislation'])
                                   ->get();


        $logtime = logTime($logtime, 'GROUPS');


        $people_recent      = Person::where('team_id',Auth::user()->team->id);
                                   // ->where('entity',0);

        $people_month_total = $people_recent->whereDate('created_at','>',Carbon::now()->subDays(30))
                                            ->count();

        $people_recent      = $people_recent->take(5)
                                            ->orderBy('created_at', 'desc')
                                            ->get();
        $logtime = logTime($logtime, 'PEOPLE');

        $events_json        = $this->getCalendarEventsJson(3);

        $logtime = logTime($logtime, 'CALENDAR');

        ///////////////////////////////////////////// MAP  ////////////////////////////

      $map = [];
      $map['lat_max'] = Auth::user()->team->people()->max('address_lat');
      $map['lat_min'] = Auth::user()->team->people()->min('address_lat');
      $map['lng_max'] = Auth::user()->team->people()->max('address_long');
      $map['lng_min'] = Auth::user()->team->people()->min('address_long');

      $logtime = logTime($logtime, 'MAP');
      //dd($map);

      /////////////////////////////////////////////CONTACTS GRAPH  ////////////////////////////

        // $render = microtime(true) - $render;
        // Log::info('Before Graph '.$render);

      $first_date = Carbon::now()->subDays(365);
      $last_date  = Carbon::now();
      $max_cols   = 6; //Show n months at a time
      $items      = collect(
                      Contact::where('team_id',Auth::user()->team->id)
                      ->whereDate('created_at','>',$first_date)
                      ->whereDate('created_at','<=',$last_date)
                      ->orderBy('created_at', 'desc')
                      ->get()
                    );
      $items      = $items->groupBy(function ($i) {
                      return $i->created_at->format('M y');
                    })
                    ->map(function ($i) {
                       return $i->count('id');
                    })
                    ->take($max_cols)
                    ->reverse();



      //$items = $items->concat($items)->take($max_cols); //To Test graph with more columns

      $graph_a                  = [];
      $graph_a['max_height']    = 250;
      $graph_a['max_width']     = 320;
      if ($items->count() > 0) {
        $graph_a['col_width']     = $graph_a['max_width']/$items->count();
      } else {
        $graph_a['col_width']     = 0;
      }
      $graph_a['max_y']         = $items->max();
      $graph_a['items']         = $items;

      $logtime = logTime($logtime, 'GRAPH');
      //dd($logtime);

      $birthdays = Auth::user()->team->people()
                                     ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(dob) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(dob)')
                                     ->orderByRaw('DAYOFYEAR(dob)')
                                     ->paginate(5);
                                     dd($birthdays);

      //////////////////////////////////////////////////

        // $render = microtime(true) - $render;
        // Log::info('Controller Finish '.$render);

        return view('office.dashboard.main', compact(
                                                    'graph_a',
                                                    'people_recent',
                                                    'people_month_total',
                                                    'followups',
                                                    'followups_overdue',
                                                    'followups_overdue_total',
                                                    'followups_total',
                                                    'date',
                                                    'contacts_total',
                                                    'cases_total',
                                                    'recent_contacts',
                                                    'events_json',
                                                    'open_cases',
                                                    'case_type',
                                                    'logtime',
                                                    'map',
                                                    'categories'
                                                  ));
    }


    public function activityMap()
    {
        $timeframe = 30;
        if (request('timeframe')) {
            $timeframe = request('timeframe');
        }

        $contact_ids = Contact::where('team_id', Auth::user()->team_id)
                             ->where('date', '>', Carbon::today()->subDays($timeframe))
                             ->pluck('id');

        $person_ids = ContactPerson::whereIn('contact_id', $contact_ids)
                                   ->pluck('person_id');

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
            if ($person->cases()->unresolved()->count() > 0) {
              $household['color'] = 'red';
            } else if ($person->cases()->resolved()->count() > 0) {
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

    public function metricsHistory()
    {

      ////////////////////////////////////////////////// GRAPH A ////////////////////////////

      $first_date = HistoryItem::where('team_id',Auth::user()->team->id)->min('created_at');
      $last_date  = HistoryItem::where('team_id',Auth::user()->team->id)->max('created_at');
      $max_cols   = 30;
      $items      = collect(
                      HistoryItem::where('team_id',Auth::user()->team->id)
                      ->whereDate('created_at','>',$first_date)
                      ->whereDate('created_at','<=',$last_date)
                      ->orderBy('created_at', 'desc')
                      ->get()
                    );
      $graph_a                  = [];
      $graph_a['field']         = 'num_people';
      $graph_a['max_height']    = 200;
      $graph_a['max_y']         = HistoryItem::where('team_id',Auth::user()->team->id)
                                        ->max($graph_a['field']);
      $graph_a['modulus']       = ceil($items->count()/$max_cols);
      $graph_a['reduced_items'] = $items->nth($graph_a['modulus']);
      $graph_a['b']             = -1;
      $graph_a['previous']      = '';
      $graph_a['bg']            = array(1 => 'bg-grey-lighter', -1 => 'bg-white');


      ////////////////////////////////////////////////// GRAPH B ////////////////////////////

      $first_date = Carbon::now()->subDays(30);
      $last_date  = Carbon::now();
      $max_cols   = 60;
      $items      = collect(
                      HistoryItem::where('team_id',Auth::user()->team->id)
                      ->whereDate('created_at','>',$first_date)
                      ->whereDate('created_at','<=',$last_date)
                      ->orderBy('created_at', 'desc')
                      ->get()
                    );

      $graph_b                  = [];
      $graph_b['field']         = 'num_cases_open';
      $graph_b['max_height']    = 150;
      $graph_b['max_y']         = HistoryItem::where('team_id',Auth::user()->team->id)
                                        ->max($graph_b['field']);
      $graph_b['modulus']       = ceil($items->count()/$max_cols);
      $graph_b['reduced_items'] = $items->nth($graph_b['modulus']);
      $graph_b['b']             = -1;
      $graph_b['previous']      = '';
      $graph_b['bg']            = array(1 => 'bg-grey-lighter', -1 => 'bg-white');

      return view('office.metrics.history', compact('graph_a','graph_b'));
    }

}
