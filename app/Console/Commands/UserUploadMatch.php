<?php

namespace App\Console\Commands;

use App\Participant;
use App\Person;
use App\Team;
use App\UserUpload;
use App\UserUploadData;
use App\Voter;
use App\VoterMaster;
use Illuminate\Console\Command;

class UserUploadMatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:user_upload_match {--upload_id=} {--team_id=} {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function getMatchedFieldIndex($upload, $db_field)
    {
        foreach ($upload->column_matches as $key => $match) {
            if ($match['db'] == $db_field) {
                return array_search($match['user'], $upload->columns);
            }
        }

        return null;
    }

    public function checkVoterIDPrefix($data)
    {
        return (substr($data, 0, 3) != 'MA_') ? 'MA_'.$data : $data;
    }

    public function handle()
    {
        $upload_id = $this->option('upload_id');
        $team_id = $this->option('team_id');
        $user_id = $this->option('user_id');

        session()->put('team_table', Team::find($team_id)->db_slice);

        $upload = UserUpload::find($upload_id);

        //////////////////////// Identifying Fields ////////////////////////

        foreach (['voter_id', 'first_name', 'last_name', 'address_city', 'primary_email'] as $field) {
            $field_indexes[$field] = $this->getMatchedFieldIndex($upload, $field);
        }

        //////////////////////////////// Loop ////////////////////////////////

        $at_at_time = 247;

        $count = 0;

        while ($upload->count > $upload->matched_count) {
            $voters_userdata = UserUploadData::where('team_id', $team_id)
                                            ->where('upload_id', $upload->id)
                                            ->whereNull('matched_at')
                                            ->take($at_at_time)
                                            ->get();

            //dd($voters_userdata);
            if ($voters_userdata->count() < 1) {
                break;
            }
            echo " So far ".$count."\r";

            foreach ($voters_userdata as $userdata) {
                $participant = null;
                $person = null;
                $voter = null;

                $array = $userdata->data;

                $data = [];
                foreach (['voter_id', 'first_name', 'last_name', 'address_city', 'primary_email'] as $field) {
                    if ($field_indexes[$field] === null) {
                        $data[$field] = null;
                    } else {
                        $data[$field] = trim($array[$field_indexes[$field]]);
                    }
                }
                //dd($userdata, $data);

                //dd($data);

                ////////////////////////// Matching Logic //////////////////////////

                // Look for a matching Voter

                if ($data['voter_id']) {
                    $ma_voter_id = $this->checkVoterIDPrefix($data['voter_id']);
                    echo $ma_voter_id."\n";
                    //dd();
                    $voter = VoterMaster::find($ma_voter_id);
                }
                //dd($voter);

                if (! $voter && $data['first_name']
                            && $data['last_name']
                            && $data['address_city']) {
                    $voters = VoterMaster::where('first_name', $data['first_name'])
                                ->where('last_name', $data['last_name'])
                                ->where('address_city', $data['address_city'])
                                ->get();
                    //dd($voters, $data);

                    if ($voters->count() == 1) {
                        $voter = $voters->first();
                    }
                }

                if (! $voter && $data['primary_email']) {

                    // $voter = VoterMaster::where('emails', 'like'. '%'.$data['primary_email'].'%')
                    //               ->first();
                }

                if ($voter) {
                    $userdata->voter_id = $voter->id;
                }

                ///////////////////////////// Match Participants /////////////////////////////

                if (Team::find($team_id)->app_type == 'campaign') {
                    if ($voter) {
                        $participant = Participant::where('team_id', $team_id)
                                                  ->where('voter_id', $voter->id)
                                                  ->first();
                    }

                    if (! $participant && $data['voter_id']) {
                        $participant = Participant::where('team_id', $team_id)
                                                  ->where('voter_id', $this->checkVoterIDPrefix($data['voter_id']))
                                                  ->first();
                    }

                    if (! $participant && $data['primary_email']) {
                        $participant = Participant::where('team_id', $team_id)
                                                  ->where('primary_email', $data['primary_email'])
                                                  ->first();
                    }

                    if (! $participant && $data['first_name']
                                      && $data['last_name']
                                      && $data['address_city']) {
                        $participants = Participant::where('team_id', $team_id)
                                                   ->where('first_name', $data['first_name'])
                                                   ->where('last_name', $data['last_name'])
                                                   ->where('address_city', $data['address_city'])
                                                   ->get();

                        if ($participants->count() == 1) {
                            $participant = $participants->first();
                        }
                    }

                    if ($participant) {
                        $userdata->participant_id = $participant->id;
                        if ($participant->voter_id) {
                            $userdata->voter_id = $participant->voter_id;
                        }
                    }
                }

                ///////////////////////////// Match People /////////////////////////////

                if (Team::find($team_id)->app_type != 'campaign') {
                    if ($voter) {
                        $person = Person::where('team_id', $team_id)
                                        ->where('voter_id', $voter->id)
                                        ->first();
                    }

                    if (! $person && $data['voter_id']) {
                        $person = Person::where('team_id', $team_id)
                                        ->where('voter_id', $this->checkVoterIDPrefix($data['voter_id']))
                                        ->first();
                    }

                    if (! $person && $data['primary_email']) {
                        $person = Person::where('team_id', $team_id)
                                        ->where('primary_email', $data['primary_email'])
                                        ->first();
                    }

                    if (! $person && $data['first_name']
                                 && $data['last_name']
                                 && $data['address_city']) {
                        $people = Person::where('team_id', $team_id)
                                         ->where('first_name', $data['first_name'])
                                         ->where('last_name', $data['last_name'])
                                         ->where('address_city', $data['address_city'])
                                         ->get();

                        if ($people->count() == 1) {
                            $person = $people->first();
                        }
                    }

                    if ($person) {
                        $userdata->person_id = $person->id;
                        if ($person->voter_id) {
                            $userdata->voter_id = $person->voter_id;
                        }
                    }
                }

                ///////////////////////////// END LOOP /////////////////////////////

                $upload->matched_count++;
                $userdata->matched_at = now();
                $userdata->save();
            }

            $upload->save();
        }
    }
}
