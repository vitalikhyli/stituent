<?php

namespace App\Http\Controllers\Campaign;

use App\CampaignList;
use App\District;
use App\Http\Controllers\Controller;
use App\Participant;

use App\Voter;
use App\CampaignListUser;

use Auth;
use Illuminate\Http\Request;

use App\Traits\ExportTrait;

use Carbon\Carbon;

class CampaignListsController extends Controller
{
    use ExportTrait;

    public function removeValueFromArray($array, $value)
    {
        if (($key = array_search($value, $array)) !== false) {
                unset($array[$key]);
        }

        return $array;
    }

    public function loginGuestByLink($uuid)
    {
        $assignment = CampaignListUser::where('uuid', $uuid)->first();        

        if ($assignment) {

            if (Carbon::parse($assignment->expires_at)->isPast()) {
                return abort('403'); // Link has expired -- notify them somehow
            }
            
            Auth::logout();
            $success = Auth::logInUsingID($assignment->user_id);

            if ($success) {

                $user = Auth::user();
                $user->current_team_id = $assignment->team_id; // For AppGate compliance
                $user->save();

                $this->authorize('hasBeenAssignedTo', $assignment->list);

                $assignment->recordClick();
                
                $user = Auth::user();
                $user->last_login = now();
                $user->save();

                if ($assignment->type == 'list') {
                    return redirect('/campaign/phonebank/'.$assignment->list_id);
                }

                if ($assignment->type == 'canvass') {
                    return redirect('/campaign/doors/'.$assignment->list_id);
                }                
            }
        }

        return abort('403');
    }

    //----------------------------------------------------------------------------

    public $export = [];
    public $fields;
    public $list_id;
    public $group_by_household;
    public $include_support;
    public $include_emails;

    public $include_phones;
    public $phones_columns;

    public $include_volunteers;
    public $volunteers_columns;

    public $include_tags;
    public $include_lists;

    public function exportForm(Request $request)
    {
        //$this->fields = null; // will be filled in with default
        $this->list_id = request('modal_export_list_id') * 1;
        $fields_arr = [];
        if (is_array(request('modal_export_fields'))) {
            foreach (request('modal_export_fields') as $field) {
                $fields_arr[$field] = 1;
            }
        }
        $this->fields = $fields_arr;
        //dd($this->fields);

        $this->group_by_household = request('group_by_household') == 'true' ? true : false;
        $this->include_emails =     request('include_emails') == 'on' ? true : false;
        $this->include_phones =     request('include_phones') == 'on' ? true : false;
        $this->include_support =    request('include_support') == 'on' ? true : false;
        $this->include_volunteers = request('include_volunteers') == 'on' ? true : false;
        $this->include_tags =       request('include_tags') == 'on' ? true : false;
        $this->include_lists =      request('include_lists') == 'on' ? true : false;
        $this->volunteers_columns = request('volunteers_columns') == 'on' ? true : false;
        $this->phones_columns     = request('phones_columns') == 'on' ? true : false;
        
        return $this->startExport();
    }

    public function startExport()
    {
        set_time_limit(240);

        $list = CampaignList::find($this->list_id);
        $this->authorize('basic', $list);

        $this->buildExportCollection();

        if ($this->group_by_household) {
            $this->groupByHousehold();
        }

        $this->removeVoterIDFieldExceptForDevelopers();

        return $this->createCSV($this->export);
    }

    public function groupByHousehold()
    {
        $collection = collect($this->export)->sortBy('household_id');
        $grouped = $collection->groupBy('household_id');
        
        $final_format = [];

        $grouped_fields = [];

        if ($this->include_emails)                                   $grouped_fields[] = 'emails';
        if ($this->include_phones && !$this->phones_columns)         $grouped_fields[] = 'phones';
        if ($this->include_support)                                  $grouped_fields[] = 'support';
        if ($this->include_volunteers && !$this->volunteers_columns) $grouped_fields[] = 'volunteers';
        if ($this->include_tags)                                     $grouped_fields[] = 'tags';
        if ($this->include_lists)                                    $grouped_fields[] = 'lists';

        //------------------------------------------------------------------------

        foreach ($grouped as $household_id => $voters) {
            $first = $voters[0];
            //dd($first);
            if (isset($this->fields['best_address'])) {
                $row['best_address_full'] = $first['best_address_full'];
                $row['best_address_street'] = $first['best_address_street'];
                $row['best_address_city'] = $first['best_address_city'];
                $row['best_address_state'] = $first['best_address_state'];
                $row['best_address_zip'] = $first['best_address_zip'];
            }

            $row['household_name'] = $this->getHouseholdName($voters);
            
            $residents = "";
            foreach ($voters as $voter) {
                if (isset($this->fields['name'])) {
                    $residents .= $voter['full_name'];
                }
                $details = "";
                if (isset($this->fields['age/gender'])) {
                    $details .= $voter['age'].$voter['gender'];
                }
                if (isset($this->fields['party'])) {
                    $details .= ' ('.$voter['party'].')';
                }
                if ($details) {
                    $residents .= ' - '.$details."\n";
                }
            }
            $row['residents'] = trim($residents);

            $other_addresses = "";
            foreach ($voters as $voter) {
                if (isset($this->fields['voter_file_address']) && isset($this->fields['best_address_full'])) {
                    if ($voter['voter_file_address_full'] != $first['best_address_full']) {
                        $other_addresses .= $voter['voter_file_address_full']." (".$voter['full_name']." - Voter File)\n";
                    }
                }
                if (isset($this->fields['mailing_address'])) {
                    if ($voter['mailing_address_full']) {
                        if (isset($first['best_address_full'])) {
                            if ($voter['mailing_address_full'] != $first['best_address_full']) {
                                $other_addresses .= $voter['mailing_address_full']." (".$voter['full_name']." - Mailing Address)\n";
                            }
                        } else {
                            $other_addresses .= $voter['mailing_address_full']." (".$voter['full_name']." - Mailing Address)\n";
                        }
                    }
                }
            }
            $row['other_addresses'] = $other_addresses;

            if (isset($this->fields['ward/precinct'])) {
                $row['voter_file_ward'] = $first['voter_file_ward'];
                $row['voter_file_precinct'] = $first['voter_file_precinct'];
            }

            //------------------------------------------------------------------------

            foreach ($grouped_fields as $field) {
                $field_str = "";
                if (isset($first[$field])) {
                    foreach ($voters as $voter) {
                        if ($voter[$field]) {
                            $field_str .= "[".$voter['full_name']."] ".$voter[$field]."\n";
                        }
                    }
                }
                $row[$field] = $field_str;
            }


            //------------------------------------------------------------------------
            
            if ($this->include_phones && $this->phones_columns) {

                foreach (['voter_file',
                          'primary', 
                          'cell',
                          'work',
                          'cf_plus_home', 
                          'cf_plus_work',
                          'other'] as $key) {

                    $row[$key] = null;

                    $who = [];
                    
                    if (count($voters) == 1) {
                        $row[$key] = $first[$key];
                        continue;
                    }

                    foreach ($voters as $voter) {
                        if (!$voter[$key]) continue;
                        $who[] = $voter['full_name'].': '.$voter[$key];
                    }

                    $row[$key] = implode("\n", $who);

                }

            }

            //------------------------------------------------------------------------
            
            if ($this->include_volunteers && $this->volunteers_columns) {

                foreach(Participant::getVolunteerColumns() as $vol_type) {

                    $who = [];

                    foreach ($voters as $voter) {

                        if ($voter[$vol_type]) {
                            $who[] = $voter['full_name'];
                        }

                    }

                    $row[$vol_type] = implode("\n", $who);

                }

            }

            //------------------------------------------------------------------------


            $final_format[] = $row;
        }
        //dd($final_format);
        $this->export = $final_format;
    }

    public function getHouseholdName($grouped_household)
    {
        if (!isset($this->fields['name'])) {
            return "Current Residents";
        }
        $grouped_household = collect($grouped_household);
        // foreach ($grouped_household as $gh) {
        //     $gh->last_name = trim($gh->last_name);
        // }
        $last_names = $grouped_household->where('last_name', '<>', null)
                                                ->groupBy('last_name')
                                                ->map(function ($last_names) {
                                                    return $last_names->count();
                                                });
        $household_name = "";                                     //dd($last_names);
        if ($last_names->count() > 2) {

            $grouped_household_sorted = $grouped_household->sortByDesc(3);
            $tempfirst = trim($grouped_household_sorted[0]['last_name']);
            $tempsecond = trim($grouped_household_sorted[1]['last_name']);
            if ($tempfirst == $tempsecond && isset($grouped_household_sorted[2]['last_name'])) {
                $tempsecond = $grouped_household_sorted[2]['last_name'];
            }
            $household_name = $tempfirst
                        .' and '
                        .$tempsecond
                        .' Household';

        } elseif ($last_names->count() == 2) {

            $household_name = $grouped_household[0]['last_name'].' and '
                        .$grouped_household[1]['last_name'].' Household';

        } elseif ($last_names->count() == 1) {

            if ($grouped_household->count() > 1) {

                // One last name, multiple people
                $household_name = $grouped_household->first()['last_name'].' Household';
            } else {

                // One person
                //dd($grouped_household->first());
                $household_name = $grouped_household->first()['full_name'];
            }

        } else {

            //For some reason, no last names

            $household_name = 'Current Residents';
        }
        //dd("Laz");
        return $household_name;
    }

    public function getDefaultExportFields()
    {
        return ['voter_id',
                'first_name',
                'last_name',
                'full_name',
                'full_address'
                ];
    }

    public function exportByHousehold()
    {
        $voters = CampaignList::find($this->list_id)->voters()->get();

        $this->fields[] = 'all_residents';
        $this->fields[] = 'all_voter_ids';

        $remove = ['first_name',
                    'middle_name',
                    'last_name',
                    'voter_id',
                    'phone_list', ];

        $tempfields = [];

        foreach ($this->fields as $field) {
            if (!in_array($field, $remove)) {
                $tempfields[] = $field;
            }
        }

        $this->fields = $tempfields;


        foreach ($voters as $voter) {

            $voter = $voter->replaceWithParticipantFields(['first_name',
                                                           'middle_name',
                                                           'last_name',
                                                           'full_name',
                                                           'address_number',
                                                           'address_fraction',
                                                           'address_street',
                                                           'address_apt',
                                                           'address_city',
                                                           'address_state',
                                                           'address_zip',
                                                           'full_address']);
                                                           
        }
 

        $constituents_householded = $voters->groupBy('full_address');

        $constituents = collect([]);

        foreach ($constituents_householded as $household => $constituents_in_house) {

            // Group by last names of residents
            $all_residents_str = '';
            foreach ($constituents_in_house as $resident) {

                // $resident = $resident->replaceWithParticipantFields(['first_name',
                //                                                      'middle_name',
                //                                                      'last_name',
                //                                                      'full_name']);

                $all_residents_str .= $resident->full_name.'; ';
                $resident->last_name = ucwords(strtolower($resident->last_name), " \t\r\n\f\v'-");
            }

            $last_names = $constituents_in_house->where('last_name', '<>', null)
                                                ->groupBy('last_name')
                                                ->map(function ($last_names) {
                                                    return $last_names->count();
                                                });

            if ($last_names->count() > 2) {

                // More than two last names. Get two most common

                $constituents_in_house_sorted = $constituents_in_house->sortByDesc(2);
                $family = $constituents_in_house_sorted[0]->last_name
                            .' and '
                            .$constituents_in_house_sorted[1]->last_name
                            .' Household';

            } elseif ($last_names->count() == 2) {

                $family = $constituents_in_house[0]->last_name.' and '
                            .$constituents_in_house[1]->last_name.' Household';

            } elseif ($last_names->count() == 1) {

                if ($constituents_in_house->count() > 1) {

                    // One last name, multiple people
                    $family = $constituents_in_house->first()->last_name.' Household';
                } else {

                    // One person
                    $family = $constituents_in_house->first()->full_name;
                }

            } else {

                //For some reason, no last names

                $family = 'Current Residents';
            }

            $first = $constituents_in_house->first();

            if ($first->is_participant) {
                $new_household = new Participant;
            } else {
                $new_household = new Voter;
            }

            foreach ($this->fields as $field) {
                $new_household->$field = $first->$field;
            }

            $all_voter_ids = $constituents_in_house->pluck('voter_id')->toArray();
            $new_household->all_voter_ids = implode(',', $all_voter_ids);
            $new_household->all_residents = ucwords(strtolower($all_residents_str));
            $new_household->full_name = ucwords(strtolower($family));
            $new_household->full_address = ucwords(strtolower($household));

            $constituents->push($new_household);

        }

        foreach ($constituents as $constituent) {
            foreach ($this->fields as $field) {
                $sub[$field] = $constituent->$field;
                $sub[$field] = str_replace(
                                ' '.ucwords(strtolower(session('team_state'))).' ', 
                                ' '.strtoupper(session('team_state')).' ', 
                                $sub[$field]
                               );
            }

            $this->export[] = $sub;
        }

    }

    public function buildExportCollection()
    {
        $list = CampaignList::find($this->list_id);
        $list->cacheVoters(); // make sure latest accurate data
        updateParticipants(); // make sure all participants are accounted for

        $list->voters()->chunk(1000, function ($voters) {

            foreach ($voters as $voter) {

                if (isParticipant($voter)) {
                    $participant = getParticipant($voter);
                } else {
                    $participant = $voter; // copy of voter file
                }

                /*
                // ====================> OPTIONS ON MODAL
                      'name', 
                      'party',
                      'age',
                      'best_address',
                      'voter_file_name',
                      'voter_file_address',
                      'mailing_address',
                      'ward/precinct',
                */
                
                $row['voter_id'] =                  $voter->id;
                $row['household_id'] =              $participant->household_id;

                if (isset($this->fields['name'])) {
                    $row['first_name'] =                $participant->first_name;
                    $row['middle_name'] =               $participant->middle_name;
                    $row['last_name'] =                 $participant->last_name;
                    $row['full_name'] =                 $participant->full_name;
                }
                
                if (isset($this->fields['age/gender'])) {
                    if ($participant->age) {
                        $row['age'] =                   $participant->age;
                    } else {
                        $row['age'] =                   $voter->age;
                    }
                    $row['gender'] =                    $participant->gender; 
                }

                if (isset($this->fields['party'])) {
                    $row['party'] =                     $participant->party;
                }
                

                if (isset($this->fields['best_address'])) {
                    $row['best_address_full'] =         $participant->full_address;
                    $row['best_address_street'] =       $participant->address_line_street;
                    $row['best_address_city'] =         $participant->address_city;
                    $row['best_address_state'] =        $participant->address_state;
                    $row['best_address_zip'] =          $participant->address_zip;
                }
                
                if (isset($this->fields['voter_file_name'])) {
                    $row['voter_file_first_name'] =     $voter->first_name;
                    $row['voter_file_middle_name'] =    $voter->middle_name;
                    $row['voter_file_last_name'] =      $voter->last_name;
                }

                if (isset($this->fields['voter_file_address'])) {
                    $row['voter_file_address_full'] =   $voter->full_address;
                    $row['voter_file_address_street'] = $voter->address_line_street;
                    $row['voter_file_address_city'] =   $voter->address_city;
                    $row['voter_file_address_state'] =  $voter->address_state;
                    $row['voter_file_address_zip'] =    $voter->address_zip;
                }

                if (isset($this->fields['mailing_address'])) {
                    $row['mailing_address_full'] =      "";
                    $row['mailing_address_street'] =    "";
                    $row['mailing_address_city'] =      "";
                    $row['mailing_address_state'] =     "";
                    $row['mailing_address_zip'] =       "";
                
                    /*
                        {   
                            "address":"1 Atlantic Ter",
                            "address2":"",
                            "city":"Lynn",
                            "state":"MA",
                            "zip":"01902",
                            "zip4":""
                        }
                    */

                    if (isset($voter->mailing_info['address']) && $voter->mailing_info['address']) {
                        $full_mailing = $voter->mailing_info['address']." ";
                        if ($voter->mailing_info['address2']) {
                            $full_mailing .= $voter->mailing_info['address2']." ";
                        }
                        $full_mailing .= $voter->mailing_info['city']." ".
                                         $voter->mailing_info['state']." ".
                                         $voter->mailing_info['zip'];

                        $row['mailing_address_full'] =      trim($full_mailing);
                        $row['mailing_address_street'] =    trim($voter->mailing_info['address']." ".
                                                            $voter->mailing_info['address2']);
                        $row['mailing_address_city'] =      $voter->mailing_info['city'];
                        $row['mailing_address_state'] =     $voter->mailing_info['state'];
                        $row['mailing_address_zip'] =       $voter->mailing_info['zip'];
                    }
                }

                if (isset($this->fields['ward/precinct'])) {
                    $row['voter_file_ward'] =               $voter->ward;
                    $row['voter_file_precinct'] =           $voter->precinct;
                }

                foreach ($row as $field => $val) {

                    if ($field == 'voter_id' || 
                        $field == 'household_id' || 
                        $field == 'best_address_state' ||
                        $field == 'voter_file_address_state' ||
                        $field == 'mailing_address_state') {

                        $row[$field] = str_replace(
                                            session('team_state').'_', 
                                            '', 
                                            strtoupper($val)
                                       );

                    } else {
                        $row[$field] = ucwords(strtolower($val), " \t\r\n\f\v'-");
                        $row[$field] = str_replace(
                                    ' '.ucwords(strtolower(session('team_state'))).' ',
                                    ' '.strtoupper(session('team_state')).' ', 
                                    $row[$field]
                                       );
                    }
                }

                //$row['all_voter_ids'] = $voter->id; //Maintain State Prefix
                //dd($row);


                if ($this->include_support) {
                    $row['support'] = "";
                    $cp = $participant->campaignParticipant;
                    if ($cp) {
                        $row['support'] = $cp->support;
                    }
                }

                //------------------------------------------------------------------------
                
                if ($this->include_phones && !$this->phones_columns) {
                    $phones_str = "";
                    foreach ($participant->phones as $type => $number) {
                        $phones_str .= $number." ($type) ";
                    }
                    $row['phones'] = $phones_str;
                }

                if ($this->include_phones && $this->phones_columns) {

                    $phones = $participant->phones;
                    //dd($phones);
                    $other_phones = [];

                    foreach (['voter_file',
                              'primary', 
                              'cell',
                              'work',
                              'cf_plus_home', 
                              'cf_plus_work',
                              'other'] as $key) {

                        $row[$key] = null;

                        if (isset($phones[$key])) {
                            $row[$key] = $phones[$key];
                        } 

                    }

                    $row['other'] = null;
                    
                    if (is_array($participant->other_phones)) {

                        foreach($participant->other_phones as $pair) {
                            $other_phones[] = $pair[0].' ('.$pair[1].')';
                        }

                        $row['other'] = implode(', ', $other_phones);

                    }

                }

                //------------------------------------------------------------------------
                //dd($this->volunteers_columns);
                if ($this->include_volunteers && $this->volunteers_columns) {

                    if ($cp = $participant->campaignParticipant) {

                        foreach(Participant::getVolunteerColumns() as $col) {
                            $row[$col] = ($cp->$col) ? 'YES' : null;
                        }

                    } else {

                        foreach(Participant::getVolunteerColumns() as $col) {
                            $row[$col] = null;
                        }

                    }
                }
                //dd($row);

                if ($this->include_volunteers && !$this->volunteers_columns) {

                    $volstr = "";

                    if ($cp = $participant->campaignParticipant) {

                        foreach(Participant::getVolunteerColumns() as $col) {
                            
                            if ($cp->$col) {
                                $col = str_replace('volunteer_', '', $col);
                                $col = str_replace('_', ' ', $col);
                                $col = ucwords($col);
                                $volstr .= $col."; ";
                            }

                        }

                     }

                    $row['volunteers'] = trim($volstr);
                }

                //------------------------------------------------------------------------

                if ($this->include_emails) {
                    $emails_str = "";
                    foreach ($participant->emails as $type => $email) {
                        $emails_str .= $email." ";
                    }
                    $row['emails'] = $emails_str;
                }

                if ($this->include_tags) {
                    $tags_str = "";
                    if ($participant->tags()) {
                        foreach ($participant->tags as $tag) {
                            $tags_str .= $tag->name."; ";
                        }
                    }
                    $row['tags'] = $tags_str;
                }

                if ($this->include_lists) {
                    $lists_str = "";
                    foreach ($participant->listsTheyBelongTo() as $list) {
                        $lists_str .= $list->name."; ";
                    }
                    $row['lists'] = $lists_str;
                }

                $this->export[] = $row;
            }

        });

    }

    public function addVoterIDField()
    {
        array_unshift($this->fields, 'voter_id');
    }

    public function removeVoterIDFieldExceptForDevelopers()
    {
        if (Auth::user()->permissions->developer) return;

        foreach($this->export as $key => $row) {
            //unset($this->export[$key]['voter_id']);
            unset($this->export[$key]['household_id']);
        }
    }

    public function appendEmailsToExport()
    {
      foreach($this->export as $key => $row) {

            $voter_ids = explode(',', $row['all_voter_ids']);

            $emails = Participant::thisTeam()
                                 ->whereIn('voter_id', $voter_ids)
                                 ->whereNotNull('primary_email')
                                 ->pluck('primary_email')
                                 ->toArray();

            $this->export[$key]['emails'] = implode(',', $emails);

            unset($this->export[$key]['all_voter_ids']);  // Always Remove this
        }

    }

    public function appendSupportToExport()
    {
        $voter_prefix = session('team_state').'_';

        foreach($this->export as $key => $row) {

            $supporter = Participant::thisTeam()
                                    ->where('voter_id', $voter_prefix.$row['voter_id'])
                                    ->first();

            if ($supporter) {

                $cp = $supporter->campaignParticipant;

                if ($cp) {
                    $this->export[$key]['support'] = $cp->support;
                }

            }
        }

    }

    public function appendVolunteersToExport()
    {
        $voter_prefix = session('team_state').'_';

        foreach($this->export as $key => $row) {

            $supporter = Participant::thisTeam()
                                    ->where('voter_id', $voter_prefix.$row['voter_id'])
                                    ->first();

            if ($supporter) {

                $cp = $supporter->campaignParticipant;

                if ($cp) {

                    foreach(Participant::getVolunteerColumns() as $col) {
                        $this->export[$key][$col] = ($cp->$col) ? 'YES' : null;
                    }

                }

            }
        }

    }


    //----------------------------------------------------------------------------

    public function index()
    {
        $lists = CampaignList::where('team_id', Auth::user()->team->id)
                     ->orderBy('name')
                     ->get();
        $districts = District::all();

        return view('campaign.lists.index', compact('lists', 'districts'));
    }

    public function assign($id)
    {
        $list = CampaignList::find($id);
        $this->authorize('basic', $list);
        return view('campaign.lists.assign', compact('list'));
    }

    public function show($id)
    {
        $list = CampaignList::find($id);

        //dd($list);
        $this->authorize('basic', $list);
        return view('campaign.lists.lw', compact('list'));
    }

    public function showForGuests($id)
    {
        $list = CampaignList::find($id);
        $this->authorize('basic', $list);
        $this->authorize('hasBeenAssignedTo', $list);
        return view('campaign.lists.phonebank.lw', compact('list'));
    }

    public function edit($id)
    {
        $list = CampaignList::find($id);
        $this->authorize('basic', $list);
        $edit = true;

        return view('campaign.lists.edit', compact('list', 'edit'));
    }

    public function delete($id)
    {
        $list = CampaignList::find($id);
        $this->authorize('basic', $list);
        $list->delete();

        return redirect(Auth::user()->team->app_type.'/lists');
    }

    public function update(Request $request, $id, $close = null)
    {
    }

    public function new(Request $request)
    {
        // $house_districts = $this->getHouseDistricts();
        // $senate_districts = $this->getSenateDistricts();
        // $congressional_districts = $this->getCongressionalDistricts();

        return view('campaign.lists.new');
    }

    public function store(Request $request)
    {
        // $districts = District::all();
        // return view('campaign.lists.new', compact('districts'));
    }

    public function print(Request $request, $id)
    {
        $list = CampaignList::find($id);
        $households = $list->voters()
                           ->get()
                           ->sortBy('household_id')
                           ->groupBy('household_id');
        //dd($households);
        return view('campaign.lists.printable', compact('list', 'households'));
    }

    public function map($id)
    {
        $list = CampaignList::find($id);

        $max = 1000;
        if (request('max')) {
            $max = request('max');
        }

        $participants = getParticipants();

        $voters = $list->voters()->take($max)->get();

        $activity = collect([]);
        $activity['households'] = collect([]);
        foreach ($voters as $voter) {
            if ($voter->address_lat < 38 || $voter->address_lat > 44) {
                continue;
            }
            $household = [];
            $household['name'] = $voter->name;
            $household['address'] = $voter->full_address;
            $household['url'] = '/campaign/participants/'.$voter->id.'/edit';
            $household['phone'] = $voter->primary_phone;

            $household['lat'] = $voter->address_lat;
            $household['lng'] = $voter->address_long;

            if (isset($participants[$voter->id])) {
                $participant = getParticipant($voter);
                if (! $participant->support()) {
                    $household['color'] = 'blue';
                } elseif ($participant->support() == 1) {
                    $household['color'] = 'green';
                } elseif ($participant->support() == 2) {
                    $household['color'] = 'yellow';
                } elseif ($participant->support() == 3) {
                    $household['color'] = 'orange';
                } elseif ($participant->support() > 3) {
                    $household['color'] = 'red';
                }
            } else {
                $household['color'] = 'blue';
            }

            $activity['households'][] = $household;
        }

        $bounds = [];
        $bounds['max_lat'] = $activity['households']->max('lat');
        $bounds['min_lat'] = $activity['households']->min('lat');
        $bounds['max_lng'] = $activity['households']->max('lng');
        $bounds['min_lng'] = $activity['households']->min('lng');
        $activity['bounds'] = $bounds;

        return $activity->toJson();
    }
}
