<?php

/* EDITING OUT UNNECESSSARY CONTROLLER
namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Contact;

use Auth;

class FollowupsController extends Controller
{

    public function index()
    {
        $followups = Contact::where('followup',1)
                            ->where('followup_done',0)
                            ->where('private',0)
                            ->where('team_id', Auth::user()->team->id)
                            ->orWhere(function ($q) {
                                $q->where('private',1);
                                $q->where('user_id', Auth::user()->id);
                                $q->where('followup_done',0);
                                $q->where('followup',1);
                                $q->where('team_id', Auth::user()->team->id);
                            })
                            ->orderBy('followup_on','desc');

        $total = $followups->count();
        $followups = $followups->get();

        return view('u.contacts.followups', compact('followups','total'));
    }

    public function indexDone()
    {
        $followups_done = Contact::where('followup',1)
                            ->where('followup_done',1)
                            ->where('private',0)
                            ->where('team_id', Auth::user()->team->id)
                            ->orWhere(function ($q) {
                                $q->where('private',1);
                                $q->where('user_id', Auth::user()->id);
                                $q->where('followup_done',1);
                                $q->where('followup',1);
                                $q->where('team_id', Auth::user()->team->id);
                            })
                            ->orderBy('followup_on','desc');

        $total = $followups_done->count();
        $followups_done = $followups_done->get();

        return view('u.contacts.followups_done', compact('followups_done','total'));
    }

    public function followupDone($id, $tof)
    {
        $followup = Contact::find($id);

        $this->authorize('basic', $followup);

        $followup->followup_done = ($tof == 'true') ? 1 : 0;
        $followup->save();
        return Auth::user()->outstandingFollowUps()->count();
    }

    public function followupCount()
    {
        $followups = Auth::user()->outstandingFollowUps();
        return $followups->count();
    }
}
