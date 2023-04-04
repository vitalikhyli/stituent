<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Team;
use App\TeamUser;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Log;
use Mail;
use Validator;

class UsersController extends Controller
{
    public function joinTeam($app_type, $user_id, $team_id)
    {
        if (! Auth::user()->permissions->admin && ! Auth::user()->permissions->developer) {
            return redirect()->back();
        }

        $pivot = TeamUser::where('user_id', $user_id)->where('team_id', $team_id)->first();

        if (! $pivot) {
            $pivot = new TeamUser;
            $pivot->user_id = $user_id;
            $pivot->team_id = $team_id;
            $pivot->save();

            $permission = new Permission;
            $permission->user_id = $user_id;
            $permission->team_id = $team_id;
            $permission->save();
        }

        return redirect()->back();
    }

    public function leaveTeam($app_type, $user_id, $team_id)
    {
        if (! Auth::user()->permissions->admin && ! Auth::user()->permissions->developer) {
            return redirect()->back();
        }

        TeamUser::where('user_id', $user_id)->where('team_id', $team_id)->delete();

        Permission::where('user_id', $user_id)->where('team_id', $team_id)->delete();

        return redirect()->back();
    }

    public function checkToken($token)
    {
        $user = User::where('login_token', $token)->first();
        if (! $user) {
            return redirect('/');
        } else {
            Auth::login($user);

            return redirect($user->team->app_type.'/users/'.$user->id.'/edit');
        }
    }

    public function addMemory($user_id, $key, $value)
    {
        $user = User::where('id', $user_id)->first();
        $user->addMemory($key, $value);
        $user->save();
    }

    public function changeTeam($team_id)
    {
        $user = Auth::User();
        if (!$user->allTeams->contains($team_id)) return;
        $user->current_team_id = $team_id;
        $user->save();

        return redirect('/'.Team::find($team_id)->app_type);
    }

    public function setTeamCol($col, $v)
    {
        $team = Auth::User()->team;
        $team->$col = $v;
        $team->save();
    }

    public function emailLink($app_type, $id)
    {
        Log::info('Emailing '.now());

        $id = base64_decode($id);

        $user = User::find($id);

        $info = [
            'recipient_email' 	=> $user->email,
            'recipient_name' 	=> $user->name,
            'from_email' 		=> Auth::user()->email,
            'from_name' 		=> Auth::user()->name,
            'subject' 			=> Auth::user()->team->name,
            'system_email' 		=> 'laz@communityfluency.com', //config('app.system_from_email'),
            'system_name' 		=> 'Community Fluency', //config('app.system_from_name'),
        ];

        $data = [
            'date'				=> Carbon::now()->format('m/d/Y'),
            'link'				=> config('app.url').'/link/'.$user->login_token,
            'to_name'			=> $user->name,
            'from_name'			=> Auth::user()->name,
            'account'			=> Auth::user()->team->name,
        ];

        $validator = Validator::make(array_merge($info, $data), [
            'recipient_email' 	=> ['required', 'email'],
            'recipient_name' 	=> ['required', 'max:50'],
            'from_email' 		=> ['required', 'email'],
            'from_name' 		=> ['required', 'max:50'],
            'subject' 			=> ['required', 'max:50'],
            'to_name' 			=> ['required', 'max:50'],
            'system_email' 		=> ['required', 'email'],
            'system_name' 		=> ['required', 'max:50'],
        ]);

        if ($validator->fails()) {
            return 'Error'; //.' -- Validation failed'.print_r($info);
        }

        $html_blade = 'emails.link';
        $plaintext_blade = 'emails.link_plain';

        Mail::send([$html_blade, $plaintext_blade], $data, function ($message) use ($info) {
            $message->from($info['system_email'], $info['system_name']);
            $message->replyTo($info['from_email'], $info['from_name']);
            $message->to($info['recipient_email'], $info['recipient_name']);
            $message->subject($info['subject']);
        });

        if (Mail::failures()) {
            return 'Error';
        } else {
            return 'Done!';
        }
    }

    public function settings($app_type)
    {
        return redirect('/'.Auth::user()->team->app_type.'/users/'.Auth::user()->id.'/edit');
    }

    public function new($app_type)
    {
        return view('shared-features.user.new');
    }

    public function save(Request $request, $app_type)
    {
        $validator = Validator::make($request->toArray(), [
            'email' 			=> ['required', 'unique:users,email', 'email'],
            'name' 				=> ['required', 'min:3', 'max:25'],
        ],
        $messages = [
            'email'    			=> 'Please correct your email address',
        ]);

        if ($validator->errors()->count() > 0) {
            return redirect('/'.Auth::user()->team->app_type.'/users/new')->withErrors($validator)->withInput();
        } else {
            $user = new User;
            $user->login_token = $user->generateLoginToken();
            $user->change_password = 1;
            $user->active = 1;
            $user->name = request('name');
            $user->email = request('email');
            $user->current_team_id = Auth::user()->team->id;
            $user->save();

            $pivot = new TeamUser;
            $pivot->user_id = $user->id;
            $pivot->team_id = Auth::user()->team->id;
            $pivot->save();

            $permissions = new Permission;
            $permissions->team_id = Auth::user()->team->id;
            $permissions->user_id = $user->id;
            $permissions->admin = 0;
            $permissions->save();

            return $this->edit($app_type, $user->id);
        }
    }

    public function edit($app_type, $id)
    {
        $user = User::find($id);

        return view('shared-features.user.edit', compact('user'));
    }

    public function update(Request $request, $app_type, $id, $close = null)
    {

        //////// Validate

        if (request('new_1')) {

            $validator = Validator::make($request->toArray(), [
                'new_1' 			=> ['required', 'min:6', 'max:30'],
                'new_2' 			=> ['required', 'min:6', 'max:30', 'same:new_1'],

                'email' 			=> ['required', 'unique:users,email,'.$id, 'email'],
                'name' 				=> ['required', 'min:3', 'max:25'],
            ],
            $messages = [
                'same'    			=> 'The passwords you entered don\'t match!',
                'new_1.required' 	=> 'First password in blank',
                'new_2.required' 	=> 'Confirmation password is blank',
                'min' 				=> 'Passwords must be at least 8 characters long',

                'email'    			=> 'Please correct your email address',

            ]);

        } else {

            $validator = Validator::make($request->toArray(), [
                'email' 			=> ['required'],
                'name' 				=> ['required', 'min:3', 'max:25'],
            ],
            $messages = [
                'email'    			=> 'Please correct your email address',
            ]);

        }

        if ($validator->errors()->count() > 0) {

            return redirect('/'.Auth::user()->team->app_type.'/users/'.$id.'/edit')->withErrors($validator)->withInput();

        }

    
        ////////// Update

        $user = User::find($id);

        $user->name                         = request('name');
        $user->email                        = request('email');
        $user->username                     = request('username');
        $user->language                     = request('language');

        if (request('active') !== null) { // Only if "active" appears on the form
            $user->active = request('active') ? true : false;
        }
        
        if (request('new_login_token')) {
            $user->login_token              = $user->generateLoginToken();
            $user->change_password          = true;
        }

        if (request('new_1')) {

            $user->password         = Hash::make(request('new_1'));
            $user->change_password  = false;
            $user->login_token      = null;
            $user->active           = true;

            session()->flash('msg', 'Your password has been changed.');

        } elseif ((!empty($user->password)) && (!request('new_login_token'))) {

            $user->change_password = (request('change_password')) ? true : false;

        }

        $user->save();

        if (!$user->change_password) {
            $user->login_token = null;
            $user->save();
        }

        $permissions = Permission::where('user_id', $user->id)
                                 ->where('team_id', Auth::user()->team->id)
                                 ->first();

        if ($permissions) {

            foreach(['developer',
                     'admin',
                     'chat',
                     'export',
                     'reports',
                     'metrics',
                     'constituents',
                     'creategroups',
                     'createconstituents'] as $which) {

                if (request($which.'_is_option')) {  // Hidden field to make sure exists on form

                    $permissions->$which = (request($which)) ? true : false;

                }
            }

            $permissions->title = request('title');
            $permissions->save();
        
        }

        //////// RETURN

        if ($close) {
            if (Auth::user()->permissions->admin) {
                return redirect('/'.Auth::user()->team->app_type.'/users/team');
            } else {
                return redirect('/'.Auth::user()->team->app_type.'/');
            }
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/users/'.$id.'/edit');
        }

    }


    public function teamIndex($app_type)
    {
        $other_teams = Auth::user()->team->account->teams
                                     ->where('id', '!=', Auth::user()->team->id);

        if (Auth::user()->team->app_type == 'office') {
            // Don't display Campaign team if user is current in the Office app
            $other_teams = $other_teams->where('app_type', '!=', 'campaign');
        }

        $users = Auth::user()->team->usersall->where('id', '!=', Auth::user()->id)
                                             ->sortBy('name');

        $users->prepend(Auth::user());	// Puts current user at top of the list

        //Remove guests
        $users = $users->reject(function ($item) {
                                        if (!$item->permissions) return false;
                                        return $item->permissions->guest; // Not a Guest
                                    });

        $team = Team::find(Auth::user()->team->id);

        return view('shared-features.user.team', compact('users', 'team', 'other_teams'));
    }
}
