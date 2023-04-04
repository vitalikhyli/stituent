<?php

namespace App\Console\Commands;

use App\Election;
use DB;
use Illuminate\Console\Command;
use Solarium\Client;

class SolrElections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:solr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the voter election histories to the solr instance.';

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
    public function handle(Client $client)
    {
        //////////////////////////////////////////////////////////////////////////
        // Set up Solarium object
        // Also, must run: composer require solarium/solarium
        // Then must add: /config/solarium.php

        //////////////////////////////////////////////////////////////////////////
        // Go through voter records in chunks

        $count = 0;
        $go = true;
        $at_a_time = 20;
        $artificial_limit = 8000000;

        while ($go == true) {
            $count++;

            $voters = DB::table('x_MA_STATE')
                        ->skip($count * $at_a_time)
                        ->take($at_a_time)
                        ->get();

            if (! $voters->first() || ($artificial_limit < $count * $at_a_time)) {
                echo "\r\n Done \r\n";
                $go = false;
            } else {
                $update = $client->createUpdate();

                echo str_repeat('-', 48).'/ next '.$at_a_time." records:\r\n";

                foreach ($voters as $voter) {
                    $election_string = $this->getElectionString($voter);

                    $election_count_string = null;

                    for ($since = 2020; $since >= 2010; $since--) {
                        $election_count_string .= $this->getCountString($since, $voter).' ';
                    }

                    $election_count_string = trim($election_count_string);

                    $doc = $update->createDocument();

                    $doc->voter_id = $voter->id;
                    $doc->elections = $election_string;
                    $doc->counts = $election_count_string;

                    $update->addDocument($doc);

                    $this->info($voter->id);
                    echo "--\r\n";
                    echo $election_string."\r\n";
                    echo "--\r\n";
                    echo $election_count_string."\r\n";
                    echo "--\r\n";
                }

                $update->addCommit();

                $result = $client->update($update);

                // echo " Results: ".($count * $at_a_time)." ".$result."\r\n";
            }
        }
    }

    public function getElectionString($voter)
    {
        $string = null;

        $elections = json_decode($voter->elections, true);

        foreach ($elections as $election => $participation) {
            $string .= $election.':'.$participation.' ';
        }

        return trim($string);
    }

    public function getCountString($since, $voter)
    {
        $string = null;

        $elections = json_decode($voter->elections, true);

        $elections_parsed = [];

        foreach ($elections as $election => $participation) {
            $elections_parsed[$election] = $this->parseElection($election);
        }

        $keys = ['statewide',
                 'local',
                 'presidential',
                 'prez_primary',
                 'town_meeting',
                 'general',
                 'special',
                 'primary',
                ];

        foreach ($keys as $key) {
            $count = 0;

            foreach ($elections_parsed as $id => $election) {
                if ($election['year'] >= $since) {
                    if ($election[$key]) {
                        $count++;
                    }
                }
            }

            $string .= 'since:'.$since.'-'.$key.':'.$count.' ';
        }

        return trim($string);
    }

    public function parseElection($string)
    {
        $components = explode('-', $string);

        // MA-2000-11-07-STATE-0000
        // MA-2009-11-03-L0000-0035

        $data = [];

        $data['id'] = $string;
        $data['state'] = $components[0];
        $data['year'] = $components[1] * 1;
        $data['date'] = $components[1].'-'.$components[2].'-'.$components[3];
        $data['city_code'] = $components[5] * 1;

        $type = $components[4];

        $data['statewide'] = ($type == 'STATE') ? true : false;
        $data['general'] = ($type == 'STATE') ? true : false;
        $data['presidential'] = ($type == 'STATE' && $data['year'] % 4 == 0) ? true : false;

        $data['statewide'] = (substr($type, 0, 1) == 'S') ? true : false;
        $data['local'] = (substr($type, 0, 1) == 'L') ? true : false;

        $data['special'] = (substr($type, 1, 1) == 'S') ? true : false;
        $data['primary'] = (substr($type, 1, 1) == 'P') ? true : false;
        $data['prez_primary'] = (substr($type, 2, 1) == 'P') ? true : false;

        $data['town_meeting'] = (substr($type, 1, 2) == 'TM') ? true : false;

        return $data;
    }
}
