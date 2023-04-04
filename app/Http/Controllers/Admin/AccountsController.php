<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Team;
use App\TeamUser;
use App\User;
use App\VoterSlice;

use Auth;
use Illuminate\Http\Request;
use Schema;

class AccountsController extends Controller
{

    public function setUp()
    {
        return view('admin.accounts.setup');
    }

    public function addPermissions($user_id)
    {
        $theuser = User::find($user_id);
        if (null == ($theuser->permissions)) {
            $permission = new Permission;
            $permission->user_id        = $theuser->id;
            $permission->team_id        = $theuser->team_id;
            $permission->chat           = true;
            $permission->reports        = true;
            $permission->constituents   = true;
            // $permission->createconstituents   = true;
            $permission->metrics        = true;
            $permission->creategroups   = true;
            $permission->save();
        }

        return redirect()->back();
    }

    public function linkBillyGoatAccount(Request $request, $account_id)
    {
        $account = Account::find($account_id);
        $account->billygoat_id = request('billygoat_id');
        $account->save();

        return back();
    }

    public function users($account_id)
    {
        $theaccount = Account::find($account_id);
        $teams = $theaccount->teams;

        return view('admin.accounts.users', compact('theaccount', 'teams'));
    }

    public function audit()
    {
        return view('admin.accounts.audit');
    }

    public function new()
    {
        return view('admin.accounts.new');
    }

    public function save(Request $request)
    {
        $theaccount = new Account;
        $theaccount->name = request('name');
        $theaccount->save();

        return view('admin.accounts.edit', compact('theaccount'));
    }

    public function editTeam($account_id, $user_id)
    {
        $team = Team::find($user_id);
        $theaccount = Account::find($account_id);

        $slice = VoterSlice::where('name', $team->db_slice)->first();

        $available_slices = VoterSlice::orderBy('name')->get();
        $available_district_types = Team::whereNotNull('district_type')->pluck('district_type')->unique();

        return view('admin.accounts.edit-team', compact('team', 'theaccount', 'slice', 'available_slices', 'available_district_types'));
    }

    public function updateTeam(Request $request, $account_id, $team_id, $close = null)
    {
        $team = Team::find($team_id);

        if (request('remove_developers_from_team')) {
            foreach (request('remove_developers_from_team') as $user_id) {
                if (request('add_developers_to_team')) {
                    if (in_array($user_id, request('add_developers_to_team'))) {
                        continue;
                    }
                }

                $user = User::find($user_id);
                $pivot_a = TeamUser::where('user_id', $user->id)->where('team_id', $team->id)->delete();
                $pivot_b = Permission::where('user_id', $user->id)->where('team_id', $team->id)->delete();
            }
        }

        if (request('add_developers_to_team')) {
            foreach (request('add_developers_to_team') as $user_id) {
                $user = User::find($user_id);
                $pivot = TeamUser::where('user_id', $user->id)->where('team_id', $team->id)->first();
                if (! $pivot) {
                    $pivot = new TeamUser;
                    $pivot->user_id = $user->id;
                    $pivot->team_id = $team->id;
                    $pivot->save();
                }
                $pivot = Permission::where('user_id', $user->id)->where('team_id', $team->id)->first();
                if (! $pivot) {
                    $pivot = new Permission;
                    $pivot->user_id = $user->id;
                    $pivot->team_id = $team->id;
                    $pivot->save();
                }
            }
        }

        $team->name = request('name');
        $team->short_name = request('short_name');
        $team->district_type = request('district_type');
        $team->district_id = request('district_id');
        $team->db_slice = request('db_slice');
        $team->data_folder_id = request('state');

        if (request('new_app_type')) {
            $team->app_type = request('new_app_type');
        } else {
            $team->app_type = request('app_type');
        }

        $team->pilot = (request('pilot')) ? true : false;

        $team->save();

        if ($close) {
            return redirect('/admin/accounts/'.$account_id.'/edit');
        } else {
            return redirect('/admin/accounts/'.$account_id.'/teams/'.$team_id.'/edit');
        }
    }

    public function editUser($account_id, $user_id)
    {
        $user = User::find($user_id);
        $theaccount = Account::find($account_id);
        $member_of_teams = TeamUser::where('user_id', $user->id)->pluck('team_id')->toArray();

        return view('admin.accounts.edit-user', compact('user', 'theaccount', 'member_of_teams'));
    }

    public function updateUser(Request $request, $account_id, $user_id, $close = null)
    {
        $user = User::find($user_id);

        $user->name = request('name');
        
        $user->email = request('email');

        if (request('new_login_token')) {
            $user->login_token = $user->generateLoginToken();
            $user->change_password = true;
        }

        foreach(collect(request()->input())->keys() as $input) {
            if (stripos($input, 'set-current_') !== false) {
                $new_current_team_id = substr($input, 12);
                $user->current_team_id = $new_current_team_id;
            }
        }

        $user->save();

        // $permissions = $user->permissions;
        // $permissions->developer         = request('permissions_developer') ? true : false;
        // $permissions->admin             = request('permissions_admin') ? true : false;
        // $permissions->campaign          = request('permissions_campaign') ? true : false;
        // $permissions->chat_external     = request('permissions_chat_external') ? true : false;
        // $permissions->chat              = request('permissions_chat') ? true : false;
        // $permissions->reports           = request('permissions_reports') ? true : false;
        // $permissions->metrics           = request('permissions_metrics') ? true : false;
        // $permissions->constituents      = request('permissions_constituents') ? true : false;
        // $permissions->creategroups      = request('permissions_creategroups') ? true : false;

        // $permissions->save();

        $team_pivots_proposed = request('teams');
        $team_pivots_existing = TeamUser::where('user_id', $user_id)
                                        ->pluck('team_id')
                                        ->toArray();

        if (! $team_pivots_proposed) {
            $team_pivots_to_add = [];
            $team_pivots_to_remove = $team_pivots_existing;
        } else {
            $team_pivots_to_add = array_diff($team_pivots_proposed, $team_pivots_existing);
            $team_pivots_to_remove = array_diff($team_pivots_existing, $team_pivots_proposed);
        }

        foreach ($team_pivots_to_add as $team_id) {
            $pivot_team = new TeamUser;
            $pivot_team->team_id = $team_id;
            $pivot_team->user_id = $user_id;
            $pivot_team->save();

            $pivot_permission = new Permission;
            $pivot_permission->team_id              = $team_id;
            $pivot_permission->user_id              = $user_id;
            $pivot_permission->chat_external        = true;
            $pivot_permission->chat                 = true;
            $pivot_permission->reports              = true;
            $pivot_permission->metrics              = true;
            $pivot_permission->constituents         = true;
            $pivot_permission->creategroups         = true;
            $pivot_permission->save();
        }

        foreach ($team_pivots_to_remove as $team_id) {
            $pivot_team = TeamUser::where('team_id', $team_id)
                                  ->where('user_id', $user_id)
                                  ->delete();

            $pivot_permission = Permission::where('team_id', $team_id)
                                          ->where('user_id', $user_id)
                                          ->delete();
        }

        // "permission_226_chat" => "136"

        foreach (request()->input() as $name => $value) {
            if (substr($name, 0, 11) == 'permission_') {
                $c = explode('_', $name, 3); // Limit 3 because of "chat_external" underscore
                $id = $c[1];
                $permission_to_modify = Permission::find($id);
                if ($permission_to_modify) {
                    $field = $c[2];
                    $permission_to_modify->$field = ($value == 1) ? true : false;
                    $permission_to_modify->save();
                }
            }
        }

        if ($close) {
            return redirect('/admin/accounts/'.$account_id.'/edit');
        } else {
            return redirect('/admin/accounts/'.$account_id.'/users/'.$user_id.'/edit');
        }
    }

    public function edit($account_id)
    {
        $theaccount = Account::find($account_id);

        return view('admin.accounts.edit', compact('theaccount'));
    }

    public function update(Request $request, $account_id, $close = null)
    {
        $theaccount = Account::find($account_id);
        $theaccount->name = request('name');
        $theaccount->contact_name = request('contact_name');
        $theaccount->email = request('email');
        $theaccount->phone = request('phone');
        $theaccount->address = request('address');
        $theaccount->city = request('city');
        $theaccount->state = request('state');
        $theaccount->zip = request('zip');
        $theaccount->save();

        if (request('team_new_name')) {
            $team = new Team;
            $team->name = request('team_new_name');

            if (request('team_new_app_type')) {
                $team->app_type = request('team_new_app_type');
            } else {
                $team->app_type = request('team_existing_app_type');
            }

            $team->data_folder_id = request('team_new_state');
            $team->db_slice = request('team_new_slice');
            $team->account_id = $theaccount->id;
            $team->save();
        }

        if (request('user_new_name') && request('user_new_email')) {
            $user = new User;
            $user->name = request('user_new_name');
            $user->email = request('user_new_email');
            $user->current_team_id = request('user_new_team');
            $user->login_token = $user->generateLoginToken();
            $user->change_password = true;
            $user->active = true;
            $user->save();

            $pivot = new TeamUser;
            $pivot->user_id = $user->id;
            $pivot->team_id = $user->current_team_id;
            $pivot->save();

            $permissions = new Permission;
            $permissions->user_id = $user->id;
            $permissions->team_id = $user->current_team_id;
            $permissions->developer = request('permissions_developer') ? true : false;
            $permissions->admin = request('permissions_admin') ? true : false;
            $permissions->campaign = request('permissions_campaign') ? true : false;
            $permissions->chat_external = request('permissions_chat_external') ? true : false;
            $permissions->chat = request('permissions_chat') ? true : false;
            $permissions->reports = request('permissions_reports') ? true : false;
            $permissions->metrics = request('permissions_metrics') ? true : false;
            $permissions->constituents = request('permissions_constituents') ? true : false;
            $permissions->createconstituents = request('permissions_createconstituents') ? true : false;
            $permissions->creategroups = request('permissions_creategroups') ? true : false;

            $permissions->save();
        }

        if ($close) {
            return redirect('/admin/accounts');
        } else {
            return view('admin.accounts.edit', compact('theaccount'));
        }
    }

    public function index()
    {
        if (isset($_GET['state']) && $_GET['state'] != '') {
            $state_teams = Team::where('data_folder_id', $_GET['state'])
                                ->get()
                                ->pluck('account_id');
            $accounts = Account::whereIn('id', $state_teams)
                               ->orWhere('state', $_GET['state'])
                               ->orderBy('name')->get();
        } else {
            $accounts = Account::orderBy('name')->get();
        }

        $me_id = Auth::user()->team->account->id;

        $accounts = $accounts->each(function ($item) use ($me_id) {
            $item['me'] = ($item['id'] == $me_id) ? true : false;
        })
        ->sortByDesc('me');

        $state_options = Account::where('state', '!=', '')
                                ->whereNotNull('state')
                                ->get()
                                ->pluck('state')
                                ->map(function ($item) { return strtoupper($item); })
                                ->unique()
                                ->sortBy('state');

        return view('admin.accounts.main', compact('accounts', 'state_options'));
    }

    public function billyGoatIndex()
    {
        $accounts = Account::orderBy('name')->get();

        if (config('app.env') == 'local') {
            $domain = config('app.billygoat_local');
        } else {
            $domain = config('app.billygoat_url');
        }

        $url = $domain.'/api/'.config('app.billygoat_api_key').'/getallclients';

        $bg_accounts_api = @file_get_contents($url);

        if (! $bg_accounts_api) {
            $bg_accounts = false;
        } else {
            $bg_accounts_api = json_decode($bg_accounts_api);

            $bg_accounts = collect([]);

            foreach ($bg_accounts_api as $bg_account) {
                $cf_account = Account::where('billygoat_id', $bg_account->id);

                if ($cf_account->exists()) {
                    $bg_account = (object) array_merge((array) $bg_account, ['cf_id' => $cf_account->first()->id]);
                } else {
                    $bg_accounts->push($bg_account);
                }
            }
        }

        return view('admin.accounts.billygoat', compact('accounts', 'bg_accounts'));
    }

    public function createBillyGoatAccount($account_id)
    {
        $account = Account::find($account_id);

        if (config('app.env') == 'local') {
            $domain = config('app.billygoat_local');
        } else {
            $domain = config('app.billygoat_url');
        }

        $data = base64_encode($account->name)
        .';'.base64_encode($account->contact_name)
        .';'.base64_encode($account->address)
        .';'.base64_encode($account->city)
        .';'.base64_encode($account->state)
        .';'.base64_encode($account->zip)
        .';'.base64_encode($account->email)
        .';'.base64_encode($account->phone);

        $url = $domain.'/api/'.config('app.billygoat_api_key').'/createclient/'.$data;

        $response = @file_get_contents($url);

        if ($response === false) {
            return 'Error';
        } else {
            $newlycreated_billygoat_id = $response;
        }

        $account->billygoat_id = $newlycreated_billygoat_id;
        $account->save();

        session()->flash('msg', 'BillyGoat ID # '.$newlycreated_billygoat_id.' was created and linked.');

        return back();
    }
}
