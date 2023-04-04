<?php

// namespace App\Console\Commands\Marketing;

// use Illuminate\Console\Command;
// use Illuminate\Support\Facades\Mail;
// use Carbon\Carbon;

// use App\Candidate;

// class SendEmails extends Command
// {
    /*
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'marketing:sendemails {confirm?}';

    /*
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Send sequence of emails to candidates';

    /*
     * Create a new command instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /*
     * Execute the console command.
     *
     * @return mixed
     */

    // public function waitForSeconds($seconds)
    // {
    //     for ($i=1; $i <= $seconds; $i++) {
    //         echo "Waiting ".$i."...\r";
    //         sleep(1);
    //     }
    // }

    // public function indicateConfirmMode() {
    //     echo str_repeat('=', 60)."\r\n";
    //     echo str_repeat(' ', 24)."In Confirm Mode"."\r\n";
    //     echo str_repeat('=', 60)."\r\n";
    // }

    // public function handle()
    // {

    //     dd('No automatic sending yet.')

        // echo "\r\n";

        // if ($this->argument('confirm')) $this->indicateConfirmMode();

        // $today = Carbon::today();
        // // $today = Carbon::today()->addDays(22);  // For Future Testing

        // //Get Schedule
        // $sequence_camel = 'NewCandidate';
        // // $sequence_snake = 'new_candidate';

        // //Get Schedule
        // $path = app_path().'/Mail/Marketing/'.$sequence_camel.'/schedule.json';
        // $schedule = utf8_encode(file_get_contents($path));
        // $schedule = json_decode($schedule, true);

        // if (!$schedule) {
        //     dd('Error reading JSON schedule.');
        // }

        // Schedule.json looks like this (as an array):
        //
        // array:2 [
        //   1 => array:2 [
        //     "mailable" => "IntroEmail"
        //     "wait" => "21"
        //   ]
        //   2 => array:2 [
        //     "mailable" => "FollowUp"
        //     "wait" => "30"
        //   ]
        // ]

        // Get Candidates
        // $candidates = Candidate::where('do_not_contact', false)     // ok to contact
        //                        ->whereNull('account_id')            // not a current client
        //                        ->whereNull('loyalty_conflict_id')   // no conflict
        //                        ->where(function($q) {
        //                             $q->orWhere(function($r) {
        //                                 $r->where('candidate_email', '!=', null);
        //                                 $r->where('ok_email_candidate', true);
        //                             });
        //                             $q->orWhere(function($r) {
        //                                 $r->where('chair_email', '!=', null);
        //                                 $r->where('ok_email_chair', true);
        //                             });
        //                             $q->orWhere(function($r) {
        //                                 $r->where('treasurer_email', '!=', null);
        //                                 $r->where('ok_email_treasurer', true);
        //                             });
        //                        })->get();

        // foreach ($candidates as $key => $candidate) {

        //     // Create list of recipients and initial Mail object
        //     $recipients = [];
        //     if ($candidate->candidate_email && $candidate->ok_email_candidate) {
        //         $recipients[] = $candidate->candidate_email;
        //     }
        //     if ($candidate->chair_email && $candidate->ok_email_chair) {
        //         $recipients[] = $candidate->chair_email;
        //     }
        //     if ($candidate->treasurer_email && $candidate->ok_email_treasurer) {
        //         $recipients[] = $candidate->treasurer_email;
        //     }

        //     if (count($recipients) <= 0) continue;

        //     $mail = Mail::to($recipients[0]);
        //     unset($recipients[0]);
        //     if (count($recipients) > 0) $mail = $mail->cc($recipients);

        //     //////////////////////////////////////////////////////////////////////////

        //     $mailable = null;
        //     $mailable_full = null;

        //     $last_step = $candidate->contacts()->where('sequence', $sequence_camel)
        //                                        ->orderBy('step', 'desc')
        //                                        ->first();

        //     $last_step_num = ($last_step) ? $last_step->step : 0;

        //     // Check next step
        //     if ($last_step_num == 0) {

        //         // Go to First Step
        //         $mailable = $schedule[1]['mailable'];

        //     } else if (isset($schedule[$last_step_num + 1])) {

        //         $days_wait = $schedule[$last_step_num]['wait'] * 1;

        //         if (Carbon::parse($last_step->created_at)->diffInDays($today, false) >= $days_wait) {

        //             $mailable = $schedule[$last_step_num + 1]['mailable'];

        //         } else {

        //             // Too soon to send next email

        //         }

        //     } else {

        //         // No more steps

        //     }

        //     // Send Mailable

        //     if ($mailable) {
        //         $mailable_full = 'App\Mail\Marketing\\'.$sequence_camel.'\\'.$mailable;
        //     }

        //     if ($mailable_full) {

        //         if ($this->argument('confirm')) {
        //             if (!$this->confirm('About to send "'.$mailable.'" to '.$candidate->fullName
        //                                 .' ...OK?')) {
        //                 echo "Skipping.\r\n";
        //                 continue;
        //             }
        //         }

        //         try {

        //             if (!class_exists($mailable_full)) {
        //                 throw new \Exception('Mailable "'.$mailable.'" does not exist.');
        //             }

        //             $mail->send(new $mailable_full($candidate));
        //             $candidate->addSequenceStep($sequence_camel, $last_step_num + 1, $mailable);

        //             echo 'Emailed: "'.$mailable.'" to '.$candidate->fullName."\n";
        //             $this->waitForSeconds(3);

        //         } catch (\Exception $e) {

        //             echo 'Error: '.$candidate->fullName.' -- '.$e->getMessage()."\r\n";

        //         }

        //     }

    //     }
    // }
// }
