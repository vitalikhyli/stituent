<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\VoterMaster;
use App\ElectionProfile;

use Carbon\Carbon;

class ElectionYearCounts extends Command
{
    protected $signature = 'cf:election_year_counts {--show} {--city=} {--limit=} {--nulls}';
    protected $description = 'Command description';

    public $types;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $willy = [];

        // if ($willy[1212] && isset($willy[1212])) {
        //     dd('OK');
        // } else {
        //     dd('error');
        // }

        $this->types = ['L0000',
                        'SP000',
                        'STATE'];

        $this->ranges = [];

        $this->year = Carbon::now()->format('Y');
        $this->earliest = 2002;

        $num_years = ($this->year - $this->earliest);

        for ($y=0; $y < $num_years + 1; $y++) { 
            $this->ranges[] = ($this->year-$y).':'.$this->year;
        }

        session()->put('team_state', 'MA');
 
        //----------------------------------------------------------------------------
        $voters = VoterMaster::whereNotNull('elections');

        if ($this->option('city')) {
            $voters = $voters->where('address_city', $this->option('city'));
        }

        if ($this->option('nulls')) {

            $voters_table   = (new VoterMaster)->getTable();
            $join_table     = (new ElectionProfile)->getTable();

            $voters = $voters->leftJoin($join_table, $voters_table.'.id', $join_table.'.voter_id')
                             ->whereNull('year_count');

        }

        if ($this->option('limit')) {
            $voters = $voters->take($this->option('limit'));
        }

        // dd($voters->toSql());

        $voters = VoterMaster::whereIn('id', $voters->pluck($voters_table.'.id'));
        $voters = $voters->get();

        // dd($voters->pluck('voter_id'));
        
        //----------------------------------------------------------------------------

        foreach($voters as $voter) {

            $statString = $this->parseAndCount($voter->elections);
            
            if ($this->option('show')) {

                $this->info($voter->full_name);
                echo $statString."\n";

            } else {

            }

            $profile = $voter->profile;

            if (!$profile) {
                $profile = new ElectionProfile;
                $profile->voter_id = $voter->id;
            }

            $profile->year_count = $statString;
            $profile->save();

        }

    }

    public function parseAndCount($elections)
    {
        $arr = [];

        foreach($this->types as $type) {

            $new_arr = $this->getStatStringForType($type, $elections);

            $arr = array_merge($arr, $new_arr);

        }

        return implode(' ', $arr);
    }

    public function getStatStringForType($type, $elections)
    {
        $arr = [];

        foreach($this->ranges as $range) {

            $new_arr = $this->getStatStringForRangeAndType($range, $type, $elections);

            $arr = array_merge($arr, $new_arr);

        }

        return $arr;
    }

    public function getStatStringForRangeAndType($range, $type, $elections)
    {
        $arr = [];

        $count = 0;

        $range_start = substr($range, 0, 4);
        $range_end = substr($range, 5, 4);

        foreach($elections as $election => $participation) {

            $election_year = substr($election, 3, 4) * 1; // MA_2020

            if ($election_year >= $range_start && $election_year <= $range_end) {

                if(strpos($election, $type)) {
                    $count++;
                }
            }

        }

        $arr[] = $range.'-'.$type.'='.$count;

        for ($c=$count - 1; $c >= 0; $c--) { 
            $arr[] = $range.'-'.$type.'>'.$c;
        }

        return $arr;
    }  

}
