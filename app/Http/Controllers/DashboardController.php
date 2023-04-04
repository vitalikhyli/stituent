<?php

namespace App\Http\Controllers;

use App\CasePerson;
use App\Category;
use App\Contact;
use App\ContactPerson;
use App\Entity;
use App\Group;
use App\GroupPerson;
use App\HistoryItem;
use App\Person;
use App\Voter;

use App\Traits\CalendarTrait;
use App\WorkCase;
use App\SharedCase;
use Auth;
use Carbon\Carbon;
use DB;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;

class DashboardController extends Controller
{
    use CalendarTrait;
    //
    //  OFFICIAL
    ////////////////////////////////////////////////////////////////////////////////////////

    public function dashboard($app_type)
    {
        $logtime = logTime([], 'START');

        $date = Carbon::today();

        $contacts_total = Auth::user()->contacts()->count();

        $followups = Auth::user()->contacts()
                                     ->where('followup', 1)
                                     ->where('followup_done', 0)
                                     ->where(function ($q) {
                                         $q->orwhere('followup_on', null);
                                         $q->orwhereDate('followup_on', '>=', Carbon::now());
                                     });

        $followups_total = $followups->count();
        $followups = $followups->orderBy('created_at', 'desc')
                                     ->take(5)
                                     ->get();

        $followups_overdue = Auth::user()->contacts()
                                           ->where('followup', 1)
                                           ->where('followup_done', 0)
                                           ->whereDate('followup_on', '<', Carbon::now());

        $followups_overdue_total = $followups_overdue->count();
        $followups_overdue = $followups_overdue->orderBy('followup_on')
                                                      ->take(5)
                                                      ->get();

        $recent_contacts = Auth::user()->contacts()
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();
        $logtime = logTime($logtime, 'CONTACTS');

        $contacts = Auth::user()->contacts()->whereDate('date', $date)->get();
        $cases = WorkCase::whereDate('date', $date)
                              ->where('team_id', Auth::user()->team->id)
                              ->get();

        $case_type = '';
        $cases_count = 0;
        $my_cases = WorkCase::where('user_id', Auth::user()->id)
                              ->where('team_id', Auth::user()->team->id)
                              ->where('status', '!=', 'resolved')
                              ->with('people')
                              ->orderBy('date', 'desc')
                              ->take(5)
                              ->get();

        if ($my_cases->count() <= 0) {
            $open_cases = WorkCase::where('team_id', Auth::user()->team->id)
                              ->teamOrPrivateAndMine()
                              ->where('status', '!=', 'resolved')
                              ->with('people')
                              ->orderBy('date', 'desc')
                              ->limit(5)
                              ->get();

            $cases_total = WorkCase::where('team_id', Auth::user()->team->id)
                              ->teamOrPrivateAndMine()
                              ->where('status', '!=', 'resolved')
                              ->count();

            $case_type = 'team';
        } else {
            $cases_total = WorkCase::where('user_id', Auth::user()->id)
                              ->where('team_id', Auth::user()->team->id)
                              ->where('status', '!=', 'resolved')
                              ->count();

            $open_cases = $my_cases;

            $case_type = 'user';
        }

        // ===============================================> SHARED CASES
        $allcases = WorkCase::where('team_id', Auth::user()->team_id)
                            ->TeamOrPrivateAndMine()
                            ->get();
        $all_ids = $allcases->pluck('id');
        //dd($allcases);
        $sharing = SharedCase::whereIn('case_id', $all_ids)
                             ->where('team_id', Auth::user()->team_id)
                             ->get();

        $shared_team = SharedCase::where('shared_type', 'team')
                                 ->where('shared_team_id', Auth::user()->team_id)
                                 ->get();

        $shared_user = SharedCase::where('shared_type', 'user')
                                 ->where('shared_user_id', Auth::user()->id)
                                 ->get();

        $shared_cases = $shared_team->merge($shared_user)
                                    ->merge($sharing)
                                    ->sortByDesc('created_at')
                                    ->groupBy('case_id');
        //dd($shared_cases);

        $logtime = logTime($logtime, 'CASES');

        $categories = Category::with(['groups' => function ($q) {
            $q->whereNull('archived_at');
            $q->orderBy('name');
        }])
                                   ->where('team_id', Auth::user()->team->id)
                                   ->whereIn('name', ['Issue Groups', 'Constituent Groups', 'Legislation'])
                                   ->get();

        $logtime = logTime($logtime, 'GROUPS');

        $people_recent = Person::where('team_id', Auth::user()->team->id);
        // ->where('entity',0);

        $people_month_total = $people_recent->whereDate('created_at', '>', Carbon::now()->subDays(30))
                                            ->count();

        $people_recent = $people_recent->take(5)
                                            ->orderBy('created_at', 'desc')
                                            ->get();
        $logtime = logTime($logtime, 'PEOPLE');

        $events_json = $this->getCalendarEventsJson(3);

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
        $last_date = Carbon::now();
        $max_cols = 6; //Show n months at a time
        // $max_cols   = 10; //Show n months at a time
        $items = collect(
                      Contact::where('team_id', Auth::user()->team->id)
                      ->whereDate('date', '>', $first_date)
                      ->whereDate('date', '<=', $last_date)
                      ->orderBy('date', 'desc')
                      ->get()
                    );
        $items = $items->groupBy(function ($i) {
            return $i->date->format('M y');
            // return $i->date->format('W y');
        })
                    ->map(function ($i) {
                        return $i->count('id');
                    })
                    ->take($max_cols)
                    ->reverse();

        //$items = $items->concat($items)->take($max_cols); //To Test graph with more columns

        $graph_a = [];
        $graph_a['max_height'] = 250;
        $graph_a['max_width'] = 320;
        if ($items->first()) {
            $graph_a['col_width'] = $graph_a['max_width'] / $items->count();
        } else {
            $graph_a['col_width'] = 0;
        }
        $graph_a['max_y'] = $items->max();
        $graph_a['items'] = $items;

        $logtime = logTime($logtime, 'GRAPH');
        //dd($logtime);

        //////////////////////////////////////////////////

        $this_year  = Carbon::now()->format('Y');
        $next_year  = $this_year + 1;
        $last_day   = Carbon::now()->addDays(30)->toDateString();

        $big_birthdays = collect([]);
        $linked_birthdays = collect([]);
        try {
          $birthdays_people = Auth::user()->team->people()
                              ->selectRaw('*, DATE_FORMAT(dob, "%m-%d") as month_day,
                                IF(DATE_FORMAT(dob, "%m-%d") >= DATE_FORMAT(CURRENT_TIMESTAMP, "%m-%d"),
                                  DATE_FORMAT(dob, "'.$this_year.'-%m-%d"),
                                  DATE_FORMAT(dob, "'.$next_year.'-%m-%d")
                                ) as next_birthday')
                              ->whereNotNull('dob')
                              ->having('next_birthday', '<=', $last_day)
                              ->take(10)
                              ->get();

          $linked_birthdays = $birthdays_people->sortBy('next_birthday');

          $today_date = $date->format('Y-m-d');
          $end_of_month = $date->copy()->endOfMonth();
          $days_until_end_of_month = $date->diffInDays($end_of_month);
          $tempyear = $date->format('Y');
          $big_birthdays = Voter::selectRaw("
                              id,full_name, full_address, dob, TIMESTAMPDIFF(YEAR, dob, ('$today_date' + INTERVAL + $days_until_end_of_month DAY)) AS upcoming_age, DATE_FORMAT(dob, '%m-%d') as birth_date")
                               ->whereRaw(" 
                              DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(NOW(), '%m-%d') and
                              DATE_FORMAT(dob, '%m-%d') <= DATE_FORMAT(('$today_date' + INTERVAL + $days_until_end_of_month DAY), '%m-%d')
                              and MOD($tempyear - YEAR(dob), 5) = 0
                              and archived_at is null
                              and dob < '2020-01-01'")
                        ->whereNotNull('dob')
                        ->orderByRaw('dob')
                        ->take(10)
                        ->get();
        } catch (\Exception $e) {

        }



        $logtime = logTime($logtime, 'BIRTHDAYS');

        //////////////////////////////////////////////////

        // $organizations_count = Entity::where('team_id', Auth::user()->team_id)
        //                                ->count();

        //   $organizations = Entity::where('team_id', Auth::user()->team_id)
        //                          ->with('partnerships', 'notes')
        //                          ->get()
        //                          ->sortBy(function($org) {
        //       return 1000 - $org->partnerships->count();
        //   })->splice(0, 5);

        //   $logtime = logTime($logtime, 'ORGANIZATIONS');

        // $render = microtime(true) - $render;
        // Log::info('Controller Finish '.$render);

        $notice = \App\Models\Admin\Notice::where('app_type', Auth::user()->team->app_type)
                                          ->where('approved', true)
                                          ->whereNull('archived_at')
                                          ->where('publish_at', '<=', Carbon::now()->toDateTimeString())
                                          ->orderBy('publish_at', 'desc')
                                          ->first();

        return view($app_type.'.dashboard.main', compact(
                                                    'notice',
                                                    'graph_a',
                                                    'people_recent',
                                                    'people_month_total',
                                                    'followups',
                                                    'followups_overdue',
                                                    'followups_overdue_total',
                                                    'followups_total',
                                                    'date',
                                                    'contacts',
                                                    'cases',
                                                    'shared_cases',
                                                    'contacts_total',
                                                    'cases_total',
                                                    'recent_contacts',
                                                    'events_json',
                                                    'open_cases',
                                                    'case_type',
                                                    'logtime',
                                                    'map',
                                                    'categories',
                                                    // 'organizations',
                                                    // 'organizations_count',
                                                    'linked_birthdays',
                                                    'big_birthdays'
                                                  ));
    }

    public function activityMap()
    {
        $timeframe = 30;
        if (request('timeframe')) {
            $timeframe = request('timeframe');
        }

        
        $cases = WorkCase::where('team_id', Auth::user()->team_id)
                                  ->where('created_at', '>', Carbon::today()->subDays($timeframe))
                                  ->pluck('id');
        $cases_person_ids = CasePerson::whereIn('case_id', $cases)
                                     ->pluck('person_id');
        //dd($COVID_people);

        $activity = collect([]);
        $activity['households'] = collect([]);
        

        $contact_ids = Contact::where('team_id', Auth::user()->team_id)
                             ->where('date', '>', Carbon::today()->subDays($timeframe))
                             ->pluck('id');

        $person_ids = ContactPerson::whereIn('contact_id', $contact_ids)
                                   ->pluck('person_id');

        $person_ids = $person_ids->merge($cases_person_ids);

        //dd($person_ids);

        $people = Auth::user()->team->people()
                                    ->whereNotNull('address_lat')
                                    ->whereIn('id', $person_ids)
                                    ->get();
        //dd($people);

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

    public function metricsHistory($app_type)
    {

      ////////////////////////////////////////////////// GRAPH A ////////////////////////////

        $first_date = HistoryItem::where('team_id', Auth::user()->team->id)->min('created_at');
        $last_date = HistoryItem::where('team_id', Auth::user()->team->id)->max('created_at');
        $max_cols = 30;
        $items = collect(
                      HistoryItem::where('team_id', Auth::user()->team->id)
                      ->whereDate('created_at', '>', $first_date)
                      ->whereDate('created_at', '<=', $last_date)
                      ->orderBy('created_at', 'desc')
                      ->get()
                    );
        $graph_a = [];
        $graph_a['field'] = 'num_people';
        $graph_a['max_height'] = 200;
        $graph_a['max_y'] = HistoryItem::where('team_id', Auth::user()->team->id)
                                        ->max($graph_a['field']);
        $graph_a['modulus'] = ceil($items->count() / $max_cols);
        $graph_a['reduced_items'] = $items->nth($graph_a['modulus']);
        $graph_a['b'] = -1;
        $graph_a['previous'] = '';
        $graph_a['bg'] = [1 => 'bg-grey-lighter', -1 => 'bg-white'];

        ////////////////////////////////////////////////// GRAPH B ////////////////////////////

        $first_date = Carbon::now()->subDays(30);
        $last_date = Carbon::now();
        $max_cols = 60;
        $items = collect(
                      HistoryItem::where('team_id', Auth::user()->team->id)
                      ->whereDate('created_at', '>', $first_date)
                      ->whereDate('created_at', '<=', $last_date)
                      ->orderBy('created_at', 'desc')
                      ->get()
                    );

        $graph_b = [];
        $graph_b['field'] = 'num_cases_open';
        $graph_b['max_height'] = 150;
        $graph_b['max_y'] = HistoryItem::where('team_id', Auth::user()->team->id)
                                        ->max($graph_b['field']);
        $graph_b['modulus'] = ceil($items->count() / $max_cols);
        $graph_b['reduced_items'] = $items->nth($graph_b['modulus']);
        $graph_b['b'] = -1;
        $graph_b['previous'] = '';
        $graph_b['bg'] = [1 => 'bg-grey-lighter', -1 => 'bg-white'];

        return view($app_type.'.metrics.history', compact('graph_a', 'graph_b'));
    }
}
