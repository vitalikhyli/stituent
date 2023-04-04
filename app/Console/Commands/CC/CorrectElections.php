<?php

namespace App\Console\Commands\CC;

use App\Models\CC\CCVoter;
use App\VoterMaster;
use DB;
use Illuminate\Console\Command;

class CorrectElections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:correct_elections {--last_voter=} {--reverse} {--chunk=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elections data populated wih voterID not voter_code, needs to get any missed';

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

        //dd(VoterMaster::take(1)->with('ccVoter')->get());
        $last_voter_code = '000000000000';
        if ($this->option('reverse')) {
            $last_voter_code = '999999999999';
        }
        if ($this->option('last_voter')) {
            $last_voter_code = $this->option('last_voter');
        }
        $totalcount = 0;
        $chunksize = 1000;
        if ($this->option('chunk')) {
            $chunksize = $this->option('chunk');
        }

        while (1) {
            echo date('Y-m-d h:i:s').' '.$last_voter_code.', '.$totalcount."\n";

            if ($this->option('reverse')) {
                $election_counts = DB::connection('cc_remote')
                                     ->select("SELECT voter_code, COUNT(*) as count 
                                                FROM cms_election_data
                                                WHERE voter_code < '".$last_voter_code."' 
                                                GROUP BY voter_code 
                                                ORDER BY voter_code desc
                                                LIMIT $chunksize");
            } else {
                $election_counts = DB::connection('cc_remote')
                                     ->select("SELECT voter_code, COUNT(*) as count 
                                                FROM cms_election_data
                                                WHERE voter_code > '".$last_voter_code."' 
                                                GROUP BY voter_code 
                                                ORDER BY voter_code
                                                LIMIT $chunksize");
            }
            if (count($election_counts) < 1) {
                break;
            }
            $count_lookup = [];
            $voter_ids = [];
            foreach ($election_counts as $election_count) {
                $count_lookup['MA_'.$election_count->voter_code] = $election_count->count;
                $voter_ids[] = 'MA_'.$election_count->voter_code;
                $last_voter_code = $election_count->voter_code;
            }
            //dd($voter_ids);

            $voters = VoterMaster::whereIn('id', $voter_ids)
                                 ->with('ccVoter')
                                 ->get();

            foreach ($voters as $voter) {
                $totalcount++;
                if (count($voter->elections) != $count_lookup[$voter->id]) {
                    echo $voter->id.' '.count($voter->elections).' vs. '.$count_lookup[$voter->id].' (ACTUAL)';

                    // ======================================================> Update Elections

                    $ccvoter = $voter->ccVoter;

                    //dd($voter, $ccvoter);
                    if ($ccvoter) {
                        $elections = [];
                        foreach ($ccvoter->elections as $election) {
                            $elections[$election->code] = $election->voter_info;
                        }
                        ksort($elections);
                        $voter->elections = $elections;
                        $voter->save();
                        echo " - UPDATED\n";
                    } else {
                        echo " - VOTER MISSING!\n";
                    }
                }
            }
        }
    }
}
