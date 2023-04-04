<?php

namespace App\Http\Livewire\Lists;

use App\Tag;
use App\ParticipantTag;
use App\Participant;
use App\CampaignParticipant;
use App\Voter;
use App\CFPlus;

use Livewire\Component;
use Livewire\WithPagination;

use Auth;
use Carbon\Carbon;


class Phonebank extends Component
{
    use WithPagination;

    //////////////////////////////////////[ PROPERTIES ]//////////////////////////////////////////

    public $list;
    public $perpage;
    public $count;
    public $affected_count;

    public $data = [];  // All Voter Data to Sync

    protected $updatesQueryString = ['perpage', 'edit_mode', 'tag_with', 'page'];

    // public $page; // Cannot have same property as Livewire\WithPagination (already exists so ok?)


    //////////////////////////////////////[ FUNCTIONS ]//////////////////////////////////////////

    public function reserve($voter_id)
    {
        $reserved_by_others = $this->list->reservedByOthers($voter_id);

        if (!$reserved_by_others) {

            $assignment = $this->list->assignmentInstance(Auth::user());
            $assignment->reserved = $voter_id;
            $assignment->reserved_expires_at = Carbon::now()->addMinutes(10);
            $assignment->save();

        }

    }

    public function clearReserve()
    {
        $assignment = $this->list->assignmentInstance(Auth::user());
        $assignment->reserved = null;
        $assignment->reserved_expires_at = null;
        $assignment->save();
    }

    public function toggleEditMode()
    {
        $this->edit_mode = ! $this->edit_mode;
    }

    //////////////////////////////[ INDIVIDUAL VOTER FUNCTIONS ]//////////////////////////////

    public function updateCP($voter_id, $field)
    {
        // $cp = CampaignParticipant::find($this->data[$voter_id]['cp_id']);
        $participant = findParticipantOrImportVoter($voter_id, Auth::user()->team->id);
        $cp = $participant->campaignParticipant;
        if (!$cp) $cp = $this->createCampaignParticipant($voter_id);
        $cp->$field = $this->data[$voter_id][$field];
        $cp->save();
    }

    public function updateParticipant($voter_id, $field, $participant_field)
    {
        $participant = findParticipantOrImportVoter($voter_id, Auth::user()->team->id);
        $participant->$participant_field = $this->data[$voter_id][$field];
        $participant->save();
    }

    public function addNote($note, $voter_id)
    {
        $this->data[$voter_id]['notes'] = trim($this->data[$voter_id]['notes']
                                          ."\n".Carbon::now()->format('n/j/y g:ia').' - '
                                          .$note.' '
                                          .' -'.Auth::user()->short_name);
        $this->updateCP($voter_id, 'notes');
        updateParticipants();
    }

    public function leftMessage($voter_id)
    {
        $this->addNote('Left message', $voter_id);
    }

    public function notInService($voter_id)
    {
        $this->addNote('# Not in service', $voter_id);
    }


    public function wrongNumber($voter_id)
    {
        $this->addNote('Wrong #', $voter_id);
    }

    public function called($voter_id)
    {
        $this->addNote('Called', $voter_id);
    }

    public function createCampaignParticipant($voter_id)
    {
        $campaign = CurrentCampaign();
        $participant = findParticipantOrImportVoter($voter_id, Auth::user()->team->id);
        if (!$participant) return;
    
        $cp = new CampaignParticipant;
        $cp->team_id = Auth::user()->team->id;
        $cp->user_id = Auth::user()->id;
        $cp->campaign_id = $campaign->id;
        $cp->participant_id = $participant->id;
        $cp->voter_id = $participant->voter_id;

        return $cp;
    }

    public function setSupport($voter_id, $level)
    {
        $voter = $this->data[$voter_id];
        $cp = CampaignParticipant::find($voter['cp_id']);
        if (!$cp) $cp = $this->createCampaignParticipant($voter_id);
        $cp->support = ($cp->support == $level) ? null : $level;
        $cp->save();
        updateParticipants();
    }

	//////////////////////////////////////[ LIFE CYCLE ]//////////////////////////////////////////

    public function mount($list, $guest = null)
    {
        $this->list = $list;
        $this->perpage = 100;
        $this->count = $list->count();
        $this->clearReserve();
        $this->page = 1;
    }

    public function updatedData()
    {
        $voter_id = array_key_first($this->data);
        $this->updateCP($voter_id, 'notes');
        $this->updateParticipant($voter_id, 'participant_phone', 'primary_phone');
        $this->updateParticipant($voter_id, 'participant_email', 'primary_email');
        updateParticipants();
    }

    public function updatedPerPage()
    {
        if ($this->perpage > 500) $this->perpage = 500;
        if ($this->perpage < 1) $this->perpage = 1;
    }

    public function render()
    {

        $timer = microtime(true);

        // if ($this->perpage != 'all') $voters = $this->list->voters()->paginate($this->perpage);
        if ($this->perpage != 'all') $voters = $this->list->voters()->simplePaginate($this->perpage);
        if ($this->perpage == 'all') $voters = $this->list->voters()->get();


        $timer_a = microtime(true) - $timer;

        $timer = microtime(true);

        updateParticipants(); // Only do this once

        foreach($voters as $voter) {
            
            $voter['reserved_by_user'] = ($this->list->reservedByUser($voter->id)) ? true : false;

            $voter['reserved_expires_at'] = (!$voter['reserved_by_user']) ? null : $this->list->reservedExpiresAt($voter->id);

            $voter['reserved_by_others'] = ($this->list->reservedByOthers($voter->id))  ? true : false;

            if (isParticipant($voter)) {

                $participant = getParticipant($voter);

                $voter['is_participant']    = true;
                $voter['participant_id']    = $participant->id;
                $voter['support']           = $participant->support();

                $campaignParticipant = $participant->campaignParticipant;

                if ($campaignParticipant) {
                    $voter['cp_id']         = $campaignParticipant->id;
                    $voter['notes']         = $campaignParticipant->notes;
                    $voter['ago'] = Carbon::parse($campaignParticipant->updated_at)->diffForHumans();
                } else {
                    $voter['cp_id']         = null;
                    $voter['notes']         = null;
                    $voter['ago']           = null;
                }

            }

            if ($voter['reserved_by_user']) { // Create Editable Array for Active Voter

                $data = [];

                if (isParticipant($voter)) {

                    $participant = getParticipant($voter);

                    $data[$voter->id]['is_participant']    = true;
                    $data[$voter->id]['participant_id']    = $participant->id;
                    $data[$voter->id]['support']           = $participant->support();

                    if ($campaignParticipant) {
                        $data[$voter->id]['cp_id']         = $campaignParticipant->id;
                        $data[$voter->id]['notes']         = $campaignParticipant->notes;
                        $data[$voter->id]['ago'] = Carbon::parse($campaignParticipant->updated_at)->diffForHumans();
                    } else {
                        $data[$voter->id]['cp_id']         = null;
                        $data[$voter->id]['notes']         = null;
                        $data[$voter->id]['ago']           = null;
                    }

                    $data[$voter->id]['participant_phone'] = $participant->phone;
                    $data[$voter->id]['participant_email'] = $participant->primary_email;

                    $cf_plus = null;
                    $data[$voter->id]['cf_plus_phones']    = (!$cf_plus) ? [] : $cf_plus->phones;
                    $data[$voter->id]['cf_plus_cell']      = (!$cf_plus) ? [] : $cf_plus->cell_phones;

                } else {
                    
                    $data[$voter->id]['is_participant']    = false;
                    $data[$voter->id]['participant_id']    = null;
                    $data[$voter->id]['support']           = null;
                    $data[$voter->id]['cp_id']             = null;
                    $data[$voter->id]['notes']             = null;
                    $data[$voter->id]['ago']               = null;                    
                    $data[$voter->id]['participant_phone'] = null;
                    $data[$voter->id]['participant_email'] = null;
                    $data[$voter->id]['cf_plus_phones']    = [];
                    $data[$voter->id]['cf_plus_cell']      = [];
                }

                $this->data = $data;

            }
   
        }
        $timer = microtime(true) - $timer;

        // dd($_GET['page']);

        return view('livewire.lists.phonebank', compact('voters', 'timer', 'timer_a'));
    }

    public function paginationView()
    {
        return 'livewire.list-paginate-links';
    }

}
