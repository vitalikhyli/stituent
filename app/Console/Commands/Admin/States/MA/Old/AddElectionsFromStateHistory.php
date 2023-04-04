<?php

namespace App\Console\Commands\Admin\States\MA\Old;

use App\StateVotingHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AddElectionsFromStateHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:add_elections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uses the MA_STATE_VOTING_HISTORY file to fill in elections array on voter records.';

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
        // *Get all voting history records where not MA id yet
        //   *Save new MA id
        // *Get all voting history records where null imported_at (with voter)
        //   Get election ID
        //   Save election_id
        //   get voter elections array
        //   Add this election to array
        //   Save voter
        //   Save imported_at timestamp

        // MA_01ACL0156003
        // MA_10MSN0767000

        $not_ma = StateVotingHistory::where('Voter ID Number', 'NOT LIKE', 'MA_%')
                                    ->get();
        echo 'About to update '.$not_ma->count()." to MA_\n";
        $ma_voter_col = 'Voter ID Number';
        foreach ($not_ma as $svh) {
            $ma_voter_id = 'MA_'.$svh->$ma_voter_col;
            $svh->$ma_voter_col = $ma_voter_id;
            $svh->save();
        }
        echo "About to do voter\n";
        //dd('Laz');

        $state_voting_histories = StateVotingHistory::whereNull('imported_at')
                                                    //->with('voter')
                                                    //->take(100)
                                                    ->get();

        echo $state_voting_histories->count()."\n";

        foreach ($state_voting_histories as $key => $svh) {

            if ($key % 1000 == 0) {
                echo "$key of ".$state_voting_histories->count()."\n";
            }

            // MA-2016-11-08-STATE0000000000-329-U-0
            $state = 'MA';
            $ma_electiondate_col = 'Election Date';
            $ma_type_col = 'Type of Election';
            $ma_city_col = 'City/ Town Code';
            $ma_party_col = 'Record Seq. #';
            $ma_party_voted_col = 'Party Voted';

            $date = $svh->$ma_electiondate_col->format('Y-m-d');
            $type = str_pad($this->lookupElectionType($svh->$ma_type_col), 5, '0');
            $type = trim($type);
            $city = str_pad($svh->$ma_city_col, 4, '0', STR_PAD_LEFT);
            if (! Str::startsWith($type, 'L')) {
                $city = '0000';
            }

            $election_id = "$state-$date-$type-$city";

            $city = str_pad($svh->$ma_city_col, 4, '0', STR_PAD_LEFT);
            $party = $svh->$ma_party_col;
            $ballot = $svh->$ma_party_voted_col;
            if (! $ballot) {
                $ballot = 0;
            }
            $details = $city.'-'.$party.'-'.$ballot;

            //dd($election_id, $details);

            $voter = $svh->voter;
            if ($voter) {
                $elections = $voter->elections;
                $elections[$election_id] = $details;
                $voter->elections = $elections;
                //dd($voter);
                $voter->save();
                $svh->imported_at = Carbon::now();
                $svh->election_id = $election_id;
                $svh->save();
                echo 'SAVED: '.$svh->$ma_voter_col."\r";
            } else {
                echo '******** Not Found: '.$svh->$ma_voter_col."\n";
            }
        }
    }

    public function lookupElectionType($str)
    {
        $lookup = [
            'Local Election' => 'L',
            'Presidential Primary' => 'PP',
            'State Primary' => 'SP',
            'State Election' => ' STATE',
            'Local Primary' => 'LP',
            'Special State' => 'SS',
            'Primary Election' => 'PE',
            'General Election' => 'G',
            'Local Special' => 'LS',
            'Special State Primary' => 'SSP',
            'Local Town Meeting' => 'LTM',
            'Local Rep Town Mtg' => 'LRTM',
        ];

        return $lookup[ucwords(strtolower($str))];
    }

    /*
        // MA-2016-11-08-STATE0000000000-329-U-0
        $state = $this->voter_state;
        $date = $this->election_date;
        $type = str_pad($this->election_type, 5, '0');
        $city = str_pad($this->election_city_code, 4, '0', STR_PAD_LEFT);
        if (!Str::startsWith($type, 'L')) {
            $city = '0000';
        }

        return "$state-$date-$type-$city";
    */
}
