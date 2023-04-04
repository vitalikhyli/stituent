<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use Carbon\Carbon;


class QueriesController extends Controller
{
	public function updateHistory()
	{
		$days = DB::table('query_unsloth_logs')->select('date')->groupBy('date')->get();
		foreach($days as $day) {
    		DB::table('query_unsloth_history')->insert(['date' => Carbon::parse($day->date)->toDateString()]);
    	}
	}

    public function index()
    {
		// $queries = $this->getLogLines('query.log');

    	$this->updateHistory();

    	if (!isset($_GET['mode']) || $_GET['mode'] == '') {

    		$queries = DB::table('query_unsloth_logs')->where('explain_type', 'all')->orderBy('tables')->get();

    	} elseif ($_GET['mode'] == 'everything') {

    		$queries = DB::table('query_unsloth_logs')->get();

    	} elseif ($_GET['mode'] == 'slow') {

    		$queries = DB::table('query_unsloth_logs')->where('time', '>', 200)->get();

    	}


		$queries = $queries->groupBy('hash');

    	return view('admin.queries.index', ['queries' => $queries]);
    }

	public function getLogLines($log_name)
    {
        $logs_dir = storage_path().'/logs/';

        $logs = [];

        if ($handle = opendir($logs_dir )) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != "..") {

                    if (substr($entry,-4) == '.log') $logs[] = $entry;
                }
            }

            closedir($handle);
        }

        $errors = [];
        $lines = [];

        foreach ($logs as $the_log_file) {

            $logFile = file($logs_dir.$the_log_file);
            
            if ($the_log_file != $log_name) continue;

            foreach ($logFile as $line_num => $line) {

            	$lines[] = json_decode($line);
            }


        }
 
        $queries = collect($lines)->sortBy('date');

        //-------------------------------------------- Attach Bindinngs
        foreach($queries as $key => $query) {

        	if (str_starts_with($query->sql, 'explain')) {
        		unset($queries[$key]);
        	}

        	$sql_full = $query->sql;

        	$bindings = $query->bindings;

        	while(strpos($sql_full, '?')) {

        		$sql_full = str_replace('?', reset($bindings), $sql_full);

        		$bindings = array_shift($bindings);

        	}

        	$query->sql_full = $sql_full;

        	try {

        		$result = DB::select("explain ".$sql_full);
        		$query->explain_type = $result[0]->type;
        		$query->explain_key = $result[0]->key;
        		$query->explain_rows = $result[0]->rows;

        	} catch (\Exception $e) {

        		$query->explain_type = null;
				$query->explain_key = null;
				$query->explain_rows = null;

        	}

        }

        //-------------------------------------------- Group

        $queries = $queries->groupBy('hash');

        return $queries;
    }

}
