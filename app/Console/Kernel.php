<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //------------------------------------------------------------------------------------
        // MASTER TABLE COMMANDS

        // $schedule->command('cf:ma_master 
        //     --file_path_voters='        .storage_path().'/uploads-master-voter/MA_20211_MidExportVoters_YEAR_BIRTH_ONLY.tab 
        //     --file_path_districts='     .storage_path().'/uploads-master-voter/MA_20211_MidExport_Districts.tab 
        //     --file_path_district_voter='.storage_path().'/uploads-master-voter/MA_20211_MidExport_VoterDistricts.tab 
        //     --file_path_elections='     .storage_path().'/uploads-election-history/MA_20211_MidExport_Elections.tab 
        //     --file_path_election_voter='.storage_path().'/uploads-election-history/MA_20211_MidExport_VoteHistory.tab'
        //         )
        //         ->yearlyOn($month = 2, $day = 20, $time = '17:35')
        //         ->appendOutputTo(storage_path().'/logs/ma_master.log');

        // $schedule->command('cf:ma_voters 
        //     --file_path_voters='        .storage_path().'/uploads-master-voter/{FILENAME} 
        //     --file_path_districts='     .storage_path().'/uploads-master-voter/{FILENAME} 
        //     --file_path_district_voter='.storage_path().'/uploads-master-voter/{FILENAME}
        //     --clear_districts 
        //     --clear_district_voters'
        //         )
        //         ->yearlyOn($month = 2, $day = 20, $time = '19:00');

        // $schedule->command('cf:ma_elections
        //     --file_path_elections='     .storage_path().'/uploads-election-history/MA_20211_MidExport_Elections.tab 
        //     --file_path_election_voter='.storage_path().'/uploads-election-history/MA_20211_MidExport_VoteHistory.tab
        //     --clear_elections 
        //     --clear_election_voters'
        //         )
        //         ->yearlyOn($month = 2, $day = 20, $time = '17:35')
        //         ->appendOutputTo(storage_path().'/logs/ma_elections.log');


        //------------------------------------------------------------------------------------
        // OTHER COMMANDS
                     
        $schedule->command('cf:mail')
                 ->everyFiveMinutes()
                 ->appendOutputTo(storage_path().'/logs/mail.log');

        $schedule->command('cf:geocode')
                 ->hourly()
                 ->appendOutputTo(storage_path().'/logs/geocoder.log');

        $schedule->command('cf:cache_lists')
                 ->daily();

        $schedule->command('cf:build_streets', ['--update'])
                 ->hourly();

        $schedule->command('cf:group_update_counts')
                 ->everyTenMinutes();

        // $schedule->command('cf:elections', ['--state' => 'MA'])
        //          ->weeklyOn(3, '01:35')
        //          ->appendOutputTo(storage_path()."/logs/elections.log");

        // $schedule->command('marketing:master')
        //          ->hourly();

        $schedule->command('cf:billygoat')
                 ->everyTenMinutes();

        $schedule->command('cf:create_mysql_dump')
                 ->dailyAt('04:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
