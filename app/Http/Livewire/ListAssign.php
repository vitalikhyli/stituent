<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\User;
use App\Permission;
use App\CampaignListUser;
use App\Participant;
use App\TeamUser;
use App\CampaignList;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use \App\Traits\UniqueIDTrait;

use Auth;
use Carbon\Carbon;


class ListAssign extends Component
{
	use UniqueIDTrait;

	public $list;
    public $assignments_count;

	public $new_email;
    public $new_validated;
    public $display_errors = [];
    public $just_changed = [];

	public $lookup;
	public $showAll;
    public $showTeam;
    public $showAllVolunteers;
    public $showPhoneVolunteers;

    public $script;
    public $other_scripts;
    public $copy_script;
    public $script_to_copy;
    public $script_preview;
    public $edit_script;

    public $new_expiration;
    public $new_expiration_validated;

    public $email_mode = false;
    public $email_mode_progress = false;
    public $email_mode_progress_count;
    public $email_mode_progress_percentage;
    public $mail_data = [];
    public $mail_data_validated;
    public $from = [];
    public $edit_email;

    public $search;


	////////////////////////////////////////// FUNCTIONS ///////////////////////////////////////////

    public function resetExpiration()
    {
        if (!$this->new_expiration) return;

        try {

            $new = Carbon::parse($this->new_expiration)->toDateTimeString();

            foreach($this->list->assignments as $ass) {
                $ass->expires_at = $new;
                $ass->save();
            }

            $this->new_expiration = null;
            $this->new_expiration_validated = null;
            $this->just_changed['new_expiration'] = Carbon::now()->toDateTimeString();

        } catch (\Exception $e) {

            // Error

        }
    }

    public function copyScript()
    {
        $list = $this->list;
        $list->script = CampaignList::find($this->script_to_copy)->script;
        $list->save();
        $this->script = $list->script;
        $this->script_preview = null;
        $this->copy_script = false;
    }

    public function createVolunteerParticipant($primary_email, $full_name)
    {
        $words = explode(' ', $full_name);
        $first = array_shift($words);
        $last  = implode(' ', $words);

        $model = new Participant;
        $model->team_id       = Auth::user()->team->id;
        $model->user_id       = Auth::user()->id;
        $model->first_name    = $first;
        $model->last_name     = $last;
        $model->full_name     = $full_name;
        $model->primary_email = $primary_email;
        $model->save();

        $model->markAsVolunteer('phone_calls');

        return $model;
    }

	public function createGuest($email, $name)
	{
		$user = new User;
		$user->current_team_id 	= Auth::user()->team->id;
        $user->email 			= $email;
        $user->name 			= $name;
        $user->password 		= 'NONE_'.Hash::make(Str::random(15)); // Temporary
        $user->save();

        $permission = new Permission;
        $permission->guest      = true;
        $permission->team_id    = Auth::user()->team->id;
        $permission->user_id    = $user->id;
        $permission->save();

        $team_user = new TeamUser;
        $team_user->team_id     = Auth::user()->team->id;
        $team_user->user_id     = $user->id;
        $team_user->save();

		return $user;
	}

	public function createVolunteerAndLink()
	{
		$user = $this->createGuest($this->new_email, $this->lookup);
        $participant = $this->createVolunteerParticipant($this->new_email, $this->lookup);

		$assignment = new CampaignListUser;
		$assignment->team_id 	       = Auth::user()->team->id;
		$assignment->list_id 	       = $this->list->id;
		$assignment->user_id 	       = $user->id;
        $assignment->participant_id    = $participant->id;
		$assignment->uuid 		       = $this->createUniqueID();
        $assignment->expires_at        = Carbon::now()->addDays(7);
		$assignment->type 		       = 'list';
        $assignment->created_by        = Auth::user()->id;
		$assignment->save();

		$this->new_email        = null;
        $this->lookup         = null;
        $this->new_validated    = false;
	}

	public function linkVolunteer($id, $class)
	{
        if (Auth::user()->cannot('basic', $this->list)) abort(403); //CampaignList Policy
        if ($class == 'participant') {
            if (Auth::user()->cannot('basic', $class)) abort(403); //Participant Policy
        }

        // Bring back if removed
        $trashed = CampaignListUser::onlyTrashed()
                                   ->where('team_id', Auth::user()->team->id)
                                   ->where('list_id', $this->list->id)
                                   ->where(strtolower($class).'_id', $id)
                                   ->first();

        if ($trashed) {

            $trashed->restore();
            $trashed->user_id = null;   // Users were force deleted, must create new one
            $trashed->save();
            $assignment = $trashed;

        } else {

		  $assignment = new CampaignListUser;

        }

		$assignment->team_id 	= Auth::user()->team->id;
		$assignment->list_id 	= $this->list->id;

		if ($class == 'User') {

			$assignment->user_id = $id;

		}

		if ($class == 'Participant') {

			$assignment->participant_id = $id;
			$related_user = CampaignListUser::where('team_id', $this->list->team_id)
									 		->where('participant_id', $assignment->participant_id)
									 		->whereNotNull('user_id')
									 		->first();
			if (!$related_user) {
				$user = $this->createGuest($email = Participant::find($id)->primary_email,
                                           $name = Participant::find($id)->full_name);
			} else {
				$user = User::find($related_user->user_id);
			}

			$assignment->user_id = $user->id;

		}

		$assignment->uuid 		= $this->createUniqueID();
		$assignment->type 		= 'list';
        $assignment->expires_at = Carbon::now()->addDays(7);
        $assignment->created_by = Auth::user()->id;
		$assignment->save();

        $this->lookup = null;
	}

    public function unassign($id)
    {
        $assignment = CampaignListUser::find($id);
        if (Auth::user()->cannot('basic', $assignment->list)) abort(403);

        if ($assignment) {

            $other_assignments = CampaignListUser::where('user_id', $assignment->user_id)->count();

            if ($other_assignments == 1) { // There are no other lists this user belongs to

                $related_user = User::find($assignment->user_id);

                if ($related_user && $related_user->permissions->guest) { // Never delete full users

                    $related_user->forceDelete();

                    $permissions = $related_user->permissions;
                    $permissions->delete();

                    TeamUser::where('team_id', $related_user->id)
                            ->where('user_id', $assignment->team_id)
                            ->forceDelete();

                }

                // Ask about preserving related Participant

            }

            if ($assignment->participant_id)    $assignment->delete();
            if (!$assignment->participant_id)   $assignment->forceDelete();

        }

    }

    public function forceDeleteAssignment($id)
    {
        if (Auth::user()->cannot('basic', $this->list)) abort(403);
        $assignment = CampaignListUser::onlyTrashed()->where('id', $id)->forceDelete();
    }

    public function clearEmailed($assignment_id)
    {
        $ass = CampaignListUser::find($assignment_id);
        if ($ass) {
            $ass->emailed_at = null;
            $ass->save();
        }
    }

	////////////////////////////////////////// LIFE CYCLE //////////////////////////////////////////
    
    public function mount()
    {
        $this->showAll = false;
        $this->showPhoneVolunteers = true;
        $this->showAllVolunteers = true;
        $this->showTeam = true;
        $this->script = $this->list->script;

        $this->just_changed['script'] = null;
        $this->just_changed['new_expiration'] = null;

        $this->email_mode_progress_percentage = 0;

        if (!$this->list->mail_data) $this->list->mail_data = [];

        foreach(['subject', 'body1', 'body2', 'from_user_id'] as $key) {
            if (array_key_exists($key, $this->list->mail_data)) {
                $this->mail_data[$key] =  $this->list->mail_data[$key];
            } else {
                $this->mail_data[$key] = null;
            }
        }

        $this->from = [];
        $users = Auth::user()->team->users()->get();
        foreach ($users as $user) {
            if ($user->admin) {
                $one = [];
                $one['id']      = $user->id;
                $one['name']    = $user->name;
                $one['email']   = $user->email;
                $this->from[] = $one;
            }
        }

        $this->email_mode_progress_count = $this->list->assignments()
                                                      ->whereNull('deleted_at')
                                                      ->whereNotNull('emailed_at')
                                                      ->count();

        $this->assignments_count = $this->list->assignments()
                                              ->whereNull('deleted_at')
                                              ->count();

        if ($this->assignments_count > 0) {
            $this->email_mode_progress_percentage = round($this->email_mode_progress_count / $this->assignments_count * 100, 0);
        } else {
            $this->email_mode_progress_percentage = 0;
        }
        

    }

    public function updated()
    {
        $this->errors = []; 
        $this->display_errors = []; 

        if (!$this->lookup) {

            $this->errors[] = 'Name is blank.';

        }

        if (!$this->new_email) {

            $this->errors[] = 'Email is blank.';

        } elseif (!filter_var($this->new_email, FILTER_VALIDATE_EMAIL)) {

            $this->errors[] = $this->display_errors[] = 'Email is not valid.';

        } elseif (User::withTrashed()->where('email', $this->new_email)->first()) {

            $this->errors[] = $this->display_errors[] = 'This email is already in use. Try the lookup to the right.';

        }

        $this->new_validated = (empty($this->errors)) ? true : false;

        $list = CampaignList::find($this->list->id);

        if ($list->script != $this->script) {
            $this->just_changed['script'] = Carbon::now()->toDateTimeString();
        }

        if (!$this->email_mode_progress) $list->mail_data = $this->mail_data; // Do not save if sending
        $list->script = $this->script;
        $list->save();

        if (!$this->mail_data['from_user_id']) {
            
            $this->mail_data_validated = false;
            
        } elseif (!$this->mail_data['subject']) {

            $this->mail_data_validated = false;

        // }  elseif (!$this->mail_data['body1']) {

        //     $this->mail_data_validated = false;

        // } elseif (!$this->mail_data['body2']) {

        //     $this->mail_data_validated = false;

        } else {

            $this->mail_data_validated = true;

        }

        try {
            $new = Carbon::parse($this->new_expiration)->toDateTimeString();
            $this->new_expiration_validated = true;
        } catch (\Exception $e) {
            $this->new_expiration_validated = false;
        }
        if (!$this->new_expiration) $this->new_expiration_validated = false;
    }

    public function render()
    {
        $list = CampaignList::find($this->list->id);

        $this->other_scripts = CampaignList::thisTeam()
                                           ->where('id', '<>', $this->list->id)
                                           ->whereNotNull('script')
                                           ->orderBy('created_at', 'desc')
                                           ->get();

        foreach($this->just_changed as $field => $time) {
            if (Carbon::parse($time)->diffInSeconds() > 5) $this->just_changed[$field] = null;
        }


        ////////////////////////////////////////////////////////////////////////////
        //
        // EMAILER
        //

        $this->assignments_count = $this->list->assignments()
                                              ->whereNull('list_user.deleted_at')
                                              ->count();

        $this->email_mode_progress_count = $this->list->assignments()
                                                      ->whereNull('deleted_at')
                                                      ->whereNotNull('emailed_at')
                                                      ->count();

        if ($this->assignments_count > 0) {
            $this->email_mode_progress_percentage = round($this->email_mode_progress_count / $this->assignments_count * 100, 0);
        } else {
            $this->email_mode_progress_percentage = 0;
        }

        if($this->email_mode_progress) {


            $assignment = $this->list->assignments()->whereNull('emailed_at')->first();

            if ($assignment) {

                $assignment->sendLinkToVolunteer($this->mail_data);

            } else {

                $this->email_mode_progress = false;

            }

        }

    	////////////////////////////////////////////////////////////////////////////
        // 
        // LOOKUP BAR
        //

        $users          = collect([]);
        $participants   = collect([]);

        if ($this->showAllVolunteers || $this->showPhoneVolunteers) {

        	$there_already = $list->assignments()
                                  ->whereNotNull('participant_id')
                                  ->pluck('participant_id');	

            $participants_all = Participant::whereNotIn('id', $there_already)
            							   ->whereNotNull('primary_email');

            if ($this->lookup) {
            	$participants_all = $participants_all->where('full_name', 'like', '%'.$this->lookup.'%');
            }

            $participants_all = $participants_all->with('campaignParticipant')->get();

            $participants = collect([]);

            foreach ($participants_all as $p) {

            	if (!$p->campaignParticipant) continue;

                if ($this->showAllVolunteers && $p->volunteer) {

                    $participants[] = $p;

                } elseif ($this->showPhoneVolunteers 
                         && $p->campaignParticipant->volunteer_phone_calls) {

                    $participants[] = $p;
                    
                }

            }

            $participants = $participants->unique();


        }

        if ($this->showTeam) {

            $users_this_team    = TeamUser::where('team_id', $list->team_id)->pluck('user_id');

            $there_already      = $list->assignments()->whereNotNull('user_id')->pluck('user_id');

            $users = User::whereIn('id', $users_this_team)
                         ->whereNotIn('id', $there_already);

            if ($this->lookup) {
            	$users = $users->where('name', 'like', '%'.$this->lookup.'%');
            }

            $users = $users->get()->reject(function ($item) {
                                        if (!$item->permissions) return true;
                                        return $item->permissions->guest; // Not a Guest
                                    });

        }

        $existing = [];

        foreach($users as $user) {
            $existing[] = (object) ['id'     => $user->id,
                           'name'   => $user->name,
                           'email'  => $user->email,
                           'class'  => 'User'];
        }

        foreach($participants as $participant) {
            $existing[] = (object) ['id'     => $participant->id,
                           'name'   => $participant->full_name,
                           'email'  => $participant->primary_email,
                           'class'  => 'Participant'];
        }

        $existing = collect($existing)->sortBy('name');


        // Get Assignments

        $assignments = CampaignListUser::withTrashed()
                                      ->where('list_id',  $list->id)
                                      ->orderBy('created_at', 'desc')
                                      ->get();
        if ($this->search) {
            $assignments = $assignments->filter(function ($item) {
                if($item->participant) {
                    if (stripos($item->participant->full_name, $this->search) !== false) return true;
                    if (stripos($item->participant->primary_email, $this->search) !== false) return true;
                }
                if($item->user) {
                    if (stripos($item->user->name, $this->search) !== false) return true;
                    if (stripos($item->user->email, $this->search) !== false) return true;
                }
            });
        }

        $assignments = $assignments->each(function ($item) {
            if ($item->user) $item['alpha_name'] = $item->user->name;
            if ($item->participant) $item['alpha_name'] = $item->participant->last_name;
        })->sortBy('alpha_name');


        // RETURN VIEW

        return view('livewire.list-assign', [
        										'existing' => $existing,
        										'assignments' => $assignments,
        									]);
    }
}
