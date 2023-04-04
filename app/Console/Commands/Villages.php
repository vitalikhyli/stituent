<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\VoterMaster;
use App\Participant;

class Villages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:villages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace the address city with the village name for better mailing.';

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
     * @return int
     */
    public function handle()
    {
        // ===================================> Northampton
        
        // LEEDS 01053 - Voters

        $leeds = VoterMaster::where('address_zip', '01053')
                            ->where('address_city', 'LIKE', 'Northampton')
                            ->get();
        $this->info('About to change '.$leeds->count().' residential voters to Leeds.');
        foreach ($leeds as $index => $voter) {
            $voter->address_city = 'Leeds';
            $voter->save();
            echo "\t".($index+1)." processed.\r";
        }
        $leeds_mailing = VoterMaster::where('city_code', 214)
                                    ->where('mailing_info', 'LIKE', '%01053%')
                                    ->where('mailing_info', 'LIKE', '%Northampton%')
                                    ->get();
        foreach ($leeds_mailing as $index => $voter) {
            $mailing_info = $voter->mailing_info;
            if ($mailing_info['zip'] == '01053') {
                $mailing_info['city'] = 'Leeds';
                $voter->mailing_info = $mailing_info;
                $voter->save();
            }
            echo "\t".($index+1)." processed.\r";
        }

        // LEEDS 01053 - Participants

        $leeds = Participant::where('address_zip', '01053')
                            ->where('address_city', 'LIKE', 'Northampton')
                            ->get();
        $this->info('About to change '.$leeds->count().' participants to Leeds.');
        foreach ($leeds as $index => $participant) {
            $participant->address_city = 'Leeds';
            $participant->save();
            echo "\t".($index+1)." processed.\r";
        }

        // FLORENCE 01062 - Voters

        $florence = VoterMaster::where('address_zip', '01062')
                            ->where('address_city', 'LIKE', 'Northampton')
                            ->get();
        $this->info('About to change '.$florence->count().' residential voters to Florence.');
        foreach ($florence as $index => $voter) {
            $voter->address_city = 'Florence';
            $voter->save();
            echo "\t".($index+1)." processed.\r";
        }
        $florence_mailing = VoterMaster::where('city_code', 214)
                                    ->where('mailing_info', 'LIKE', '%01062%')
                                    ->where('mailing_info', 'LIKE', '%Northampton%')
                                    ->get();
        foreach ($florence_mailing as $index => $voter) {
            $mailing_info = $voter->mailing_info;
            if ($mailing_info['zip'] == '01062') {
                $mailing_info['city'] = 'Florence';
                $voter->mailing_info = $mailing_info;
                $voter->save();
            }
            echo "\t".($index+1)." processed.\r";
        }

        // FLORENCE 01062 - Participants

        $florence = Participant::where('address_zip', '01062')
                            ->where('address_city', 'LIKE', 'Northampton')
                            ->get();
        $this->info('About to change '.$florence->count().' participants to Florence.');
        foreach ($florence as $index => $voter) {
            $voter->address_city = 'Florence';
            $voter->save();
            echo "\t".($index+1)." processed.\r";
        }
    }
}
