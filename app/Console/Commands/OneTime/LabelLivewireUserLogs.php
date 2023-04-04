<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;

use App\UserLog;

class LabelLivewireUserLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:user_logs_wire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds "/livewire/" in request url and flags userLog as "wire"';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->the_fateful_day_we_started_to_use_livewire = '2020-02-25';
        $this->at_a_time = 1000;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $do_it_again    = true;
        $running        = 0;

        while ($do_it_again) {
            $count_processed = $this->processWireLogs();
            if (!$count_processed) $do_it_again = false;
            $running += $count_processed;
        }

        $this->info('Updated '.number_format($running).' userLogs, Chief.');

    }

    public function processWireLogs()
    {
        $logs = UserLog::where('created_at', '>=', $this->the_fateful_day_we_started_to_use_livewire)
                       ->whereNull('type')
                       ->where('url', 'like', '%'.'/livewire/'.'%')
                       ->take($this->at_a_time)
                       ->get();

        $count = 0;

        foreach($logs as $log) {

            $this->info($log->created_at."\t".str_replace(config('app.url'), '', $log->url));

            $log->type = 'wire';
            $log->save();

            $count++;
            
        }

        return $count;
    }
}
