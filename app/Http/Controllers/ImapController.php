<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Auth;


class ImapController extends Controller
{

    // https://help.hover.com/hc/en-us/articles/217281777-Mail-server-settings-for-email-clients
    // mail.hover.com

    // IMAP server settings

    // Username    The full email address (Ex: me@mydomain.com)
    // Password    Password for the email address
    // Incoming mail server hostname   mail.hover.com
    // Outgoing (SMTP) mail server hostname    mail.hover.com

    // SSL Enabled SSL Disabled
    // IMAP incoming port  993 143
    // POP incoming port   995 110
    // SMTP outgoing port  465 25,587 or 8025

    public function index($app_type)
    {
        if (!Auth::user()->permissions->developer) {
            dd('Error');
        }

        $messages = [];

        $username = env('LAZ_EMAIL_USERNAME');
        $password = env('LAZ_EMAIL_PASSWORD');
        $server   = 'mail.hover.com:143';

        $connection = imap_open("{".$server."}INBOX", $username, $password);

        //$count = imap_num_msg($connection);
        // $some   = imap_search($imap, 'SUBJECT "HOWTO be Awesome" SINCE "8 August 2008"', SE_UID);
        // $some   = imap_search($connection, 'SINCE "8 August 2008"', SE_UID);

        $since = Carbon::today()->subMonths(1)->format('n M Y');
        $some   = imap_search($connection, 'SINCE "'.$since.'"', SE_UID);

        foreach($some as $m) {

            $headers = imap_headerinfo($connection, $m);
  
            $messages[] = collect([
                            'date'     => Carbon::parse($headers->date),
                            'headers'  => imap_headerinfo($connection, $m),
                            'body'     => imap_body($connection, $m)
                        ]);
           
        }

        $messages = collect($messages)->reverse();

        dd($messages->first());

        return view('shared-features.imap.index', ['messages' => $messages]);
    }
}
