<?php
/* EDITING OUT UNNECESSSARY CONTROLLER

namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

// use Auth;
// use App\Contact;
// use App\WorkCase;
// use App\Person;
// use App\Voter;

use Faker\Factory as Faker;

use Auth;
use App\Contact;
use App\WorkCase;
use App\Category;
use App\HistoryItem;
use App\Person;
use App\Entity;

use Log;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{


    public function dashboard()
    {

        // ================================================================> UNIVERSITY TEAM
        // $fluency  = App\User::where('username','fluency1')->first();
        // $u_team = App\Team::where('app_type', 'u')->first();

        // $u_member = App\TeamUser::where('user_id',$fluency->id)
        //                          ->where('team_id',$u_team->id)
        //                          ->first();
        // if(!$u_member) {
        //     $u_member = new App\TeamUser;
        //     $u_member->user_id = $fluency->id;
        //     $u_member->team_id = $u_team->id;
        //     $u_member->save();
        // }


        // $u_permission = App\Permission::where('user_id',$fluency->id)
        //                          ->where('team_id',$u_team->id)
        //                          ->first();
        // if(!$u_permission) {
        //     $u_permission = new App\Permission;
        // }

        // $u_permission->user_id = $fluency->id;
        // $u_permission->team_id = $u_team->id;
        // $u_permission->developer = true;
        // $u_permission->admin = true;
        // $u_permission->save();

        // ================================================================> /END

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
      if ($items->first()) {
        $graph_a['col_width']     = $graph_a['max_width']/$items->count();
      } else {
        $graph_a['col_width']     = 0;
      }
      $graph_a['max_y']         = $items->max();
      $graph_a['items']         = $items;

      $logtime = logTime($logtime, 'GRAPH');
      //dd($logtime);

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

    public function dashboardOLD()
    {
        $date               = Carbon::today();

        $contacts_total     = Contact::where('team_id',Auth::user()->team->id)
                                      ->where(function ($q) {
                                         $q->orwhere('private', 0);
                                         $q->orwhere(function ($w) {
                                            $w->where('private', 1);
                                            $w->where('user_id', Auth::user()->id);
                                         });
                                      })
                                      ->count();

      Log::stack(['neu'])->info("NEU TIME: ".(microtime(-1) - $start), "CONTACTS");

        $followups          = Contact::where('team_id', Auth::user()->team->id)
                                     ->where('followup',1)
                                     ->where('followup_done',0)
                                     ->where(function ($q) {
                                         $q->orwhere('private', 0);
                                         $q->orwhere(function ($w) {
                                            $w->where('private', 1);
                                            $w->where('user_id', Auth::user()->id);
                                         });
                                     })
                                     ->where(function ($q) {
                                         $q->orwhere('followup_on', null);
                                         $q->orwhereDate('followup_on', '>=', Carbon::now());
                                     });


        $followups_total    = $followups->count();
        $followups          = $followups->orderBy('created_at', 'desc')
                                     ->take(5)
                                     ->get();

        $followups_overdue        = Contact::where('team_id', Auth::user()->team->id)
                                           ->where('followup',1)
                                           ->where('followup_done',0)
                                           ->whereDate('followup_on', '<', Carbon::now())
                                           ->where(function ($q) {
                                               $q->orwhere('private', 0);
                                               $q->orwhere(function ($w) {
                                                  $w->where('private', 1);
                                                  $w->where('user_id', Auth::user()->id);
                                               });
                                           });

        $followups_overdue_total  = $followups_overdue->count();
        $followups_overdue        = $followups_overdue->orderBy('followup_on')
                                                      ->take(5)
                                                      ->get();

        $recent_contacts    = Contact::where('team_id', Auth::user()->team->id)
                                      ->where(function ($q) {
                                         $q->orwhere('private', 0);
                                         $q->orwhere(function ($w) {
                                            $w->where('private', 1);
                                            $w->where('user_id', Auth::user()->id);
                                         });
                                      })
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();

        $cases    = WorkCase::whereDate('date', $date)->get();

        $organizations_count = Entity::where('team_id', Auth::user()->team_id)
                                     ->count();

        $organizations = Entity::where('team_id', Auth::user()->team_id)
                               ->with('partnerships', 'notes')
                               ->get()
                               ->sortBy(function($org) {
            return 1000 - $org->partnerships->count();
        })->splice(0, 5);

        $case_type = "";
        $cases_count = 0;
        $my_cases = WorkCase::where('user_id', Auth::user()->id)
                              ->where('team_id', Auth::user()->team->id)
                              ->where('status','!=','resolved')
                              ->with('people')
                              ->orderBy('date', 'desc')
                              ->limit(5)
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

        // $categories = Category::university()->get();
        $categories = Auth::user()->team->categories;

        $people_recent      = Person::where('team_id',Auth::user()->team->id);
                                   // ->where('entity',0);

        $people_month_total = $people_recent->whereDate('created_at','>',Carbon::now()->subDays(30))
                                            ->count();

        $people_recent      = $people_recent->take(5)
                                            ->orderBy('created_at', 'desc')
                                            ->get();

        $events_json        = $this->getCalendarEventsJson();

        $birthdays          = Person::where('team_id',Auth::user()->team->id)
                                    // ->whereMonth('dob', '=', Carbon::now()->format("m"))
                                    ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(dob) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(dob)')
                                    ->orderByRaw('DAYOFYEAR(dob)')
                                    ->get();


        $entities        = \App\Entity::where('team_id', Auth::user()->team->id)->get();

      ///////////////////////////////////////////// WORDCLOUD ////////////////////////////

       $thecloud = (new \App\WordCloud)->getWordCloud();

      /////////////////////////////////////////////CONTACTS GRAPH  ////////////////////////////

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

      $graph_a                  = [];
      $graph_a['max_height']    = 250;
      $graph_a['max_width']     = 320;
      if ($items->first()) {
        $graph_a['col_width']     = $graph_a['max_width']/$items->count();
      } else {
        $graph_a['col_width'] = 0;
      }
      $graph_a['max_y']         = $items->max();
      $graph_a['items']         = $items;

      //////////////////////////////////////////////////

        return view('u.dashboard.main', compact(
                                                    'entities',
                                                    'graph_a',
                                                    'thecloud',
                                                    'birthdays',
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
                                                    'categories',
                                                    'organizations',
                                                    'organizations_count'
                                                  ));
    }


    public function getCalendarEventsJson()
    {
        $dates = [];
        $contacts_by_date = Contact::select('date')->orderBy('date')->groupBy('date')->get();
        foreach ($contacts_by_date as $contact) {
            if (isset($dates[$contact->date->format('Y-m-d')]['contacts'])) {
                $dates[$contact->date->format('Y-m-d')]['contacts'] += 1;
            } else {
                $dates[$contact->date->format('Y-m-d')]['contacts'] = 1;
            }
        }
        $cases_by_date = WorkCase::select('date')->orderBy('date')->groupBy('date')->get();
        foreach ($cases_by_date as $case) {
            if (isset($dates[$case->date->format('Y-m-d')]['cases'])) {
                $dates[$case->date->format('Y-m-d')]['cases'] += 1;
            } else {
                $dates[$case->date->format('Y-m-d')]['cases'] = 1;
            }

        }
        $events = [];
        foreach ($dates as $date => $details) {
            $event = [];
            $event['date'] = $date;
            foreach ($details as $type => $val) {
                $event[$type] = $val;
            }
            $events[] = $event;
        }
        return collect($events)->toJson();
    }
    public function activityMap()
    {
        $timeframe = 30;
        if (request('timeframe')) {
            $timeframe = request('timeframe');
        }
        $faker = Faker::create();

        $activity = collect([]);
        $activity['households'] = collect([]);
        for ($i=0; $i< (3 * $timeframe); $i++) {
            $household = [];
            $household['title'] = $faker->streetAddress;
            $household['residents'] = $faker->randomDigit + 1;
            $household['contacts'] = $faker->randomDigit + 1;
            $household['url'] = "/u/households/123456";
            $household['phone'] = $faker->phoneNumber;
            $household['lat'] = $faker->randomFloat(4, 42.330, 42.350);
            $household['lng'] = $faker->randomFloat(4, -71.060, -71.120);
            $household['color'] = $faker->randomElement(['red', 'green', 'blue']);
            $activity['households'][] = $household;
        }

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

      return view('u.metrics.history', compact('graph_a','graph_b'));
    }

}
