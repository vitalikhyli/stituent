<?php

namespace App\Http\Livewire;

use App\CampaignParticipant;
use App\Participant;
use App\ParticipantTag;
use App\Tag;
use App\Action;
use App\Voter;
use App\CFPlus;
use Auth;
use Carbon\Carbon;
use Livewire\Component;

class ParticipantDetails extends Component
{
    public $tag_with_id;
    public $tag_with;

    public $voter;
    public $iteration;
    public $edit;
    public $notes;
    public $cp;
    public $participant_phone;
    public $participant_email;

    public $cf_plus_phones;
    public $cf_plus_cell;

    protected $listeners = ['action_added' => '$refresh'];

    public function mount($voter_or_participant, $iteration, $edit, $tag_with_id)
    {     

        if (!$this->tag_with_id) $this->tag_with_id = $tag_with_id;

        if (isParticipant($voter_or_participant)) {
            $voter_id = $voter_or_participant->voter_id;
            //dd($voter_id);
            $this->voter = Voter::find($voter_id);
            $this->participant = $voter_or_participant;
            if (! $this->voter) {
                $this->voter = Participant::find($voter_or_participant->id);
            }
        } else {
            $voter_id = $voter_or_participant->id;
            $this->voter = $voter_or_participant;
        }

        $cf_plus = CFPlus::where('voter_id', $voter_id)->first();
        if ($cf_plus) {
            //dd($cf_plus);
            $this->cf_plus_cell = $cf_plus->cell_phone;
            $this->cf_plus_phones = $cf_plus->home_phone;
        }

        //dd($voter);
        $this->iteration = $iteration;
        $this->edit = $edit;
        $this->notes = '';
        $this->participant_phone = null;
        $this->participant_email = null;
        $this->cp = null;
        if (isParticipant($this->voter)) {

            $this->participant = getParticipant($this->voter);
            

            $this->participant_phone = $this->participant->phone;

            $this->participant_email = $this->participant->primary_email;

            $cp = CampaignParticipant::where('campaign_id', CurrentCampaign()->id)
                                    ->where('participant_id', $this->participant->id)
                                    ->first();
            if ($cp) {
                $this->notes = $cp->notes;
                $this->cp = $cp;
            }

        }

        
    }

    public function what()
    {
        dd(Auth::user()->team->id, Auth::user()->current_team_id);
    }

    public function toggleTag($tag_id, $voter_id)
    {
        $participant = findParticipantOrImportVoter($voter_id, Auth::user()->team_id);

        $tag = Tag::find($tag_id);

        // Need this because fails if voter not in VoterMaster (should never be the case)
        if (! $participant) {
            return;
        }

        if ($participant->voter->hasTag($tag->id)) {
            // dd('tagged', $tag_id, $participant);
            $participant->tags()->detach([$tag->id]);
        } else {
            // dd('not tagged', $tag_id, $participant);

            $pt = new ParticipantTag;
            $pt->tag_id = $tag->id;
            $pt->team_id = Auth::user()->team_id;
            $pt->user_id = Auth::user()->id;
            $pt->voter_id = $participant->voter_id;
            $pt->participant_id = $participant->id;
            $pt->save();
        }
    }

    public function toggleEdit()
    {
        $this->edit = ! $this->edit;
    }

    public function leftMessage()
    {
        $this->notes = trim($this->notes.' Left message '.Carbon::now()->format('n/j/y g:ia').' -'.Auth::user()->short_name);
    }

    

    public function render()
    {

        if ($this->cp) {
            if ($this->cp->notes != $this->notes) {
                $this->cp->notes = $this->notes;
                $this->cp->save();
            }
        } else {
            if ($this->notes) {
                $cp = $this->createCampaignParticipant();
                $cp->notes = $this->notes;
                $cp->save();
                $this->cp = $cp;
            }
        }
        if (isParticipant($this->voter)) {
            $participant = getParticipant($this->voter);
            if ($this->participant_email != $participant->primary_email) {
                $participant->primary_email = $this->participant_email;
                $participant->save();
            }
            if ($this->participant_phone != $participant->phone) {
                $participant->primary_phone = $this->participant_phone;
                $participant->save();
            }
        } else {
            if ($this->participant_phone) {
                $participant = findParticipantOrImportVoter($this->voter->id, Auth::user()->team->id);
                $participant->primary_phone = $this->participant_phone;
                $participant->save();
            }
            if ($this->participant_email) {
                $participant = findParticipantOrImportVoter($this->voter->id, Auth::user()->team->id);
                $participant->primary_email = $this->participant_email;
                $participant->save();
            }
        }

        $this->tag_with = ($this->tag_with_id) ? Tag::find($this->tag_with_id) : null;
        //dd("Laz");
        return view('livewire.participant-details');
    }

    public function createCampaignParticipant()
    {
        $campaign = CurrentCampaign();
        $participant = findParticipantOrImportVoter($this->voter->id, Auth::user()->team->id);

        // Need this because fails if voter not in VoterMaster (should never be the case)
        if (! $participant) {
            return;
        }

        $cp = new CampaignParticipant;
        $cp->team_id = Auth::user()->team->id;
        $cp->user_id = Auth::user()->id;
        $cp->campaign_id = $campaign->id;
        $cp->participant_id = $participant->id;
        $cp->voter_id = $participant->voter_id;

        return $cp;
    }

    public function setSupport($level)
    {
        if (! $this->cp) {
            $cp = $this->createCampaignParticipant();

            if (! $cp) {
                return;
            }

            $cp->save();
            $this->cp = $cp;
        }

        // Click same level again to null the level
        if ($this->cp->support == $level) {
            $this->cp->support = null;
        } else {
            $this->cp->support = $level;
        }

        $this->cp->save();

        updateParticipants();
    }

    public function clearSupport()
    {
        if (! $this->cp) {
            return;
        }

        $this->cp->support = null;
        $this->cp->save();
        updateParticipants();
    }
}