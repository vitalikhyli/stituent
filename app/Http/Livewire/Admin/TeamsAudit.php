<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

use App\Team;
use App\User;
use App\Account;
use App\Permission;
use App\Category;

use Schema;
// use Log;


class TeamsAudit extends Component
{
	public $search;
	public $sort_date 			= true;
	public $sort_problems 		= false;
	public $exclude_inactive 	= true;
	public $app_types = [];
	public $app_type;

	public $tooltipGroups;
	public $tooltipAdmins;
	public $insert_admins = [];

	public $query_time;

	public $page = 1;
	public $skip;
	public $take = 1000;

	public function updatedPage()
	{
		$this->skip = ($this->page - 1) * $this->take;
	}

	public function setAdmins($team_id)
	{
		foreach($this->insert_admins as $user_id => $admin_or_not) {

			$user = User::find($user_id);
			$team = Team::find($team_id);

			if (!$user || !$team) continue;									//Safeguard 1
			if (!$user->memberOfTeam($team)) continue; 						//Safeguard 2
			if (!$user->permissions || $user->permissions->guest) continue;	//Safeguard 3

			$permission = Permission::where('team_id', $team->id)
									->where('user_id', $user->id)
									->first();

			if ($permission) {

				$permission->admin = $admin_or_not;
				$permission->save();

			}

		}

		$this->setTooltip('Admins', null);

	}

	public function setTooltip($type = null, $team_id = null)
	{
		// Type could be null if desire is to clear all tooltips

		if ($type) {

			$this->{'tooltip'.$type} = ($team_id == $this->{'tooltip'.$type} || !$team_id) ? null : $team_id;

			// Null other types of tooltips

		}

		foreach(collect(['Groups', 'Admins'])->flip()->except($type)->flip() as $other) {
			$this->{'tooltip'.$other} = null;
		}

		if ($team_id) {

			// Put the existing admins into the array

			$this->insert_admins = Permission::where('team_id', $team_id)
											 ->where('admin', true)
											 ->pluck('user_id')
											 ->flip()
											 ->toArray();

			// Format so matches the array style: user_id => true

			foreach($this->insert_admins as $id => $former_flipped_key) {
				$this->insert_admins[$id] = true;
			}

		} else {

			// No team selected, so empty the array

			$this->insert_admins = [];

		}
	}

	public function createPresetGroupsForTeam($team_id)
	{
		$presets = [
					 'Constituent Groups' 	=> ['has_position' => false, 	'has_title' => true],
					 'Issue Groups'  		=> ['has_position' => true, 	'has_title' => false],
					 'Legislation'  		=> ['has_position' => true, 	'has_title' => false]
				   ];

		foreach ($presets as $name => $options) {

			if (Category::where('team_id', $team_id)
						->where('name', $name)
						->where('can_edit', false)
						->doesntExist()) {

				$cat = new Category;
				$cat->team_id 		= $team_id;
				$cat->name 			= $name;
				$cat->can_edit 		= false;
				$cat->has_notes 	= true;
				$cat->has_title 	= $options['has_title'];
				$cat->has_position 	= $options['has_position'];
				$cat->save();

			}
		}

		$this->tooltip = null;
	}

	public function updatedSearch()
	{
		$this->setTooltip(null, null);
		$this->insert_admins = [];
		$this->page = 1;
		$this->skip = 0;
	}

	public function mount()
	{
		$this->app_types = Team::all()->pluck('app_type')->unique()->sort()->toArray();
	}

    public function render()
    {

    	$start = microtime(true);

		$teams = Team::select('teams.*')->leftJoin('accounts', 'teams.account_id', 'accounts.id');

    	if ($this->search) {
    		$teams = $teams->where(function ($q) {
    							$q->orWhere('teams.name', 'like', '%'.$this->search.'%');
    							$q->orWhere('accounts.name', 'like', '%'.$this->search.'%');
    						});
    	}

		if ($this->exclude_inactive) {
			$teams = $teams->where('teams.active', true)->where('accounts.active', true);
		}

		if ($this->app_type) {
			$teams = $teams->where('app_type', $this->app_type);
		}

		$teams = $teams->orderBy('name')->skip($this->skip)->take($this->take)->get();

    	$teams = $teams->each(function ($item) {

    		$item['has_slice'] 			= ($item->db_slice) ? true : false;

    		$item['table_exists'] 		= (Schema::hasTable($item->db_slice)) ? true : false;

    		$admins = Permission::where('team_id', $item->id)
    							->where('admin', true)
    							->get();
    		$item['has_admin'] 			= ($admins->first()) ? true : false;

    		$item['has_billygoat_id'] = ($item->account && $item->account->billygoat_id) ? true : false;

    		$cats = Category::where('team_id', $item->id)
    						->where('can_edit', false)
    						->whereIn('name', ['Constituent Groups', 'Issue Groups', 'Legislation'])
    						->count();
			$item['has_group_presets'] 	= ($cats == 3) ? true : false;

    	})->each(function ($item) {

    		if ($item->app_type == 'office') {

				$all = [$item->has_slice, 
						$item->table_exists, 
						$item->has_admin,
						$item->has_billygoat_id,
						$item->has_group_presets];

			} else {

				$all = [$item->has_slice, 
						$item->table_exists, 
						$item->has_admin,
						$item->has_billygoat_id];

			}

    		$item['problems'] = count(
							 	array_filter($all, function ($x) { return (!$x) ? true : false; })
							 );		

    	})->each(function ($item) {
    		$item['fail'] = ($item->problems > 0) ? true : false;
    	});

    	if ($this->sort_date) $teams = $teams->sortByDesc('created_at');
    	if ($this->sort_problems) $teams = $teams->sortByDesc('problems');

		$this->query_time = microtime(true) - $start;

        return view('livewire.admin.teams-audit', ['teams' => $teams]);
    }
}
