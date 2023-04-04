<?php

namespace App\Http\Livewire;

use App\Participant;
use App\VolunteerEmail;
use App\User;

use Auth;
use Carbon\Carbon;
use Livewire\Component;
use Mail;

use App\Traits\ParticipantQueryTrait;


class VolunteersLive extends Component
{
    use ParticipantQueryTrait;

    public $original_participant_ids = null;
    public $volunteer_options = null;
    public $filter = null;

    public $show_emails = false;

    public $emails = [];
    public $phones = [];

    public $carbon = 'bcc';
    public $subject = '';
    public $body = '';
    public $recipients = [];
    public $recipients_final = '';

    public $from_user_id;

    public $sending = false;

    public $lookup;
    public $just_added = [];

    public $show_phones;

    public $search;


    public function mount($participants, $volunteer_options)
    {
        $this->setUpParticipants($participants);
        $this->volunteer_options = $volunteer_options;
        $this->from_user_id = Auth::user()->id;
    }

    public function setUpParticipants($participants) {
        foreach ($participants as $p) {
            $this->emails[$p->id]['email'] = $p->primary_email;
            $this->emails[$p->id]['edit'] = false;
            $this->phones[$p->id]['phone'] = $p->primary_phone;
            $this->phones[$p->id]['edit'] = false;
        }
        $this->original_participant_ids = $participants->pluck('id')->toArray();

    }

    public function showEmails()
    {
        $this->show_emails = true;
        if (! $this->filter) {
            $this->setFilter(null);
        }
    }


    public function addRecipient($email)
    {
        $this->recipients_final = $this->recipients_final.$email.' ';
    }

    public function clearRecipients()
    {
        $this->recipients_final = '';
    }

    public function updatedEmails()
    {
        foreach (Participant::whereIn('id', $this->original_participant_ids)->get() as $p) {

            if (isset($this->emails[$p->id]['email'])) {
            
                if ($this->emails[$p->id]['email'] != $p->primary_email) {
                    $p->primary_email = $this->emails[$p->id]['email'];
                    $p->save();
                }

            }
        }
    }

    public function updatedPhones()
    {
        foreach (Participant::whereIn('id', $this->original_participant_ids)->get() as $p) {

            if (isset($this->phones[$p->id]['phone'])) {
            
                if ($this->phones[$p->id]['phone'] != $p->primary_phone) {
                    $p->primary_phone = $this->phones[$p->id]['phone'];
                    $p->save();
                }

            }
        }
    }

    public function setFilter($filter)
    {
        $this->recipients_final = '';
        $this->filter = $filter;
        $participants_all = Participant::whereIn('id', $this->original_participant_ids)
                                       ->with('campaignParticipant')
                                       ->get();

        if ($this->filter) {
            $participants = collect([]);
            foreach ($participants_all as $p) {
                $volunteer_option = $this->filter;
                if ($p->campaignParticipant) {
                    if ($p->campaignParticipant->$volunteer_option) {
                        $participants[] = $p;
                    }
                }
            }
        } else {
            $participants = $participants_all;
        }
        foreach ($participants as $p) {
            if ($this->emails[$p->id]['email'] != $p->primary_email) {
                $p->primary_email = $this->emails[$p->id]['email'];
                $p->save();
            }
        }
        $recipients = $participants->pluck('primary_email');
        $this->recipients = [];
        foreach ($recipients as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->recipients[] = $email;
                $this->recipients_final = $this->recipients_final.$email.' ';
            }
        }
    }

    public function clearFilter()
    {
        $this->setFilter(null);
    }

    public function toggleVolunteer($volunteer_option, $participant_id)
    {
        //dd($volunteer_option, $participant_id);
        $participant = Participant::find($participant_id);
        $cp = $participant->campaignParticipant;
        if (!$cp) return;
        $cp->$volunteer_option = ! $cp->$volunteer_option;
        $cp->save();
        $participant->addToAudit("Toggled $volunteer_option to ".$cp->$volunteer_option);
    }

    public function send()
    {
        if ($this->sending) {
            return;
        }
        $this->sending = true;

        config(['mail.username' => env('MAIL_CAMPAIGN_USERNAME')]);
        config(['mail.password' => env('MAIL_CAMPAIGN_PASSWORD')]);

        $recipients = [];
        $recipients_str = '';

        $emails_str = $this->recipients_final;
        $emails_str = str_replace([';', ','], ' ', $emails_str);
        $temparr = explode(' ', $emails_str);
        //dd($temparr);

        foreach ($temparr as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $recipients[] = $email;
                $recipients_str .= $email.' ';
            }
        }
        $this->recipients = $recipients;

        $volunteer_email = new VolunteerEmail;
        $volunteer_email->team_id = Auth::user()->team_id;
        $volunteer_email->user_id = Auth::user()->id;
        $volunteer_email->subject = $this->subject;
        $volunteer_email->body = nl2br($this->body);
        $volunteer_email->recipients = trim($recipients_str);
        $volunteer_email->carbon = 'bcc';
        $volunteer_email->save();

        $from_user = User::find($this->from_user_id);
        if (!Auth::user()->allteams->contains($from_user->team->id)) abort(403); //Security

        Mail::send([], [], function ($message) use ($volunteer_email, $from_user) {
            $message->setBody($volunteer_email->body, 'text/html');
            $message->addPart(strip_tags($volunteer_email->body), 'text/plain');
            $message->from('sender@campaignfluency.com', $from_user->name);
            $message->replyTo($from_user->email);
            $message->to($from_user->email, Auth::user()->team->name.' Volunteers');
            $message->bcc($this->recipients);
            $message->subject($volunteer_email->subject);
        });

        $volunteer_email->sent_at = Carbon::now();
        $volunteer_email->save();

        $this->subject = null;
        $this->body = null;
        $this->sending = false;
    }

    public function edit($id)
    {
        if (!Auth::user()->permissions->admin) return;
        $this->emails[$id]['edit'] = true;
    }

    public function editPhone($id)
    {
        if (!Auth::user()->permissions->admin) return;
        $this->phones[$id]['edit'] = true;
    }

    public function addVolunteer($id)
    {
        $participant = findParticipantOrImportVoter($id, Auth::user()->team->id);
        $participant->markAsVolunteer('general');

        $this->emails[$participant->id]['email'] = $participant->primary_email;
        $this->emails[$participant->id]['edit'] = false;
        $this->phones[$participant->id]['phone'] = $participant->primary_phone;
        $this->phones[$participant->id]['edit'] = false;
        $this->original_participant_ids[] = $participant->id;
        $this->just_added[$participant->id] = Carbon::now()->toDateTimeString();

        $this->lookup = null;
    }

    public function createParticipant()
    {
        $words = explode(' ', $this->lookup);
        $first = array_shift($words);
        $last  = implode(' ', $words);

        $model = new Participant;
        $model->team_id       = Auth::user()->team->id;
        $model->user_id       = Auth::user()->id;
        $model->first_name    = $first;
        $model->last_name     = $last;
        $model->full_name     = $this->lookup;
        $model->save();

        $model->markAsVolunteer('general');

        $this->emails[$model->id]['email'] = $model->primary_email;
        $this->emails[$model->id]['edit'] = false;
        $this->phones[$model->id]['phone'] = $model->primary_phone;
        $this->phones[$model->id]['edit'] = false;
        $this->original_participant_ids[] = $model->id;
        $this->just_added[$model->id] = Carbon::now()->toDateTimeString();

        $this->lookup = null;
    }

    public function render()
    {
        $participants_all = Participant::whereIn('id', $this->original_participant_ids)
                                       ->with('campaignParticipant')
                                       ->get()
                                       ->each(function ($item) {
                                            if (array_key_exists($item->id, $this->just_added)) {
                                                $item['new'] = $this->just_added[$item->id];
                                            } else {
                                                $item['new'] = null;
                                            }
                                       })
                                       ->sortByDesc('new');

        if ($this->search) {
            $participants_all = $participants_all->filter(function ($item) {
                if (stripos($item->full_name, $this->search) !== false) return true;
                if (stripos($item->primary_email, $this->search) !== false) return true;
            });
        }

        if ($this->filter) {
            $participants = collect([]);
            foreach ($participants_all as $p) {
                $volunteer_option = $this->filter;
                if ($p->campaignParticipant) {
                    if ($p->campaignParticipant->$volunteer_option) {
                        $participants[] = $p;
                    }
                }
            }
        } else {
            $participants = $participants_all;
        }
        $volunteer_emails = VolunteerEmail::where('team_id', Auth::user()->team_id)
                                          ->latest()
                                          ->get();

        $from = Auth::user()->team->usersAll()->get();

        $recipients = [];
        $recipients_str = '';

        if ($this->recipients_final) {
            $emails_str = $this->recipients_final;
            $emails_str = str_replace([';', ','], ' ', $emails_str);
            $temparr = explode(' ', $emails_str);
            //dd($temparr);

            foreach ($temparr as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $email;
                    $recipients_str .= $email.' ';
                }
            }
            $this->recipients = $recipients;
        } else {
            $this->recipients = [];
        }
        

        //////////////////////////////////////////////////////////////

        $lookup_results = collect([]);

        if (!$this->lookup) {

            $this->lookup_results = [];

        } else {

            $input['limit'] = 10;

            $words = explode(' ', $this->lookup);

            if (count($words) == 1) {

                $input['first_name'] = $this->lookup;
                $input['last_name']  = null;
                $a = $this->participantQuery($input);

                $input['last_name']  = $this->lookup;
                $input['first_name']  = null;
                $b = $this->participantQuery($input);

                $lookup_results = $a->merge($b);

            } elseif (count($words) > 1) {

                $input['first_name'] = array_shift($words);
                $input['last_name']  = implode(' ', $words);
                $lookup_results = $this->participantQuery($input);
            }

        }

        //////////////////////////////////////////////////////////////

        return view('livewire.volunteers-live', compact('volunteer_emails', 'participants', 'from', 'lookup_results'));
    }
}
