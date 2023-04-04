<?php

namespace App\Http\Controllers\Campaign;

use App\Campaign;
use App\CampaignEvent;
use App\CampaignList;
use App\CampaignParticipant;
use App\District;
use App\Http\Controllers\Controller;
use App\Municipality;
use App\Participant;
use App\Tag;
use App\Traits\ExportTrait;
use App\Traits\ListBuilderTrait;
use App\Traits\ParticipantQueryTrait;
use App\Voter;
use App\Action;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Schema;

use Carbon\Carbon;

class ParticipantsController extends Controller
{
    use ParticipantQueryTrait;
    use ExportTrait;
    use ListBuilderTrait;

    public function store(Request $request)
    {
        $participant = new Participant;

        $name = request('new_name');

        if (! $name) {
            return redirect()->back();
        }

        $name_components = explode(' ', $name);
        $participant->first_name = array_shift($name_components);
        $participant->last_name = implode(' ', $name_components);
        $participant->team_id = Auth::user()->team->id;
        $participant->user_id = Auth::user()->id;
        $participant->save();

        return redirect('/campaign/participants/'.$participant->id.'/edit');
    }
    public function index()
    {
        $participants = Participant::where('team_id', Auth::user()->team_id)
                                   ->latest()
                                   ->get();
        return view('campaign.participants.recent', compact('participants'));
    }
    public function new()
    {
        return view('campaign.participants.new');
    }

    public function addAction($id)
    {
        $participant = findParticipantOrImportVoter($id, Auth::user()->team->id);
        return redirect('/campaign/participants/'.$participant->id.'/edit?action=add');
    }

    public function toggleTag($participant_id, $tag_id)
    {

        // $participant = Participant::find($participant_id);
        $participant = findParticipantOrImportVoter($participant_id, Auth::user()->team->id);

        $this->authorize('basic', $participant);

        $tag = Tag::find($tag_id);
        $this->authorize('basic', $tag);

        if ($participant->taggedWith($tag)) {
            $participant->tags()->detach([$tag->id]);

            return json_encode(['has_tag'  => false]);
        }

        if (! $participant->taggedWith($tag)) {
            $data = [];
            $data[$tag->id] = ['team_id'  => Auth::user()->team->id,
                               'user_id'  => Auth::user()->id,
                               'voter_id' => $participant->voter_id, ];
            $participant->tags()->attach($data);

            return json_encode(['has_tag'  => true]);
        }
    }

    public function export()
    {
        $request = request()->input();

        $participants = $this->participantQuery($request);

        $array = [];
        foreach ($participants as $participant) {
            $array[] = ['voter_id'          => strtoupper($participant->voter_id),
                        'first_name'        => $participant->first_name,
                        'last_name'         => $participant->last_name,
                        'full_name'         => $participant->full_name,
                        'full_address'      => $participant->full_address,
                        'phone'             => $participant->phone,
                        'email'             => $participant->email,
                        'volunteering'      => $participant->volunteering,
                    ];

        }

        // This anonymous function was causing problems
        //
        // $participants = $participants->map(function ($q) {
        //     return collect($q->toArray())
        //                         ->only(['voter_id',
        //                                 'first_name',
        //                                 'last_name',
        //                                 'full_name',
        //                                 'full_address', ])
        //                         ->all();
        // });

        return $this->createCSV($array);
    }

    public function showTag(Request $request, $tag_id)
    {
        $tag = Tag::find($tag_id);
        $this->authorize('basic', $tag);

        $participants = $tag->participants;

        $campaign = CurrentCampaign();

        $lists = CampaignList::where('team_id', Auth::user()->team->id)
                                ->orderBy('updated_at', 'desc')
                                ->get();

        if (! isset($_GET['tag_with'])) {
            $tag_with = null;
        } else {
            $tag_with = Tag::find($_GET['tag_with']);
        }

        return view('campaign.participants.index', compact('tag_with', 'participants', 'campaign', 'lists', 'tag'));
    }

    // public function index(Request $request, $change_query = null)
    // {
    //     if ($change_query) {
    //         $_GET['change_query']    = true;
    //         $request['change_query'] = true;
    //     }

    //     $participants           = Auth::user()->team->participants;

    //     $campaign = CurrentCampaign();

    //     $lists = CampaignList::where('team_id', Auth::user()->team->id)
    //                             ->orderBy('updated_at', 'desc')
    //                             ->get();

    //     if (!isset($_GET['tag_with'])) {
    //         $tag_with = null;
    //     } else {
    //         $tag_with =  Tag::find($_GET['tag_with']);
    //     }

    //     return view('campaign.participants.index', compact('tag_with', 'participants','campaign', 'lists'));
    // }

//=========================================================================
    
    public function votersIndex(Request $request)
    {

        session()->put('voter_query_start', Carbon::now()->milliseconds);
        
        // ===============> Clear Form
        if (request('clear')) {
            foreach ($request->input() as $key => $input) {
                unset($request[$key]);
            }
        }


        // ===============> Per Page
        $perpage = (request('perpage')) ? request('perpage') : 100;

        // ===============> Logic

        //dd("laz");

        if (!request()->input()) {

            $voters = collect([]);


        } else {

            // ===============> Force column match for Union Query
            $p = new Participant;
            $p_cols = collect(Schema::getColumnListing($p->getTable()));

            $v = new Voter;
            $v_cols = collect(Schema::getColumnListing($v->getTable()));

            $v_missing = $p_cols->diff($v_cols);
            $p_missing = $v_cols->diff($p_cols);

            $all_cols = $p_cols->merge($v_cols)->unique()->sort();

            $v_select = '';
            $p_select = '';

            foreach ($all_cols as $col) {
                if ($v_missing->contains($col)) {
                    $v_select .= 'null as '.$col.', ';
                } else {
                    $v_select .= $col.', ';
                }
                if ($p_missing->contains($col)) {
                    $p_select .= 'null as '.$col.', ';
                } else {
                    $p_select .= $col.', ';
                }
            }

            $v_select .= '0 as is_participant ';
            $p_select .= '1 as is_participant ';

            $voters       = Voter::select(DB::raw($v_select));
            $participants = Participant::where('team_id', Auth::user()->team_id)
                                       ->select(DB::raw($p_select));

            // ===============> PARTICIPANTS QUERY
            if (request('filter_by_tag')) {

                $tag = Tag::find(request('filter_by_tag'));

                if ($tag) {

                    $this->authorize('basic', $tag);

                    $participant_ids_tags = DB::table('participant_tag')
                                              ->where('tag_id', $tag->id)
                                              ->where('team_id', Auth::user()->team->id)
                                              ->pluck('participant_id')
                                              ->toArray();

                }

            }

            if (request('filter_by_support')) {
                $support    = explode(' ', request('filter_by_support'));
                $level      = $support[1];
                $operator   = $support[0];
                $participant_ids_support = DB::table('campaign_participant')
                                             ->where('support', $operator, $level)
                                             ->whereNotNull('support')
                                             ->where('team_id', Auth::user()->team->id)
                                             ->where('campaign_id', CurrentCampaign()->id)
                                             ->pluck('participant_id')
                                             ->toArray();
            }
            //dd("Laz");

            if (isset($participant_ids_support) && isset($participant_ids_tags)) {

                $participant_ids = array_intersect(
                                        $participant_ids_support,
                                        $participant_ids_tags
                                   );

            } elseif (isset($participant_ids_tags)) {
                $participant_ids = $participant_ids_tags;
            } elseif (isset($participant_ids_support)) {
                $participant_ids = $participant_ids_support;
            }

            if (isset($participant_ids)) $participants->whereIn('id', $participant_ids);


            // ===============> FORM BASIC FIELDS

            if (request('participants_only') ||
                request('filter_by_tag') ||
                request('filter_by_support')
               ) {
                $voters->where('id', 'THIS_ID_DOES_NOT_EXIST');
            }
            
            if (!request('include_archived')) {
                $voters->whereNull('archived_at');
            }

            if (request('last_name')) {
                $voters->where('last_name', 'LIKE', request('last_name').'%');
                $participants->where('last_name', 'LIKE', request('last_name').'%');
            }
            if (request('first_name')) {
                $voters->where('first_name', 'LIKE', request('first_name').'%');
                $participants->where('first_name', 'LIKE', request('first_name').'%');
            }
            if (request('municipalities')) {
                $voters->whereIn('city_code', request('municipalities'));
                $citynames = Municipality::whereIn('id', request('municipalities'))->pluck('name');
                $participants->whereIn('address_city', $citynames);
            }

            if (request('street')) {
                $voters->where('address_street', 'LIKE', request('street').'%');
                $participants->where('address_street', 'LIKE', request('street').'%');
            }
                        

            // ===============> EXCLUDE VOTERS ALREADY PULLED INTO PARTICIPANTS QUERY
            $participant_voter_ids = $participants->whereNotNull('voter_id')->pluck('voter_id');
            $voters->whereNotIn('id', $participant_voter_ids);

            // ===============> UNION TWO QUERIES
            if ($participant_voter_ids->first()) {
                $voters = $voters->union($participants);
            }

            // ===============> SORT  <================
            
            if (request('sort_by') == 'address') {
                // $voters = $voters->orderBy('household_id');
                $voters = $voters->orderBy('address_city')
                                 ->orderBy('address_street')
                                 ->orderBy('address_number');
            }
            if (request('sort_by') == 'zip') {
                $voters = $voters->orderBy('address_zip');
            }
            if (request('sort_by') == 'precinct') {
                $voters = $voters->orderByRaw('cast(ward as unsigned), ward');
                $voters = $voters->orderByRaw('cast(precinct as unsigned), precinct');
            }
            $voters = $voters->orderBy('last_name');                // Always Order by Last


            // ===============> PAGINATE  <================
            
            $voters = $voters->simplePaginate($perpage);
        }

        $lists = CampaignList::where('team_id', Auth::user()->team->id)
                                ->orderBy('updated_at', 'desc')
                                ->get();

        if (!isset($_GET['tag_with'])) {
            $tag_with = null;
        } else {
            $tag_with = Tag::find($_GET['tag_with']);
        }

        // dd(Carbon::now()->format("g:i a")." - Code Line: ".__LINE__." **** Loading in: ".(Carbon::now()->milliseconds - session('start'))." ms");

        $municipalities = $this->getMunicipalities();
        //dd($municipalities);

        return view('campaign.participants.voters', 
                        ['municipalities'   => $municipalities,
                         'pages'            => 1,
                         'tag_with'         => $tag_with,
                         'voters'           => $voters,
                         'campaign'         => CurrentCampaign(),
                         'lists'            => $lists,
                         'perpage'          => $perpage
                        ]);
    }


    public function toggleMinimize()
    {
        $status = Auth::user()->getMemory('campaign_participant_form');
        if ($status == 'min') {
            $status = Auth::user()->addMemory('campaign_participant_form', 'max');

            return 'max';
        } elseif ($status == 'max') {
            $status = Auth::user()->addMemory('campaign_participant_form', 'min');

            return 'min';
        } else {
            $status = Auth::user()->addMemory('campaign_participant_form', 'min');

            return 'min';
        }
    }

    public function toggleSearch()
    {
        $status = Auth::user()->getMemory('campaign_search_mode');
        if ($status == 'basic') {
            $status = Auth::user()->addMemory('campaign_search_mode', 'advanced');
            Auth::user()->addMemory('campaign_participant_form', 'max');

            return 'advanced';
        } elseif ($status == 'advanced') {
            $status = Auth::user()->addMemory('campaign_search_mode', 'basic');

            return 'basic';
        } else {
            $status = Auth::user()->addMemory('campaign_search_mode', 'basic');

            return 'basic';
        }
    }

    public function setSupport($participant_id, $campaign_id, $level)
    {
        $campaign = Campaign::find($campaign_id);
        $this->authorize('basic', $campaign);

        $participant = findParticipantOrImportVoter($participant_id, Auth::user()->team->id);
        $this->authorize('basic', $participant);

        $pivot = CampaignParticipant::where('campaign_id', $campaign->id)
                                    ->where('participant_id', $participant->id)
                                    ->first();

        if (! $pivot) {
            $pivot = new CampaignParticipant;
            $pivot->team_id = Auth::user()->team->id;
            $pivot->user_id = Auth::user()->id;
            $pivot->campaign_id = $campaign->id;
            $pivot->participant_id = $participant->id;
            $pivot->save();
        }

        if ($pivot->support == $level) {
            $pivot->support = null;
            $pivot->save();

            return 'do_not_set_new';
        } else {
            $pivot->support = $level;
            $pivot->save();

            return $level;
        }
    }

    public function lookup($mode, $v = null, $extra = null)
    {
        $input['search'] = $v;
        $participants = $this->participantQuery($input);

        //    // Special -- Distinguish those who were invited
        //    if ($mode == 'invite') {
        //        $event = CampaignEvent::find($extra);
        //        $participants = $participants->map(function ($item) use ($event) {
        //            if ($event->isInvitee($item['id'])) {
        //                $item['invited'] = true;
        //            }
        //            return $item;
        //        });
        //    }

        if ($mode == 'donations') {
            $view = 'campaign.donations.list-donation';
        }
        if ($mode == 'invite') {
            $view = 'campaign.events.list-invite';
        }

        return view($view, compact('participants'));
    }

    public function show($id)
    {
        //dd($id);
        return redirect('/campaign/participants/'.$id.'/edit');
        //////// Get Participant or Voter

        $voter = null;
        $participant = null;

        if (! IDisVoter($id)) {
            $participant = Participant::find($id);

            if ($participant->voter_id) {
                $participant->fillInParticipantFromVoter();
                return redirect('/campaign/participants/'.$participant->voter_id);
            }
            //dd($voter);
            $this->authorize('basic', $participant);
        } else {
            $participant = Participant::where('team_id', Auth::user()->team->id)
                                      ->where('voter_id', $id)
                                      ->first();

            if (! $participant) {
                $voter = Voter::find($id);

                if (! $voter) {
                    return redirect()->back();
                }
            } else {
                $this->authorize('basic', $participant);
            }
        }

        //////// Other Variables

        $campaigns = Campaign::where('team_id', Auth::user()->team->id)
                             ->orderBy('election_day', 'desc')
                             ->get();

        // Support Levels
        $support = null;
        if ($participant) {
            $support = CampaignParticipant::where('participant_id', $participant->id)
                                          ->where('campaign_id', $campaigns->first()->id)
                                          ->first();

            if (! $support) {
                $support = new CampaignParticipant;
                $support->support = null;
            }
        }

        $the_id = $id;

        return view('campaign.participants.show', compact('the_id',
                                                          // 'profile',
                                                          'participant',
                                                          'voter',
                                                          'campaigns',
                                                          'support'));
    }

    public function edit($id)
    {

        //////// Get Participant or Voter
        //dd($id);

        $voter = null;
        $participant = null;

        if (! IDisVoter($id)) {

            $participant = Participant::find($id);
            $this->authorize('basic', $participant);
            //dd($participant);
        } else {
            $participant = Participant::where('team_id', Auth::user()->team->id)
                                      ->where('voter_id', $id)
                                      ->first();

            if (! $participant) {
                $voter = Voter::find($id);
                // dd($voter->full_name);

                if (! $voter) {
                    return redirect()->back();
                }
            } else {
                $this->authorize('basic', $participant);
            }
        }

        //////// Other Variables

        $campaigns = Campaign::where('team_id', Auth::user()->team->id)
                             ->orderBy('election_day', 'desc')
                             ->get();

        // Support Levels
        $support = null;
        if ($participant) {
            $support = CampaignParticipant::where('participant_id', $participant->id)
                                          ->where('campaign_id', $campaigns->first()->id)
                                          ->first();

            if (! $support) {
                $support = new CampaignParticipant;
                $support->support = null;
            }
        }

        // All Volunteer Options (Any field starting with "volunteer_")
        $volunteer_options = new class {
        };

        $columns = Schema::getColumnListing('campaign_participant');

        foreach ($columns as $key => $field) {
            if (substr($field, 0, 10) == 'volunteer_') {
                $volunteer_field = substr($field, 10);
                if ($participant) {
                    $volunteer_options->$volunteer_field = ($support->$field) ? true : false;
                }
                if (! $participant) {
                    $volunteer_options->$volunteer_field = false;
                }
            }
        }

        $tag_options = Tag::thisTeam()->orderBy('name')->get();

        $the_id = $id;
        //dd($the_id);

        $actions = collect([]);
        if ($participant) {
            $actions = Action::where('participant_id', $participant->id)
                             ->latest()
                             ->get();
        }

        return view('campaign.participants.edit', compact('voter',
                                                            'participant',
                                                            'campaigns',
                                                            'support',
                                                            'volunteer_options',
                                                            'tag_options',
                                                            'the_id',
                                                            'actions'));
    }

    public function delete($id)
    {
        // Authorization
        $participant = Participant::find($id);
        $this->authorize('basic', $participant);

        $voter_id = $participant->voter_id;

        // Delete CampaignParticipant pivots
        CampaignParticipant::where('participant_id', $participant->id)->delete();

        // Delete Model
        $participant->delete();

        // Return
        if ($voter_id) {
            return redirect(Auth::user()->team->app_type.'/participants/'.$voter_id);
        } else {
            return redirect(Auth::user()->team->app_type.'/voters');
        }
    }

    public function update(Request $request, $id, $close = null)
    {
        // $participant = Participant::find($id);
        $participant = findParticipantOrImportVoter($id, Auth::user()->team->id);
        $this->authorize('basic', $participant);

        $participant->go_away           = (request('go_away')) ? true : false;
        $participant->deceased          = (request('deceased')) ? true : false;

        $participant->first_name        = request('first_name');
        $participant->middle_name       = request('middle_name');
        $participant->last_name         = request('last_name');

        $participant->address_number    = request('address_number');
        $participant->address_fraction  = request('address_fraction');
        $participant->address_street    = request('address_street');
        $participant->address_apt       = request('address_apt');
        $participant->address_city      = request('address_city');
        $participant->address_state     = request('address_state');
        $participant->address_zip       = request('address_zip');

        // Phones and Emails

        $participant->primary_email = (request('primary_email') != null) ? request('primary_email') : null;

        $participant->work_email = (request('work_email') != null) ? request('work_email') : null;

        $participant->primary_phone = (request('primary_phone') != null) ? request('primary_phone') : null;

        $participant->cell_phone = (request('cell_phone') != null) ? request('cell_phone') : null;

        $participant->work_phone = (request('work_phone') != null) ? request('work_phone') : null;

        $emails_array = [];
        foreach ($request->all() as $key => $val) {
            if (substr($key, 0, 6) == 'email_') {
                $email_id = substr($key, 6);
                if (! request('email_'.$email_id) && ! request('email-notes_'.$email_id)) {
                    continue;
                }
                $emails_array[] = [request('email_'.$email_id),
                                   request('email-notes_'.$email_id), ];
            }
            if (substr($key, 0, 6) == 'email_new') {
                if (! request('email_new') && ! request('email-notes_new')) {
                    continue;
                }
                $emails_array[] = [request('email_new'),
                                   request('email-notes_new'), ];
            }
        }

        $phones_array = [];
        foreach ($request->all() as $key => $val) {
            if (substr($key, 0, 6) == 'phone_') {
                $phone_id = substr($key, 6);
                if (! request('phone_'.$phone_id) && ! request('phone-notes_'.$phone_id)) {
                    continue;
                }
                $phones_array[] = [request('phone_'.$phone_id),
                                   request('phone-notes_'.$phone_id), ];
            }
            if (substr($key, 0, 6) == 'phone_new') {
                if (! request('phone_new') && ! request('phone-notes_new')) {
                    continue;
                }
                $phones_array[] = [request('phone_new'),
                                   request('phone-notes_new'), ];
            }
        }

        $participant->other_emails = ($emails_array) ? $emails_array : null;
        $participant->other_phones = ($phones_array) ? $phones_array : null;

        $participant->save();

        // Go Through Pivots
        //dd("Laz");
        $tags_to_sync = [];

        foreach ($request->input() as $field => $value) {

            // Tags
            if (substr($field, 0, 4) == 'tag_') {
                $tag_id = substr($field, 4) * 1;
                $tag = Tag::find($tag_id);
                $this->authorize('basic', $tag);
                if ($tag) {
                    $tags_to_sync[] = $tag->id;
                }
            }

            // Support Levels

            if (substr($field, 0, 8) == 'support_') {
                $campaign_id = substr($field, 8) * 1;

                $campaign = Campaign::find($campaign_id);
                $this->authorize('basic', $campaign);

                $pivot = CampaignParticipant::where('participant_id', $participant->id)
                                            ->where('campaign_id', $campaign->id)
                                            ->first();

                if (! $pivot) {
                    $pivot = new CampaignParticipant;
                    $pivot->team_id = Auth::user()->team->id;
                    $pivot->user_id = Auth::user()->id;
                    $pivot->campaign_id = $campaign->id;
                    $pivot->participant_id = $participant->id;
                    $pivot->voter_id = $participant->voter_id;
                }

                $pivot->support = $value;

                $pivot->save();
            }

            // Volunteer Options

            if (substr($field, 0, 10) == 'volunteer_') {
                $components = explode('_', $field);
                $campaign_id = $components[1] * 1;
                $field_name = 'volunteer_'.str_replace('-', '_', $components[2]);

                $campaign = Campaign::find($campaign_id);
                $this->authorize('basic', $campaign);

                $pivot = CampaignParticipant::where('participant_id', $participant->id)
                                            ->where('campaign_id', $campaign->id)
                                            ->first();

                if (! $pivot) {
                    $pivot = new CampaignParticipant;
                    $pivot->team_id = Auth::user()->team->id;
                    $pivot->campaign_id = $campaign->id;
                    $pivot->participant_id = $participant->id;
                }

                $pivot->$field_name = ($value) ? 1 : 0;

                $pivot->save();
            }

            // Notes

            if (str_starts_with($field, 'notes_')) {
                $campaign_id = substr($field, 6) * 1;

                $campaign = Campaign::find($campaign_id);
                $this->authorize('basic', $campaign);

                $pivot = CampaignParticipant::where('participant_id', $participant->id)
                                            ->where('campaign_id', $campaign->id)
                                            ->first();

                if (! $pivot) {
                    $pivot = new CampaignParticipant;
                    $pivot->team_id = Auth::user()->team->id;
                    $pivot->user_id = Auth::user()->id;
                    $pivot->campaign_id = $campaign->id;
                    $pivot->participant_id = $participant->id;
                    $pivot->voter_id = $participant->voter_id;
                }

                $pivot->notes = $value;

                $pivot->save();
            }

        }

        $sync_data = [];
        foreach ($tags_to_sync as $key => $tag_id) {
            $sync_data[$tag_id] = [
                                    'voter_id' => $participant->voter_id,
                                    'team_id' => Auth::user()->team->id,
                                    'user_id' => Auth::user()->id,
                                  ];
        }
        //dd("Laz2");
        $action_name = "Updated Tags";
        $action_details = "";
        //dd($sync_data);
        $sync_arrs = $participant->tags()->sync($sync_data);
        //dd($sync_arrs);

        if (count($sync_arrs['attached']) > 0) {
            $tags = Tag::whereIn('id', $sync_arrs['attached'])->get()->implode('name', ', ');
            //dd($tags);
            $action_details .= "ADDED: ".$tags;
        }
        if (count($sync_arrs['detached']) > 0) {
            $tags = Tag::whereIn('id', $sync_arrs['detached'])->get()->implode('name', ', ');
            if ($action_details) {
                $action_details .= " -  ";
            }
            $action_details .= "REMOVED: ".$tags;
        }
        //dd($action_details);
        if ($action_details) {
            addCustomActionToParticipant($participant, $action_name, $action_details, null, true);
        }

        $participant->fillInParticipantFromVoter();
        //dd("Laz3");
        if ($close) {
            return redirect('/'.Auth::user()->team->app_type.'/participants/'.$id);
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/participants/'.$id.'/edit');
        }
    }
}
