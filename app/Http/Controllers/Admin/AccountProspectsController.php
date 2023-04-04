<?php

namespace App\Http\Controllers\Admin;

use App\AccountProspect;
use App\Candidate;
use App\CandidateContact;
use App\CandidateMarketing;
use App\Http\Controllers\Controller;
use Artisan;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountProspectsController extends Controller
{
    public function linkCandidate($candidate_id, $voter_id)
    {
        $candidate = Candidate::find($candidate_id);
        $candidate->voter_id = $voter_id;
        $candidate->save();
        session()->flash('linked', $candidate->fullname.' linked to '.$voter_id);

        return redirect()->back();
    }

    // public function manualAddCandidate(Request $request)
    // {
    //     // dd($request->input());
    //     $candidate = new Candidate;
    //     $candidate->first_name          = request('first_name');
    //     $candidate->last_name           = request('last_name');
    //     $candidate->district_id         = request('district_id');
    //     $candidate->municipality_id     = request('municipality_id');
    //     $candidate->office              = request('office');
    //     $candidate->organized_at        = now();
    //     $candidate->is_candidate        = true;
    //     $candidate->save();

    //     return redirect('/admin/marketing/'.$candidate->id.'/edit');
    // }

    public function marketingIndex()
    {
        //Get Schedule
        $sequence_camel = 'NewCandidate';
        $sequence_snake = 'new_candidate';

        //Get Schedule
        $path = app_path().'/Mail/Marketing/'.$sequence_camel.'/schedule.json';
        $schedule = utf8_encode(file_get_contents($path));
        $schedule = json_decode($schedule, true);

        $candidates = Candidate::orderBy('organized_at', 'desc')
                                ->orderBy('created_at', 'desc');

        if (isset($_GET['no_voter_ids'])) {
            $candidates = $candidates->whereNull('voter_id');
        }

        $candidates = $candidates->get();

        // Ensure they have a marketing extensions

        $existing_marketing_exts = CandidateMarketing::all()->pluck('candidate_id');
        foreach (Candidate::WhereNotIn('id', $existing_marketing_exts)->get() as $candidate) {
            $marketing = new CandidateMarketing($candidate->id);
            $marketing->save();
        }

        // $candidate = $candidates->first();
        // dd($candidate->marketing);
        // Get the next marketing email for each candidate:

        $candidates->each(function ($item) use ($schedule, $sequence_camel) {
            if (
                    (
                        ($item->candidate_email && $item->marketing->ok_email_candidate) ||
                        ($item->chair_email && $item->marketing->ok_email_chair) ||
                        ($item->treasurer_email && $item->marketing->ok_email_treasurer)
                    )
                    && ! $item->do_not_contact
                ) {
                // Has required emails
            } else {
                $item['next_email'] = null;

                return;
            }

            $last_step = $item->contacts()->where('sequence', $sequence_camel)
                                               ->orderBy('step', 'desc')
                                               ->first();

            $last_step_num = ($last_step) ? $last_step->step : 0;

            if ($last_step_num == 0) {
                $item['next_email'] = Carbon::today()->toDateString();
            } elseif (isset($schedule[$last_step_num + 1])) {
                $days_wait = $schedule[$last_step_num]['wait'] * 1;
                $item['next_email'] = Carbon::parse($last_step->created_at)->addDays($days_wait)->toDateString();
            } else {

                // No more steps
            }
        });

        return view('admin.marketing.index', compact('candidates'));
    }

    public function marketingStats($year = null, $month = null)
    {
        return view('admin.marketing.stats');
    }

    public function runCommand($command)
    {
        try {
            Artisan::call('marketing:'.$command);
        } catch (\Exception $e) {
            //
        }

        return redirect()->back();
    }

    public function editCandidate($id)
    {
        $candidate = Candidate::find($id);
        if (! CandidateMarketing::where('candidate_id', $id)->exists()) {
            $marketing = new CandidateMarketing($candidate_id = $id);
            $marketing->save();
        }

        return view('admin.marketing.edit', compact('candidate'));
    }

    public function updateCandidate(Request $request, $id, $close = null)
    {
        $candidate = Candidate::find($id);
        if (! CandidateMarketing::where('candidate_id', $id)->exists()) {
            $marketing = new CandidateMarketing($candidate_id = $id);
            $marketing->save();
        }

        // Put in better validation
        foreach (['candidate_email', 'chair_email', 'treasurer_email'] as $email) {
            foreach (['.gov', '.ma.us'] as $public_address_element) {
                if (strpos(request($email), $public_address_element)) {
                    dd('Error - Gov address');
                }
            }
        }

        // $candidate->first_name          = request('first_name');
        // $candidate->middle_name         = request('middle_name');
        // $candidate->last_name           = request('last_name');
        $candidate->voter_id = request('voter_id');
        $candidate->account_id = request('account_id');
        // $candidate->candidate_email     = request('candidate_email');
        // $candidate->chair_email         = request('chair_email');
        // $candidate->treasurer_email     = request('treasurer_email');
        // $candidate->candidate_phone     = request('candidate_phone');
        // $candidate->chair_phone         = request('chair_phone');
        // $candidate->treasurer_phone     = request('treasurer_phone');
        // $candidate->chair_name          = request('chair_name');
        // $candidate->treasurer_name      = request('treasurer_name');
        // $candidate->district_id         = request('district_id');
        // $candidate->municipality_id     = request('municipality_id');
        // $candidate->office              = request('office');
        // $candidate->party               = request('party');

        $candidate->save();

        ////////////////////////////////////////////////// MARKETING EXTENSION

        $marketing_info = $candidate->marketing;

        $marketing_info->ok_email_candidate = (request('ok_email_candidate')) ? true : false;
        $marketing_info->ok_email_chair = (request('ok_email_chair')) ? true : false;
        $marketing_info->ok_email_treasurer = (request('ok_email_treasurer')) ? true : false;
        $marketing_info->do_not_contact = (request('do_not_contact')) ? true : false;
        $marketing_info->loyalty_conflict_id = request('loyalty_conflict_id');

        $marketing_info->save();

        ////////////////////////////////////////////////// CONTACTS

        if (request('add_date') || request('add_type') || request('add_notes')) {
            $contact = new CandidateContact;
            $contact->candidate_id = $candidate->id;
            $contact->user_id = Auth::user()->id;
            $contact->created_at = Carbon::parse(request('add_date').' '
                                                        .Carbon::now()->format('H:i:s')
                                                        )->toDateTimeString();
            // $contact->type              = request('add_type');
            $contact->notes = request('add_notes');
            $contact->step = request('add_step');
            $contact->sequence = request('add_sequence');
            $contact->save();
        }

        $edit_contact_id = null;
        foreach ($request->input() as $field => $value) {
            if (substr($field, 0, 20) == 'submit_edit_contact_') {
                $edit_contact_id = substr($field, 20);
                break;
            }
        }

        if ($edit_contact_id) {
            $contact = CandidateContact::find($edit_contact_id);
            // $contact->user_id           = Auth::user()->id;
            $contact->created_at = Carbon::parse(
                                            request('edit_contact_'.$edit_contact_id.'_date')
                                            )->toDateTimeString();
            $contact->notes = request('edit_contact_'.$edit_contact_id.'_notes');
            $contact->step = request('edit_contact_'.$edit_contact_id.'_step');
            $contact->sequence = request('edit_contact_'.$edit_contact_id.'_sequence');
            $contact->save();
        }
        // Other Reasons not to contact
        if ($candidate->account_id) {
            $marketing_info->do_not_contact = true;
        }
        if ($candidate->marketing->loyalty_conflict_id) {
            $marketing_info->do_not_contact = true;
        }
        $marketing_info->save();

        if ($close) {
            return redirect('/admin/marketing');
        }

        return redirect('/admin/marketing/'.$id.'/edit');
    }
}
