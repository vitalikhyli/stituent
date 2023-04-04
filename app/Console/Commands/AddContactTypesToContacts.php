<?php

namespace App\Console\Commands;

use App\Contact;
use App\ContactType;
use App\Models\CC\CCContactType;
use App\Models\CC\CCVoterContact;
use Illuminate\Console\Command;

class AddContactTypesToContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:contact_types';

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
        $arr = [];
        $cc_cts = CCContactType::all();
        foreach ($cc_cts as $cc_ct) {
            $arr[$cc_ct->contact_type] = $cc_ct->contact_desc;
            $ct = ContactType::where('code', $cc_ct->contact_type)->first();
            if (! $ct) {
                $ct = new ContactType;
                $ct->code = $cc_ct->contact_type;
                $ct->name = $cc_ct->contact_desc;
                $ct->save();
            }
        }

        $coun = 0;
        Contact::chunk(1000, function ($contacts) use (&$count, $arr) {
            echo $count."\n";
            foreach ($contacts as $contact) {
                $count++;
                if ($contact->source == 'case_contact') {  // voter_note, call_log
                    $cc_contact = CCVoterContact::where('contactID', $contact->old_cc_id)->first();

                    if (! $cc_contact) {
                        echo 'MISSING id '.$contact->old_cc_id."\n";
                        continue;
                    }
                    if (! isset($arr[$cc_contact->contact_type])) {
                        echo 'MISSING code '.$cc_contact->contact_type."\n";
                        continue;
                    }
                    $contact->type = $arr[$cc_contact->contact_type];
                    $contact->save();
                }
            }
        });
    }
}
