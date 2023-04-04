<?php

namespace App\Console\Commands\OneTime;

use DB;
use Illuminate\Console\Command;

class ArchiveUserLogAjax extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:user_logs_ajax';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes all ajax rows from user_logs table and puts them in user_logs_ajax table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        ////////////////// SET UP

        DB::table('user_logs_ajax')->truncate();

        $count_old = DB::table('user_logs')->where('type', 'ajax')->count();

        $i = 0;
        $at_a_time = 100;
        $start = time();
        $remaining = 0;
        $new_rate = 0;
        $num_inserted = 0;
        $percentage = 0;
        $remaining = 0;

        while ($num_inserted < $count_old) {

            ////////////////// GET DATA

            $rows = DB::table('user_logs')->select(['mock_id',
                                                    'user_id',
                                                    'team_id',
                                                    'name',
                                                    'username',
                                                    'url',
                                                    'type',
                                                    'time',
                                                    'created_at',
                                                    'updated_at',
                                                    ])
                                          ->where('type', 'ajax')
                                          ->take($at_a_time)
                                          ->skip($at_a_time * $i)
                                          ->get();

            ////////////////// COPY

            foreach ($rows as $row) {
                DB::table('user_logs_ajax')->insert(collect($row)->toArray());
            }

            ////////////////// ECHO

            $num_inserted = $num_inserted + $rows->count();

            $elapsed = time() - $start;
            $percentage = $at_a_time * $i / $count_old;

            if ($elapsed > 0 && $i > 0) {
                $new_rate = ($at_a_time * $i) / $elapsed;
                $new_remaining = ($count_old - ($at_a_time * $i)) / $new_rate;
                $new_remaining = round($new_remaining);
                if (! $remaining) {
                    $remaining = $new_remaining;
                }
                if ($new_remaining < $remaining) {
                    $remaining = $new_remaining;
                } //Otherwise confusing
            }

            echo number_format($num_inserted).' of '.number_format($count_old).' -- '
                    .round($remaining / 60, 1).' mins remaining -- '
                    .round($percentage * 100).'%'
                    ."\r\n";

            ////////////////// INCREMENT

            $i++;
        }

        ////////////////// FINISH AND DELETE OLD DATA

        $count_new = DB::table('user_logs_ajax')->count();

        if ($count_old == $count_new) {

            // Maybe do the delete manually?
            // DB::table('user_logs')->where('type', 'ajax')->delete();

            dd('Transferred '.number_format($count_old).' rows. It took '.round($elapsed / 60, 1).' minutes');
        } else {
            dd('Error, counts do not match for some reason.');
        }
    }
}
