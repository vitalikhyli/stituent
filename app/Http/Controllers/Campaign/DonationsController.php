<?php

namespace App\Http\Controllers\Campaign;

use App\CampaignEvent;
use App\Donation;
use App\Http\Controllers\Controller;
use App\Traits\ExportTrait;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DonationsController extends Controller
{
    use ExportTrait;

    public function search()
    {
        return $this->index();
    }

    public function donationsQuery()
    {
        $event_id = request()->input('event_id');
        $search = request()->input('search');

        $donations = Donation::where('team_id', Auth::user()->team->id);

        if (! $event_id) {
            $start = Auth::user()->getMemory('donations_filter_start');
            if ($start) {
                $start = Carbon::parse($start)->toDateString();
            }

            $end = Auth::user()->getMemory('donations_filter_end');
            if ($end) {
                $end = (! $end) ? Carbon::now()->toDateString() : $end;
            }
            $end = Carbon::parse($end)->toDateString();

            if ($start) {
                $donations = $donations->whereDate('date', '>=', $start);
            }
            if ($end) {
                $donations = $donations->whereDate('date', '<=', $end);
            }
        } else {
            $event = CampaignEvent::find($event_id);
            $this->authorize('basic', $event);
            $donations = $donations->where('campaign_event_id', $event->id);

            $selected_event = $event;
        }

        if ($search) {
            $donations = $donations->where(function ($q) use ($search) {
                $q->orWhere('first_name', 'like', '%'.$search.'%');
                $q->orWhere('last_name', 'like', '%'.$search.'%');
                $q->orWhere('occupation', 'like', '%'.$search.'%');
                $q->orWhere('employer', 'like', '%'.$search.'%');
                $q->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        $donations = $donations->orderBy('date', 'desc')->get();

        return $donations;
    }

    public function export()
    {
        $donations = $this->donationsQuery();

        $donations = $donations->map(function ($q) {
            if ($q['campaign_event_id']) {
                $q['campaign_event'] = CampaignEvent::find($q['campaign_event_id'])->name;
            } else {
                $q['campaign_event'] = null;
            }

            return collect($q);
        });

        $donations = $donations->map(function ($q) {
            return collect($q->toArray())
                                ->only(['date',
                                        'amount',
                                        'fee',
                                        'method',
                                        'first_name',
                                        'last_name',
                                        'street',
                                        'city',
                                        'state',
                                        'zip',
                                        'occupation',
                                        'employer',
                                        'notes',
                                        'campaign_event', ])
                                ->all();
        });

        return $this->createCSV($donations);
    }

    public function index()
    {
        $donations = $this->donationsQuery();

        $events = CampaignEvent::where('team_id', Auth::user()->team->id)
                               ->orderBy('date', 'desc')
                               ->get();

        $event_id = request()->input('event_id');
        $selected_event = (! $event_id) ? null : CampaignEvent::find($event_id);

        $search = request()->input('search');
        $search = (! $search) ? null : $search;

        return view('campaign.donations.index', compact('donations', 'events', 'selected_event', 'search'));
    }

    public function edit($id)
    {
        $donation = Donation::find($id);

        $events = CampaignEvent::where('team_id', Auth::user()->team->id)
                               ->orderBy('date', 'desc')
                               ->get();

        return view('campaign.donations.edit', compact('donation', 'events'));
    }

    public function delete($id)
    {
        // Authorization
        $donation = Donation::find($id);
        $this->authorize('basic', $donation);

        // Delete Model
        $donation->delete();

        // Return
        return redirect(Auth::user()->team->app_type.'/donations');
    }

    public function update(Request $request, $id, $close = null)
    {
        $donation = Donation::find($id);

        $this->authorize('basic', $donation);

        $donation->user_id              = Auth::user()->id;
        $donation->date                 = Carbon::parse(request('date'))->toDateString();
        $donation->amount               = (! request('amount')) ? 0 : request('amount');
        $donation->fee                  = (! request('fee')) ? 0 : request('fee');
        $donation->occupation           = request('occupation');
        $donation->employer             = request('employer');
        $donation->notes                = request('notes');
        $donation->first_name           = request('first_name');
        $donation->last_name            = request('last_name');
        $donation->campaign_event_id    = request('campaign_event_id');
        $donation->street               = request('street');
        $donation->city                 = request('city');
        $donation->state                = request('state');
        $donation->zip                  = request('zip');
        $donation->method               = request('method');

        if (request('participant_id')) {
            $participant = findParticipantOrImportVoter(request('participant_id'), $donation->team_id);
            $donation->participant_id = ($participant) ? $participant->id : null;
        } elseif (!request('linked')) {
            $donation->participant_id = null;
        }

        $donation->save();

        if ($close) {
            return redirect('/'.Auth::user()->team->app_type.'/donations/');
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/donations/'.$id.'/edit');
        }
    }

    public function store(Request $request)
    {
        $donation = new Donation;
        $donation->team_id = Auth::user()->team->id;

        $donation->team_id = Auth::user()->team->id;
        $donation->user_id = Auth::user()->id;
        $donation->date = Carbon::parse(request('date'))->toDateString();
        $donation->amount = (! request('amount')) ? 0 : request('amount');
        $donation->fee = (! request('fee')) ? 0 : request('fee');
        $donation->occupation = request('occupation');
        $donation->employer = request('employer');
        $donation->notes = request('notes');
        $donation->first_name = request('first_name');
        $donation->last_name = request('last_name');
        $donation->campaign_event_id = request('campaign_event_id');
        $donation->street = request('street');
        $donation->city = request('city');
        $donation->state = request('state');
        $donation->zip = request('zip');
        $donation->method = request('method');

        $participant = findParticipantOrImportVoter(request('the_id'), $donation->team_id);
        $donation->participant_id = ($participant) ? $participant->id : null;

        $donation->save();

        if (request('return_to_participant')) {
            return redirect('/'.Auth::user()->team->app_type.'/participants/'.request('return_to_participant').'/edit');
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/donations');
        }
    }

    public function filter(Request $request)
    {
        Auth::user()->addMemory('donations_filter_start', request('donations_filter_start'));
        Auth::user()->addMemory('donations_filter_end', request('donations_filter_end'));

        return redirect('/'.Auth::user()->team->app_type.'/donations');
    }
}
