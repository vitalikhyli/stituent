<?php

namespace App\Console\Commands;

use App\ParticipantTag;
use App\UserUploadData;
use Illuminate\Console\Command;

class ManuallyAddTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:manually_add_tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a tag from an upload';

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
        $upload_records = UserUploadData::where('upload_id', 19)
                                        //->whereNotNull('voter_id')
                                        ->whereNotNull('participant_id')
                                        ->get();
        $c = 0;
        foreach ($upload_records as $ur) {
            $partag = ParticipantTag::where('tag_id', 18)
                                    ->where('participant_id', $ur->participant_id)
                                    //->where('voter_id', $ur->voter_id)
                                    ->first();
            if (! $partag) {
                $partag = new ParticipantTag;
                $partag->team_id = $ur->team_id;
                $partag->user_id = 705;
                $partag->voter_id = $ur->voter_id;
                $partag->participant_id = $ur->participant_id;
                $partag->tag_id = 18;
                $partag->save();
                echo($c++).'\r';
            }
        }
    }
}
