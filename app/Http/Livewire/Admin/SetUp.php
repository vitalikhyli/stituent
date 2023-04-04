<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

use App\Account;
use App\Team;
use App\Permission;
use App\TeamUser;
use App\User;
use App\Category;
use App\VoterSlice;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SetUp extends Component
{
	public $account_mode;
	public $new_account_name;
	public $new_account_state;
	public $account_id;

	public $team_mode;
	public $new_team_name;
	public $new_team_state;
	public $new_team_app_type;
	public $team_id;

	public $new_user_name;
	public $new_user_email;

	// public $voter_file_mode;
	public $db_slice;

	//==============================================================================//

	// public function voterFileModeSlice()
	// {
	// 	$this->voter_file_mode ='slice';
	// }

	// public function voterFileModeStandalone()
	// {
	// 	$this->voter_file_mode ='standalone';
	// }

	public function updatedDBSlice()
	{
		$team = Team::find($this->team_id);
		$team->db_slice = $this->db_slice;
		$team->save();
	}

	//==============================================================================//

	public function toggleAdmin($user_id)
	{

		$permission = Permission::where('user_id', $user_id)
								->where('team_id', $this->team_id)
								->first();

		if ($permission) {

			$permission->admin = ($permission->admin) ? false : true;
			$permission->save();

		}

	}

	public function toggleOnTeam($user_id)
	{

		$team_user = TeamUser::where('user_id', $user_id)
							 ->where('team_id', $this->team_id)
							 ->first();

		if (!$team_user) {

			$pivot = new TeamUser;
			$pivot->team_id = $this->team_id;
			$pivot->user_id = $user_id;
			$pivot->save();

			$pivot = new Permission;
			$pivot->team_id = $this->team_id;
			$pivot->user_id = $user_id;
			$pivot->save();

		} else {

			$on_other_account_teams = TeamUser::where('user_id', $user_id)
							 		  		  ->where('team_id', '!=', $this->team_id)
							 		  		  ->whereIn('team_id', Account::find($this->account_id)->teams->pluck('id'))
							 		  		  ->first();

			// Do not remove from last team (user would be orphaned)

			if ($on_other_account_teams) {

				$pivot = TeamUser::where('user_id', $user_id)
							     ->where('team_id', $this->team_id)
							     ->delete();

				$pivot = Permission::where('user_id', $user_id)
								   ->where('team_id', $this->team_id)
							   	   ->delete();
			}	

		}

	}


	public function createUser()
	{
		$user = new User;
		$user->name 			= $this->new_user_name;
		$user->email 			= $this->new_user_email;
		$user->password 		= 'NONE_'.Hash::make(Str::random(15)); // Temporary
		$user->current_team_id	= $this->team_id;
		$user->save();

		$pivot = new TeamUser;
		$pivot->team_id = $this->team_id;
		$pivot->user_id = $user->id;
		$pivot->save();

		$pivot = new Permission;
		$pivot->team_id = $this->team_id;
		$pivot->user_id = $user->id;
		$pivot->save();

		$this->new_user_name = null;
		$this->new_user_email = null;
	}

	//==============================================================================//

	public function addPresetCats()
	{
		$team = Team::find($this->team_id);
		foreach (['Constituent Groups', 'Issue Groups', 'Legislation'] as $cat_name) {

			$cat = Category::where('team_id', $team->id)
						   ->where('can_edit', false)
						   ->where('name', $cat_name)
						   ->first();

			if (!$cat) {

				$cat = new Category;
				$cat->team_id 	= $team->id;
				$cat->name 		= $cat_name;
				$cat->can_edit 	= false;
				$cat->save();

			}

		}
	}

	public function createTeam()
	{
		$team = new Team;
		$team->account_id 		= $this->account_id;
		$team->name 			= $this->new_team_name;
		$team->data_folder_id 	= $this->new_team_state;
		$team->app_type 		= $this->new_team_app_type;
		$team->save();

		$this->team_id = $team->id;
		$this->team_mode = 'existing';
	}


	public function teamModeExisting()
	{
		$this->team_mode = 'existing';
		$this->new_team_name = null;
		$this->new_team_state = null;
		$this->team_id = null;
	}

	public function teamModeNew()
	{
		$this->team_mode = 'new';
		$this->new_team_name = null;

		$account = Account::find($this->account_id);
		$this->new_team_state = $account->state;

		$this->team_id = null;
	}

	public function updatedTeamID()
	{
		$team = Team::find($this->team_id);
		$this->db_slice = $team->db_slice;
	}

	//==============================================================================//

	public function createAccount()
	{
		$account = new Account;
		$account->name 		= $this->new_account_name;
		$account->state 	= $this->new_account_state;
		$account->save();

		$this->account_id = $account->id;
		$this->account_mode = 'existing';
	}

	public function accountModeExisting()
	{
		$this->account_mode = 'existing';
		$this->new_account_name = null;
		$this->new_account_state = null;
		$this->account_id = null;
		$this->team_id = null;
	}

	public function accountModeNew()
	{
		$this->account_mode = 'new';
		$this->new_account_name = null;
		$this->new_account_state = null;
		$this->account_id = null;
		$this->team_id = null;
	}

	public function updatedAccountID()
	{
		$this->team_id = null;
		$this->team_mode = null;
		$account = Account::find($this->account_id);
		if ($account) {
			$this->new_account_state = $account->state;
		}
	}

	//==============================================================================//

    public function render()
    {
    	$available_accounts = Account::orderBy('state')->orderBy('name')->get();
    	$available_app_types = Team::all()->pluck('app_type')->unique();
    	

    	if(!$this->account_id) {
    		$account = Account::find('this_id_does_not_exist');
    	} else {
    		$account = Account::find($this->account_id);
    	}

    	if(!$this->team_id) {
    		$team = Team::find('this_id_does_not_exist');
    		$available_slices = VoterSlice::orderBy('name')->get();
    	} else {
    		$team = Team::find($this->team_id);
    		$state = $team->data_folder_id;
    		$available_slices = VoterSlice::orderBy('name')
    									  ->get()
    									  ->reject(function ($item) use ($state) {
    									  	return (substr($item['name'], 2, 2) != $state);
    									  });
    	}

        return view('livewire.admin.set-up.set-up',
        								[
    									'available_accounts' 	=> $available_accounts,
    									'available_app_types' 	=> $available_app_types,
    									'available_slices' 		=> $available_slices,
    									'account' 				=> $account,
    									'team' 					=> $team,
        								]);
    }
}
