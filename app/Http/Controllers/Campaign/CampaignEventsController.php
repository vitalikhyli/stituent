<?php

namespace App\Http\Controllers\Campaign;

use App\CampaignEvent;
use App\CampaignEventInvite;
use App\Donation;
use App\Http\Controllers\Controller;
use App\Participant;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class CampaignEventsController extends Controller
{
    public function show($id)
    {
        $event = CampaignEvent::find($id);
        $this->authorize('basic', $event);
        //dd($event);
        return view('campaign.events.show', compact('event'));
    }
    public function totalGuests($event_id)
    {
        $event = CampaignEvent::find($event_id);

        return $event->invitees()->where('can_attend', true)->sum('guests')
               + $event->invitees()->where('can_attend', true)->count();
    }

    public function toggleTOF($event_id, $participant_id, $field)
    {
        $model = CampaignEventInvite::where('participant_id', $participant_id)
                                    ->where('campaign_event_id', $event_id)
                                    ->first();
        $model->$field = ($model->$field) ? false : true;
        $model->save();

        return ($model->$field) ? 'true' : 'false';
    }

    public function guestCount($event_id, $participant_id, $count)
    {
        if (! is_numeric($count) || strpos($count, '.') !== false) {
            return 'error';
        }

        $model = CampaignEventInvite::where('participant_id', $participant_id)
                                    ->where('campaign_event_id', $event_id)
                                    ->first();
        $model->guests = $count;
        $model->save();

        return $count;
    }

    public function removeInvite($event_id, $participant_id)
    {
        $participant = findParticipantOrImportVoter($participant_id, Auth::user()->team->id);

        $pivot = CampaignEventInvite::where('participant_id', $participant->id)
                                    ->where('campaign_event_id', $event_id)
                                    ->first();

        $pivot->delete();

        return redirect()->back();
    }

    public function addInvite($event_id, $participant_id)
    {
        $participant = findParticipantOrImportVoter($participant_id, Auth::user()->team->id);

        // Check for duplicates
        $pivot = CampaignEventInvite::where('participant_id', $participant->id)
                                    ->where('campaign_event_id', $event_id)
                                    ->first();

        $event = CampaignEvent::find($event_id);
        // dd($event_id, $participant_id, $event);

        if (! $pivot) {
            $pivot = new CampaignEventInvite;
            $pivot->participant_id = $participant->id;
            $pivot->campaign_event_id = $event_id;
            $pivot->team_id = Auth::user()->team->id;
            $pivot->user_id = Auth::user()->id;
            $pivot->save();

            // dd($participant, $participant->event($event->id));

            return view('campaign.events.invitees-new-row', compact('participant', 'event'));
        }

        return null;
    }

    public function guestsIndex($id)
    {
        $event = CampaignEvent::find($id);
        $invitees = $event->invitees()->orderBy('last_name')->get();
        $num_guests = $event->invitees()->where('can_attend', true)->sum('guests')
                          + $event->invitees()->where('can_attend', true)->count();

        return view('campaign.events.invitees', compact('event', 'invitees', 'num_guests'));
    }

    public function index()
    {
        // $sort_by_raised = request()->input('sort_by_raised');

        $start = Auth::user()->getMemory('campaign_events_filter_start');
        if ($start) {
            $start = Carbon::parse($start)->toDateString();
        }

        $end = Auth::user()->getMemory('campaign_events_filter_end');
        // $end = (!$end) ? Carbon::now()->toDateString() : $end;
        if ($end) {
            $end = Carbon::parse($end)->toDateString();
        }

        $events = CampaignEvent::where('team_id', Auth::user()->team->id);
        if ($start) {
            $events = $events->whereDate('date', '>=', $start);
        }
        if ($end) {
            $events = $events->whereDate('date', '<=', $end);
        }
        $events = $events->orderBy('date', 'desc')->get();

        // if ($sort_by_raised) $events = $events->orderBy('total_raised','desc')->get();

        //dd($start, $end, $events);

        return view('campaign.events.index', compact('events'));
    }

    public function edit($id)
    {
        $event = CampaignEvent::find($id);
        $this->authorize('basic', $event);

        return view('campaign.events.edit', compact('event'));
    }

    public function delete($id)
    {

        //Authorization
        $event = CampaignEvent::find($id);
        $this->authorize('basic', $event);

        //Unlink donations
        $donations = Donation::where('campaign_event_id', $event->id)->get();
        foreach ($donations as $donation) {
            $donation->campaign_event_id = null;
            $donation->save();
        }

        //Unlink invitations
        $invitations = CampaignEventInvite::where('campaign_event_id', $event->id)->get();
        foreach ($invitations as $invitation) {
            $invitation->delete();
        }

        //Delete model
        $event->delete();

        // Return
        return redirect(Auth::user()->team->app_type.'/events');
    }

    public function update(Request $request, $id, $close = null)
    {
        $event = CampaignEvent::find($id);

        $this->authorize('basic', $event);

        $event->user_id = Auth::user()->id;
        $event->date = Carbon::parse(request('date'))->toDateString();
        $event->time = (! request('time')) ? '' : Carbon::parse(request('time'))->toTimeString();
        $event->name = request('name');
        $event->venue_name = request('venue_name');
        $event->venue_street = request('venue_street');
        $event->venue_city = request('venue_city');
        $event->venue_state = request('venue_state');
        $event->venue_zip = request('venue_zip');

        $event->save();

        if ($close) {
            return redirect('/'.Auth::user()->team->app_type.'/events/');
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/events/'.$id.'/edit');
        }
    }

    public function store(Request $request)
    {
        $event = new CampaignEvent;
        $event->team_id = Auth::user()->team->id;
        $event->user_id = Auth::user()->id;
        $event->date = Carbon::parse(request('date'))->toDateString();
        $event->name = request('name');
        $event->save();

        return redirect('/'.Auth::user()->team->app_type.'/events/'.$event->id.'/edit');
    }

    public function filter(Request $request)
    {
        Auth::user()->addMemory('campaign_events_filter_start', request('campaign_events_filter_start'));
        Auth::user()->addMemory('campaign_events_filter_end', request('campaign_events_filter_end'));

        return redirect('/'.Auth::user()->team->app_type.'/events');
    }
}
