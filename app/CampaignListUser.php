<?php

namespace App;

use App\VolunteerEmail;

use Carbon\Carbon;
use Auth;
use Mail;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignListUser extends Model
{
    // use HasFactory;
    use SoftDeletes;


    protected $table = 'list_user';

    public function sendLinkToVolunteer($mail_data)
    {
        config(['mail.username' => env('MAIL_CAMPAIGN_USERNAME')]);
        config(['mail.password' => env('MAIL_CAMPAIGN_PASSWORD')]);

        $mail_data['recipient_name']    = $this->user->name;
        $mail_data['recipient']         = $this->user->email;
        $mail_data['link']              = $this->uuid;

        $user = User::find($mail_data['from_user_id']);

        $mail_data['from']              = $user->email;
        $mail_data['from_name']         = $user->name;

        $volunteer_email = new VolunteerEmail;
        $volunteer_email->team_id = Auth::user()->team_id;
        $volunteer_email->user_id = Auth::user()->id;
        $volunteer_email->subject = $mail_data['subject'];
        $volunteer_email->body = nl2br($mail_data['body1']
                                        ."\n\n"."Here's your unique login link:"
                                        ."\n".config('app.url').'/lists/'.$mail_data['link']
                                        ."\n\n"
                                        .$mail_data['body2']);
        $volunteer_email->recipients = $mail_data['recipient'];
        $volunteer_email->carbon = 'individual';
        $volunteer_email->save();


        
        Mail::send([], [], function ($message) use ($volunteer_email, $mail_data) {
            $message->setBody($volunteer_email->body, 'text/html');
            $message->addPart(strip_tags($volunteer_email->body), 'text/plain');
            $message->from('sender@campaignfluency.com', $mail_data['from_name']);
            $message->replyTo(Auth::user()->email, $mail_data['from_name']);
            $message->to($volunteer_email->recipients, $mail_data['recipient_name']);
            $message->subject($volunteer_email->subject);
        });

        $volunteer_email->sent_at = Carbon::now();
        $volunteer_email->save();

        $this->emailed_at = now();
        $this->save();
    }


    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function list()
    {
        return $this->belongsTo(CampaignList::class);
    }

    public function recordClick()
    {
    	$this->clicks_count++;
    	$this->save();
    }

}
