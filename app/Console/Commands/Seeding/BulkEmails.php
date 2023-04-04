<?php

namespace App\Console\Commands\Seeding;

use App\BulkEmail;
use App\BulkEmailQueue;
use App\Models\CC\CCBulkEmail;
use App\Models\CC\CCBulkEmailVoter;
use App\Models\CC\CCVoter;
use App\Models\CC\CCVoterArchive;
use App\Person;
use App\Team;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BulkEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_bulk {--campaign=}';

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
    public function handle()
    {
        //if (env('LOCAL_MACHINE') != 'Slothe')             return;
        if (! $this->confirm('BULK EMAILS: Do you wish to continue?')) {
            return;
        }

        if ($this->option('campaign')) {
            $valid_campaign_ids = [$this->option('campaign')];
            echo date('Y-m-d h:i:s').' Starting Bulk Emails with Campaign ID '.$this->option('campaign')."\n";
        } else {
            echo "No campaign";
            return;
            $valid_campaign_ids = Team::whereAppType('office')
                                      ->pluck('old_cc_id')
                                      ->unique();
            echo date('Y-m-d h:i:s').' Starting Bulk Emails with '.count($valid_campaign_ids)." Campaigns\n";
        }

        foreach ($valid_campaign_ids as $campaign_id) {
            $team = Team::where('old_cc_id', $campaign_id)->first();
            $cc_bulks = CCBulkEmail::where('cms_bulkemail_tracker_campaignID', $campaign_id)
                                   ->get();

            echo date('Y-m-d h:i:s').' '.$team->name.' Bulk Emails: '.$cc_bulks->count()."\n";
            if (!session('live')) {
                return;
            }
            foreach ($cc_bulks as $cc_bulk) {
                $bulk = BulkEmail::where('old_cc_id', $cc_bulk->cms_bulkemail_tracker_id)->first();
                if (! $bulk) {
                    $bulk = new BulkEmail;
                    $bulk->team_id = $team->id;
                    $user = User::where('current_team_id', $team->id)
                                ->where('username', '=', $cc_bulk->cms_bulkemail_tracker_sent_by_login)
                                ->first();
                    if (! $user) {
                        $user = User::where('current_team_id', $team->id)->first();
                        if (! $user) {
                            echo $cc_bulk->cms_bulkemail_tracker_sent_by_login.' not found, campaign ID '.$cc_bulk->cms_bulkemail_tracker_campaignID."\n";
                            continue;
                        }
                    }
                    $bulk->user_id = $user->id;
                    if ($cc_bulk->cms_bulkemail_tracker_desc) {
                        $bulk->name = $cc_bulk->cms_bulkemail_tracker_desc;
                    } else {
                        $bulk->name = $cc_bulk->cms_bulkemail_tracker_email_subject;
                    }
                }
                $bulk->subject = $cc_bulk->cms_bulkemail_tracker_email_subject;
                $bulk->content = $cc_bulk->cms_bulkemail_tracker_email_text;

                $bulk->sent_from = $cc_bulk->cms_bulkemail_tracker_sent_as_name;
                $bulk->sent_from_email = $cc_bulk->cms_bulkemail_tracker_sent_as_email;

                if ($cc_bulk->cms_bulkemail_tracker_sent > 0) {
                    $bulk->completed_at = $cc_bulk->cms_bulkemail_tracker_date;
                    $bulk->send_date = $cc_bulk->cms_bulkemail_tracker_date;
                    $bulk->queued = true;
                }

                $bulk->old_cc_id = $cc_bulk->cms_bulkemail_tracker_id;
                $bulk->old_tracker_code = $cc_bulk->cms_bulkemail_tracker_code;
                $bulk->created_at = $cc_bulk->cms_bulkemail_tracker_date;

                $bulk->save();

                echo date('Y-m-d h:i:s').' Adding '.$cc_bulk->ccBulkEmailVoters()->count()." recipients\n";

                $cc_bulk->ccBulkEmailVoters()->chunk(100, function ($cc_bulk_voters) use ($team, $bulk) {
                    foreach ($cc_bulk_voters as $cc_bev) {
                        $person = Person::where('team_id', $team->id)
                                        ->where('old_cc_id', $cc_bev->cms_voter_bulkemail_voterID)
                                        ->first();
                        $ccvoter = CCVoter::find($cc_bev->cms_voter_bulkemail_voterID);
                        if (! $person) {
                            //dd($cc_assignment);

                            if (! $ccvoter) {
                                $ccvoter = CCVoterArchive::find($cc_bev->cms_voter_bulkemail_voterID);
                            }
                            if (! $ccvoter) {
                                echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_bev->cms_voter_bulkemail_voterID."\n";

                                continue;
                            }
                            if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                                $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                            } else {
                                $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                            }
                        }
                        if (! $ccvoter && ! $person) {
                            echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_bev->cms_voter_bulkemail_voterID." or person\n";
                            continue;
                        }
                        if (! $person) {
                            // Double check remote voters table

                            $person = $ccvoter->createAndReturnPerson();
                        }
                        if (! $person) {
                            $archived = CCVoterArchive::where('voter_code', $ccvoter->voter_code)->first();
                            if ($archived) {
                                $person = $archived->createAndReturnPerson();
                            }
                        }
                        if (! $person) {
                            echo date('Y-m-d h:i:s').' '.$team->name.": Couldn't find person ".$cc_note->voter_code."\n";
                            continue;
                        }

                        $person->team_id = $team->id;

                        if (! $person->updated_at || $person->updated_at > Carbon::yesterday()) {
                            if ($tempdate = dateIsClean($cc_bev->cms_voter_bulkemail_date)) {
                                $person->updated_at = $tempdate;
                            }
                        }

                        if (! $person->created_at || $person->created_at > Carbon::yesterday()) {
                            if ($tempdate = dateIsClean($cc_bev->cms_voter_bulkemail_date)) {
                                $person->created_at = $tempdate;
                            }
                        }

                        $person->save();

                        //dd($cc_bev, $person);

                        $bulk_queue = new BulkEmailQueue;

                        $bulk_queue->bulk_email_id = $bulk->id;
                        $bulk_queue->team_id = $team->id;
                        if ($person) {
                            if ($person->email) {
                                if (is_array($person->email)) {
                                    $bulk_queue->email = '';
                                } else {
                                    $bulk_queue->email = $person->email;
                                }
                            } else {
                                $bulk_queue->email = '';
                            }
                            $bulk_queue->person_id = $person->id;
                            $bulk_queue->voter_id = $person->voter_id;
                        } else {
                            $bulk_queue->email = '';
                        }
                        $bulk_queue->old_voter_id = $cc_bev->cms_voter_bulkemail_voterID;
                        $bulk_queue->processing = false;
                        $bulk_queue->processing_start = $cc_bev->cms_voter_bulkemail_date;
                        $bulk_queue->attempts = 1;
                        $bulk_queue->sent = true;
                        $bulk_queue->created_at = $cc_bev->cms_voter_bulkemail_date;
                        $bulk_queue->updated_at = $cc_bev->cms_voter_bulkemail_date;

                        try {
                            $bulk_queue->save();
                        } catch (\Exception $e) {
                            echo date('Y-m-d h:i:s').' ERROR BULK QUEUE: '.$e->getMessage()."\n";
                            print_r($bulk_queue);
                        }
                    }
                });
            }
        }
    }
}

/*
        "cms_bulkemail_tracker_id" => 2283
        "cms_bulkemail_tracker_campaignID" => 1
        "cms_bulkemail_tracker_code" => "NEWTRACKER"
        "cms_bulkemail_tracker_desc" => "New tracker for Test"
        "cms_bulkemail_tracker_sent_by_login" => "pcolella"
        "cms_bulkemail_tracker_sent_as_name" => "Paul Colella"
        "cms_bulkemail_tracker_sent_as_email" => "paul@paulcolella.com"
        "cms_bulkemail_tracker_exclude_prior" => "y"
        "cms_bulkemail_tracker_date" => "2013-03-06 01:23:41"
        "cms_bulkemail_tracker_excluded" => 1
        "cms_bulkemail_tracker_noemail" => 0
        "cms_bulkemail_tracker_sent" => 0
        "cms_bulkemail_tracker_failed" => 0
        "cms_bulkemail_tracker_read" => 0
        "cms_bulkemail_tracker_optouts" => 0
        "cms_bulkemail_tracker_email_subject" => "Test Selecting Existing Tracker Code"
        "cms_bulkemail_tracker_email_text" => """
          <p>\n
          \tTest</p>
          """
        "cms_bulkemail_tracker_summary" => "Emailed Issue Category: test1\r\n"
        "archive" => "n"


        $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();

            $table->string('name')->nullable();
            // $table->text('recipients_form')->nullable();
            $table->unsignedInteger('search_id')->nullable()->index();

            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->text('content_plain')->nullable();
            $table->boolean('refresh_plain')->default(true);

            $table->string('sent_from')->nullable();
            $table->string('sent_from_email')->nullable();
            // $table->unsignedInteger('bulk_email_template_id')->nullable()->index();
            $table->date('send_date')->nullable();


            $table->boolean('exclude_prior')->default(false);
            $table->text('excluded')->nullable();

            // Tracking the Email
            $table->boolean('queued')->default(0);
            $table->text('no_email_address')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->unsignedInteger('expected_count')->nullable();
            $table->text('emails')->nullable();
            $table->unsignedInteger('sent_count')->nullable();
            $table->dateTime('completed_at')->nullable();

            // Sending results
            $table->text('failed')->nullable();
            $table->text('read')->nullable();

            $table->string('old_tracker_code')->nullable();
        */
