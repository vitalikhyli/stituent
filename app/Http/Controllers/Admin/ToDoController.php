<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Person;
use App\Team;
use App\UserLog;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ToDoController extends Controller
{
    public function checkArrayStructure($a)
    {
        if (is_array($a)) {                         // FIRST LEVEL IS AN ARRAY

            if (count($a) == 1) {                   // ...THAT HAS ONLY ONE ELEMENT

                if (isset($a[0])) {
                    if (is_array($a[0])) {          // ...THE SOLE ELEMENT IS ITSELF AN ARRAY

                        if (count($a[0]) == 1) {    // ...THAT HAS ONLY ONE ELEMENTS

                            // echo "**** Double nested array found.\r\n";

                            return $a[0];           // RETURN THE SOLE NESTED ARRAY
                        }
                    }
                }
            }
        }
    }

    public function arrayFix($team_id = null)
    {
        $num_problems = 0;
        $solutions = [];

        if (! $team_id) {
            $team = Team::where('name', 'All Campaigns')->first(); //->limit(1)->get();
        } else {
            $team = Team::where('id', $team_id)->first(); //->get();
        }
        // $teams = Team::all();

        // foreach ($teams as $team) {
        $people = Person::where('team_id', $team->id)->get();
        foreach ($people as $person) {
            $correction = $this->checkArrayStructure($person->other_emails);
            if ($correction) {
                $num_problems++;
                $solutions[] = ['full_name' => $person->full_name, 'person_id' => $person->id, 'other_emails' => $correction];
            }

            $correction = $this->checkArrayStructure($person->other_phones);
            if ($correction) {
                $num_problems++;
                $solutions[] = ['full_name' => $person->full_name, 'person_id' => $person->id, 'other_phones' => $correction];
            }
        }
        // }

        $selected_team = $team;

        return view('admin.arrayfix', compact('num_problems', 'solutions', 'selected_team'));
    }

    public function arrayCheck(Request $request, $person_id = null)
    {
        $person = false;
        if (request('person_id')) {
            $person_id = request('person_id'); //Works for either form or URL
            $person = \App\Person::find($person_id);
        }
        if (! $person) {
            $person = \App\Person::where('team_id', Auth::user()->team->id)->first();
        }

        return view('admin.arraycheck', compact('person'));
    }

    public function userLogs()
    {
        $userlogs = UserLog::select('url',
                                    'type',
                                    DB::raw('AVG(time) as avgtime'),
                                    DB::raw('COUNT(*) as thecount'))
                            ->groupBy('url', 'type')
                            ->orderBy('avgtime', 'desc')
                            ->get();

        // $max_time = Carbon::parse(UserLog::max('created_at'));
        $min_time = Carbon::now()->toDateString();
        $max_time = Carbon::now()->toDateString();

        return view('admin.userlogs', compact('userlogs', 'max_time', 'min_time'));
    }

    public function errorLogs()
    {
        $logs_dir = storage_path().'/logs/';

        $logs = [];

        if ($handle = opendir($logs_dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    if (substr($entry, -4) == '.log') {
                        $logs[] = $entry;
                    }
                }
            }

            closedir($handle);
        }

        $errors = [];
        $lines = [];
        $i = 0;

        foreach ($logs as $the_log_file) {
            $logFile = file($logs_dir.$the_log_file);

            foreach ($logFile as $line_num => $line) {
                $maybe_date = substr($line, 1, 10);

                if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $maybe_date)) {
                    // $i++;

                    $i = substr($line, 1, 19); //[2019-07-28 04:28:12]
                }

                if ($the_log_file == 'query_timer.log') {
                    $echo_line = 'QUERY TIMER '.htmlspecialchars($line);
                } else {
                    $echo_line = htmlspecialchars($line);
                }

                $lines[$i][] = $echo_line;
            }
        }

        krsort($lines);

        $lines = collect($lines)->take(100);

        return view('admin.errors.error-logs', compact('lines'));
    }

    public function toDoGet()
    {
        $the_file = base_path().'/resources/views/admin/plans/todo.json';

        $strJsonFileContents = file_get_contents($the_file);
        $items = json_decode($strJsonFileContents, false);

        $items = collect($items);
        $items = $items->sortBy('done')->sortBy('item');

        $cats = $items->sortBy('cat')->pluck('cat')->unique()->toArray();

        return view('admin.plans.todo', compact('items', 'cats'));
    }

    public function toDoSave(Request $request)
    {
        $total = request('total');
        $item = [];

        for ($i = 0; $i <= $total; $i++) {
            if (request($i.'_item')) {
                if (request($i.'_done')) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }

                $items[] = ['item' => request($i.'_item'),
                             'cat' => request($i.'_cat'),
                             'done' => $checked, ];
            }
        }

        usort($items, [$this, 'cmp_item']);
        usort($items, [$this, 'cmp_cat']);

        $items_encoded = json_encode($items);

        // dd(request('0_item'), $j, $total, $items, $items_encoded);

        $the_file = base_path().'/resources/views/admin/plans/todo.json';

        $fp = fopen($the_file, 'w');
        fwrite($fp, $items_encoded);
        fclose($fp);

        return redirect('/admin/todo');
    }

    private function cmp_item($a, $b)
    {
        if ($a['item'] == $b['item']) {
            return 0;
        }

        return ($a['item'] < $b['item']) ? -1 : 1;
    }

    private function cmp_cat($a, $b)
    {
        if ($a['cat'] == $b['cat']) {
            return 0;
        }

        return ($a['cat'] < $b['cat']) ? -1 : 1;
    }
}
