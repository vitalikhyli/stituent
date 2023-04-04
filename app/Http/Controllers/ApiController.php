<?php

namespace App\Http\Controllers;

use App\CandidateContact;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function CampaignClick($key, $string)
    {
        if ($key != config('app.community_app_api')) {
            return null;
        }

        $request = parse_str($string);
        $response = [];

        ////////////////////////////////////////////////////////////////////////////

        if (isset($request['unsubscribe'])) {
            $values = $this->decodeString($request['unsubscribe']);
            $response['unsubscribe_success'] = $this->recordUnsubscribe($values['email'], $values['contact_id']);
        }

        if (isset(request['source'])) {
            $values = $this->decodeString($request['source']);
            $this->recordClick($values['email'], $values['contact_id']);
        }

        ////////////////////////////////////////////////////////////////////////////

        return json_encode(collect($response));
    }

    public function recordClick($email, $contact_id)
    {
        $contact = CandidateContact::find($contact_id);
        if (! $contact || ! $email || ! $contact_id) {
            return false;
        }
        $clicks = $contact->clicks;
        $clicked_at = now();
        $clicks[] = [$clicked_at, $email, null];
        $contact->clicks = $clicks;
        $contact->last_clicked_at = $clicked_at;
        $contact->save();
    }

    public function recordUnsubscribe($email, $contact_id)
    {
        $contact = CandidateContact::find($contact_id);
        if (! $contact || ! $email || ! $contact_id) {
            return false;
        }

        $clicks = $contact->clicks;
        $clicks[] = [now(), $email, 'unsubscribe'];
        $contact->clicks = $clicks;
        $contact->save();

        $candidate = $contact->candidate;

        $emails_to_check = [
                            ['address' => 'candidate_email', 	'rules' => 'ok_email_candidate'],
                            ['address' => 'chair_email', 		'rules' => 'ok_email_email'],
                            ['address' => 'treasurer_email', 	'rules' => 'ok_email_treasurer'],
                            ];

        foreach ($emails_to_check as $which_email) {
            if ($candidate->$which_email['address'] == $email) {
                $candidate->$which_email['rules'] = false;
            }
        }

        if (! $candidate->ok_email_candidate &&
            ! $candidate->ok_email_chair &&
            ! $candidate->ok_email_treasurer) {
            $candidate->do_not_email = true;
        }

        $candidate->save();

        return true;
    }

    public function decodeString($string)
    {
        $email = null;
        $contact_id = null;

        $marker_pos = strpos($string, '_') + 1;
        $encode_prefix = substr($string, 0, $marker_pos);
        $string = substr($string, $marker_pos);

        ////////////////////////////////////////////////////////////////
        //
        // First generation decode method
        //

        if ($encode_prefix == '001_') {
            for ($i = 0; $i < 3; $i++) {
                $string = base64_decode($string);
            }
            $string = explode(';', $string);
            $email = (isset($string[0])) ? $string[0] : null;
            $contact_id = (isset($string[1])) ? $string[1] : null;
        }

        ////////////////////////////////////////////////////////////////
        //
        // Second generation decode method -- for future use
        //

        if ($encode_prefix == '002_') {
            //
        }

        ////////////////////////////////////////////////////////////////

        return ['email' => $email, 'contact_id' => $contact_id];
    }
}
