<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// use App\User;
// use App\Team;

// use Auth;

class UsersController__OLD extends Controller
{
    // public function checkToken($token)
    // {
    //     $user = User::where('login_token',$token)->first();
    //     if (!$user) {
    //         return redirect('/');
    //     } else {
    //         Auth::login($user);
    //         return redirect($user->team->app_type.'/users/'.$user->id.'/edit');
    //     }
    // }

    // public function addMemory($user_id, $key, $value) {
    //     $user = User::where('id',$user_id)->first();
    //     $user->addMemory($key, $value);
    //     $user->save();
    // }

    // public function changeTeam($team_id) {
    // 	$user = Auth::User();
    //     $user->current_team_id = $team_id;
    //     $user->save();
    //     return redirect('/'.Team::find($team_id)->app_type);
    // }

    // public function setTeamCol($col, $v) {
    // 	$team = Auth::User()->team;
    // 	$team->$col = $v;
    // 	$team->save();
    // 	return back();
    // }
}
