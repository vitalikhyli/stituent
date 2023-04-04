<?php
/* EDITING OUT UNNECESSSARY CONTROLLER

namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Team;
use App\Permission;

use Auth;

use Validator;

use Mail;

use Log;

use Carbon\Carbon;

use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
{

    public function emailLink($id)
    {
        Log::info("Emailing ".now());

        $id = base64_decode($id);

        $user = User::find($id);

        $info = array(
            'recipient_email' 	=> $user->email,
            'recipient_name' 	=> $user->name,
            'from_email' 		=> Auth::user()->email,
            'from_name' 		=> Auth::user()->name,
            'subject' 			=> Auth::user()->team->name,
            'system_email' 		=> 'contact@fluency.software', //config('app.system_from_email'),
            'system_name' 		=> 'Community Fluency' //config('app.system_from_name'),
        );

        $data = array(
            'date'				=> Carbon::now()->format("m/d/Y"),
            'link'				=> 'stituent.test/link/'.$user->login_token,
            'to_name'			=> $user->name,
            'from_name'			=> Auth::user()->name,
            'account'			=> Auth::user()->team->name
        );


        $validator = Validator::make(array_merge($info, $data), [
            'recipient_email' 	=> ['required','email'],
            'recipient_name' 	=> ['required', 'max:50'],
            'from_email' 		=> ['required','email'],
            'from_name' 		=> ['required', 'max:50'],
            'subject' 			=> ['required', 'max:50'],
            'to_name' 			=> ['required', 'max:50'],
            'system_email' 		=> ['required','email'],
            'system_name' 		=> ['required', 'max:50'],
        ]);

        if ($validator->fails()) {

            return 'Error'; //.' -- Validation failed'.print_r($info);

        }

        $html_blade 		= 'emails.link';
        $plaintext_blade 	= 'emails.link_plain';

        Mail::send([$html_blade, $plaintext_blade], $data, function($message) use ($info)
        {
            $message->from($info['system_email'],$info['system_name']);
            $message->replyTo($info['from_email'],$info['from_name']);
            $message->to($info['recipient_email'],$info['recipient_name']);
            $message->subject($info['subject']);
        });

        if (Mail::failures()) {

           return 'Error';

        } else {

           return 'Done!';

        }

    }

    public function settings()
    {
        return redirect('u/users/'.Auth::user()->id.'/edit');
    }

    public function new()
    {
        return view('u.user.new');
    }

    public function save(Request $request)
    {

        $validator = Validator::make($request->toArray(), [
            'email' 			=> ['required', 'unique:users,email', 'email'],
            'name' 				=> ['required', 'min:3', 'max:25'],
        ],
        $messages = [
            'email'    			=> 'Please correct your email address'
        ]);

        if ($validator->errors()->count() > 0) {

            return redirect('u/users/new')->withErrors($validator)->withInput();

        } else {

            $user = new User;
            $user->login_token 			= $user->generateLoginToken();
            $user->change_password		= 1;
            $user->active 				= 1;
            $user->name 				= request('name');
            $user->email 				= request('email');
            $user->current_team_id		= Auth::user()->team->id;
            $user->save();

            $team = Auth::user()->team;
            $user->attachTeam($team);

            $permissions = new Permission;
            $permissions->team_id 		= $team->id;
            $permissions->user_id 		= $user->id;
            $permissions->admin 		= 0;
            $permissions->save();

            return $this->edit($user->id);
        }
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('u.user.edit',compact('user'));
    }

    public function update(Request $request, $id, $close = null)
    {

        if(request('new_1')) {

            $validator = Validator::make($request->toArray(), [
                'new_1' 			=> ['required', 'min:8', 'max:30'],
                'new_2' 			=> ['required', 'min:8', 'max:30', 'same:new_1'],

                'email' 			=> ['required', 'unique:users,email,'.$id, 'email'],
                'name' 				=> ['required', 'min:3', 'max:25'],
            ],
            $messages = [
                'same'    			=> 'The passwords you entered don\'t match!',
                'new_1.required' 	=> 'First password in blank',
                'new_2.required' 	=> 'Confirmation password is blank',
                'min' 				=> 'Passwords must be at least 8 characters long',

                'email'    			=> 'Please correct your email address'

            ]);

        } else {

            $validator = Validator::make($request->toArray(), [
                'email' 			=> ['required', 'unique:users,email,'.$id, 'email'],
                'name' 				=> ['required', 'min:3', 'max:25'],
            ],
            $messages = [
                'email'    			=> 'Please correct your email address'
            ]);

        }

        if ($validator->errors()->count() > 0) {

            return redirect('u/users/'.$id.'/edit')->withErrors($validator)->withInput();

        } else {

            $user = User::find($id);

            $user->name 						= request('name');
            $user->email 						= request('email');
            $user->active 						= request('active');
            $user->language 					= request('language');

            if (request('new_login_token')) {
                $user->login_token = $user->generateLoginToken();
                $user->change_password = true;
            }
            if (request('new_1')) {

                $user->password = Hash::make(request('new_1'));
                $user->change_password = 0;
                $user->login_token = null;

            } elseif ((!empty($user->password)) &&(!request('new_login_token'))) {

                $user->change_password	= (request('change_password')) ? 1 : 0;

            }

            $user->save();

            if (!$user->change_password) {
                $user->login_token = null;
            }

            $user->save();

            $permissions = Permission::where('user_id',$id)
                                       ->where('team_id',Auth::user()->team->id)
                                       ->first();
            $permissions->title 				= request('title');
            $permissions->developer 			= (request('developer')) ? 1 : 0;
            $permissions->admin 				= (request('admin')) ? 1 : 0;
            $permissions->chat 					= (request('chat')) ? 1 : 0;
            $permissions->reports 				= (request('reports')) ? 1 : 0;
            $permissions->metrics 				= (request('metrics')) ? 1 : 0;
            $permissions->constituents			= (request('constituents')) ? 1 : 0;
            $permissions->creategroups			= (request('creategroups')) ? 1 : 0;

            $permissions->save();

        }

        if ($close) {
            if (Auth::user()->permissions->admin) {
                return redirect('u/team');
            } else {
                return redirect('u/');
            }
        } else {
            return redirect('u/users/'.$id.'/edit');
        }
    }


    public function teamIndex() {
        $users = User::join('team_user','users.id','team_user.user_id')
                     ->where('team_id', Auth::user()->team->id)
                     ->get();

        $team = Team::find(Auth::user()->team->id);

        return view('u.user.team', compact('users','team'));
    }

}
