<?php

namespace App\Console\Commands;

use App\BulkEmail;
use App\BulkEmailQueue;
use App\BulkSentEmail;
use App\Person;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;
use Validator;

use Illuminate\Support\Facades\Notification;
use App\Notifications\BulkEmailProcessingError;


class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:mail {--errors} {--tests_only} {--bulkid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Oh I think you know very well what this command does';

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

        $this->checkForLongProcessingEmails();

        // SEND MAIL

        $number_at_a_time = 100;

        $emails_in_queue = BulkEmailQueue::where('processing', false)
                                        ->where('sent', false)
                                        ->where('created_at', '>', Carbon::today()->subDays(2))
                                        ->take($number_at_a_time);

        if ($this->option('bulkid')) {
            $emails_in_queue = BulkEmailQueue::where('bulk_email_id', $this->option('bulkid'))
                                             ->where('sent', false);
           // dd($emails_in_queue);
        }

        if ($this->option('tests_only')) {
            $emails_in_queue = $emails_in_queue->where('test', true);
        }

        $emails_in_queue = $emails_in_queue->get();

        foreach ($emails_in_queue as $queue) {
            if ($queue->processing && !$this->option('bulkid')) {
                continue;
            }

            $queue->markAsProcessing();

            $seconds_to_pause = 1;
            $microseconds_to_pause = $seconds_to_pause * 1000000;
            usleep($microseconds_to_pause);

            if ($queue->team && $queue->bulkEmail) {
                echo date('Y-m-d h:i:s').' '.$queue->team->name.' ('.$queue->bulkEmail->name.') sending to '.$queue->email;
            } else {
                echo date('Y-m-d h:i:s').' MISSING TEAM OR BULK EMAIL, queue id '.$queue->id."\n";
            }
            $result = $this->sendTheEmail($queue);

            if (stripos($result, 'error') !== false) {

                //$queue->noLongerProcessing();
                echo ' ERROR, queue id '.$queue->id."\n";
                echo 'Error Log: '.$result."\n";
            } else {
                echo " SENT!\n";
                $queue->markAsSent();

                $result .= ' Marked as Sent.';
            }

            if ($this->option('errors')) {
                dd($result);
            }
        }

        // HOUSEKEEPING -- ALL EMAILS IN A BULK EMAIL QUEUE HAVE BEEN SENT

        $uncompleted = BulkEmail::whereNull('completed_at')->get();

        foreach ($uncompleted as $email) {
            if ($email->queuedCount() > 0) {
                if ($email->queuedAndProcessing()->count() >= $email->queuedCount()) {
                    $email->completed_at = now();
                    $email->save();
                }
            }
        }
    }

    public function mergeFields($queue, $text)
    {
        $available = [
                        ['tag' => 'full_name',  'column' => 'full_name'],
                        ['tag' => 'first_name', 'column' => 'first_name'],
                        ['tag' => 'last_name',  'column' => 'last_name'],
                        ['tag' => 'title',      'column' => 'title'],
                    ];

        if ($queue->test) {
            $person = new Person;
            $person->full_name = 'Frederick Johnson';
            $person->name_title = 'President';
            $person->title = 'President';
            $person->first_name = 'Frederick';
            $person->last_name = 'Johnson';
        } else {
            $person = Person::find($queue->person_id);
        }
        //dd($person);

        foreach ($available as $field) {
            $tag_name = $field['tag'];
            $column_name = $field['column'];
            $text = str_replace('{%'.$tag_name.'%}', $person->$column_name, $text);
            $text = str_replace('{% '.$tag_name.' %}', $person->$column_name, $text);
        }

        return $text;
    }

    public function sendTheEmail($queue)
    {
        // Log::info("Emailing ".now());

        $content = $this->mergeFields($queue, $queue->bulkemail->content);
        $content_plain = $this->mergeFields($queue, $queue->bulkemail->content_plain);

        $info = [
            'recipient_email'   => trim($queue->email),
            'recipient_name'    => ($queue->test) ? 'Fakey Testname' : $queue->person->full_name,
            'from_email'        => $queue->bulkemail->sent_from_email,
            'from_name'         => $queue->bulkemail->sent_from,
            'subject'           => $queue->bulkemail->subject,
            'system_email'      => 'laz@communityfluency.com',
            'system_name'       => 'Community Fluency',
            'html'              => $content,
            'plain'             => $content_plain,
        ];

        $sent_already = BulkSentEmail::where('email', $info['recipient_email'])
                                     ->where('queue_id', $queue->id)
                                     ->whereNotNull('finished_at')
                                     ->first();
        if ($sent_already) {
            return '** Error: Sent Already';
        }

        $bulk_sent_email = new BulkSentEmail;
        $bulk_sent_email->team_id = $queue->team_id;
        $bulk_sent_email->queue_id = $queue->id;
        $bulk_sent_email->bulk_email_id = $queue->bulk_email_id;
        $bulk_sent_email->name = $info['recipient_name'];
        $bulk_sent_email->email = $info['recipient_email'];
        $bulk_sent_email->subject = $info['subject'];
        $bulk_sent_email->save();

        $data = [
            'date'              => Carbon::now()->format('m/d/Y'),
            'link'              => null,
            'to_name'           => ($queue->test) ? 'Test Person' : $queue->person->full_name,
            'from_name'         => $queue->bulkemail->sent_from,
            // 'account'           => 'Slorhe'
        ];

        $validator = Validator::make(array_merge($info, $data), [
            'recipient_email'   => ['required', 'email'],
            'from_email'        => ['required', 'email'],
            'system_email'      => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            $bulk_sent_email->error = 'Validation Error'.$validator->errors();
            $bulk_sent_email->save();

            return 'Validation Error'.$validator->errors(); //.' -- Validation failed'.print_r($info);
        }

        // https://stackoverflow.com/questions/1884550/converting-html-to-plain-text-in-php-for-e-mail
        //https://stackoverflow.com/questions/26139931/laravel-mail-pass-string-instead-of-view

        

        try {
            Mail::send([], $data, function ($message) use ($info) {
                $message->setBody($info['html'], 'text/html');
                $message->addPart($info['plain'], 'text/plain');
                $message->from($info['from_email'], $info['from_name']);
                $message->replyTo($info['from_email'], $info['from_name']);
                $message->to($info['recipient_email'], $info['recipient_name']);
                $message->subject($info['subject']);
            });

            $bulk_sent_email->finished_at = Carbon::now();
            $bulk_sent_email->save();
        } catch (\Exception $e) {
            $bulk_sent_email->error = 'Error'.$e->getMessage();
            $bulk_sent_email->save();
            echo $e->getMessage();

            return 'Mail Failure Error.';
        }
        if (in_array($info['recipient_email'], Mail::failures())) {
            $bulk_sent_email->error = 'Mail Failures Error';
            $bulk_sent_email->save();
            echo print_r(Mail::failures());

            return 'Mail Failure Error.';
        }

        return 'Done.';
    }

    public function checkForLongProcessingEmails()
    {
        // CHECK IF ANY HAVE BEEN PROCESSING FOR OVER AN HOUR

        $problems = BulkEmailQueue::where('processing', true)
                                  ->where('sent', false)
                                  ->whereDate('processing_start', '<', Carbon::now()->subMinutes(60))
                                  ->whereDate('processing_start', '>=', Carbon::now()->subDays(2))
                                  ->get();

        if ($problems->first()) {

            $this->info('Long processing time for Bulk Email Queue detected: '.Carbon::parse($problems->min('processing_start'))->diffForHumans());

            $staff = User::join('permissions', 'users.id', '=', 'permissions.user_id')
                         ->where('permissions.developer', true)
                         ->get();

            $staff = User::whereIn('id', $staff->pluck('user_id'))->get(); //Because multiple teams?

            if (config('app.env') == 'local') $staff = $staff->take(1); // Spare Mailtrap.io

            foreach($staff as $user) {

                $last = $user->getMemory('lastBulkEmailProcessingNotification');

                //Only send one notification per hour

                if (!$last || Carbon::parse($last)->diffInMinutes() >= 60) {

                    $user->addMemory('lastBulkEmailProcessingNotification', Carbon::now()->toDateTimeString());

                    Notification::route('mail', $user->email)
                                ->notify(new BulkEmailProcessingError($problems));

                    $this->info('Sending notification to '.$user->name);

                } else {

                    $this->info('Skipping notification for '.$user->name.' because notified '.Carbon::parse($last)->diffForHumans());

                    continue; 

                }

            }

        }
    }
}
