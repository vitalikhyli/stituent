<?php

namespace App\Models\Admin;

use App\Models\Admin\DataImport;
use App\Models\Admin\DataJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataWorker extends Model
{
    use SoftDeletes;

    protected $casts = [
        'jobs' => 'array',
    ];

    // protected $table = 'data_workers';

    // public function __construct()
    // {
    //     $this->running = true;
    // }

    public function getLastLogAttribute()
    {
        $log = $this->log;
        $log_arr = explode("\r", $log);
        $lastindex = count($log_arr) - 2;
        if (isset($log_arr[$lastindex])) {
            return $log_arr[$lastindex];
        }
    }

    public function markInterrupted()
    {
        $this->interrupted = 1;
        $this->log .= 'Interrupted.'."/s                \r";
        $this->save();
        $this->delete();
    }

    public function work()
    {
        $start = microtime(true);   // Needed?
        $this->ping = time(); //Tells the system it is still operating
        $this->save();

        $job_count = DataJob::where('done', false)->count();

        if ($job_count <= 0) {
            return 0; //No jobs left
        } else {
            $job = DataJob::where('done', false)->orderBy('id')->first();

            if (! isset($this->jobs[$job->id])) {
                $jobs = $this->jobs;
                $jobs[$job->id] = date('Y-m-d g:ia');
                $this->jobs = $jobs;
                $this->save();
            }

            $import = DataImport::find($job->data_import_id);

            if (! $job->start) {
                $job->start();
            }

            switch ($job->type) {
                case 'import':
                $arguments = json_decode($job->arguments, true);
                $remaining = $import->import($arguments);
                break;

                case 'enrich':
                $remaining = $import->enrich();
                break;

                case 'createHouseholds':
                $remaining = $import->createHouseholds();
                break;

                case 'deploy':
                $remaining = $import->deploy();
                break;

                case 'deployHouseholds':
                $remaining = $import->deployHouseholds();
                break;

                case 'clearHouseholds':
                $remaining = $import->clearHouseholds();
                break;

                case 'defineSlice':
                $arguments = json_decode($job->arguments, true);
                $remaining = $import->defineSlice($arguments);
                break;

                case 'populateSlice':
                $remaining = $import->populateSlice();
                break;

                case 'copy':
                $remaining = $import->copy();
                break;

                case 'ready':
                $remaining = $import->isReady();
                break;

                case 'notReady':
                $remaining = $import->isNotReady();
                break;

                case 'merge':
                $arguments = json_decode($job->arguments, true);
                $remaining = $import->merge($arguments);
                break;

                case 'populateTableWithSliceOfMaster':
                $arguments = json_decode($job->arguments, true);
                $remaining = $import->populateTableWithSliceOfMaster($arguments);
                break;

                case 'populateSliceHouseholds':
                $arguments = json_decode($job->arguments, true);
                $remaining = $import->populateSliceHouseholds($arguments);
                break;

            }

            if ($remaining['error'] == true) {
                $log = 'Error in '.$remaining['function']
                        .', during: ('.$remaining['part']
                        .") for: '".$remaining['name']
                        ."' -- Stopped at: "
                        .$remaining['at'];

                $this->log .= $log."/s                \r";
                echo "\r\n\033[1;33m*** ".$log." \033[0m \r\n";

                $this->save();

                return 'INTERRUPT'; //return this to Worker Command loop
            } else {
                $job->remaining = $remaining;
                $job->save();

                if ($remaining <= 0) {
                    $job->markAsDone();
                }

                $size = 10000; //Assumed
                $speed = ($size / (microtime(true) - $start));
                $log = '  '.number_format(DataJob::where('done', 0)->count(), 0, '.', ',').' Jobs left --> '.$job->type.' --> '.number_format($remaining, 0, '.', ',').'   @ '.number_format(round($speed), 0, '.', ',')."/s     \r";
                $this->log .= $log;
                echo $log;

                $this->save();

                return $job_count;
            }
        }
    }
}
