<?php

namespace App\Policies;

use App\User;
use App\WorkCase;
use App\SharedCase;
use Illuminate\Auth\Access\HandlesAuthorization;
use Auth;


class WorkCasePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function basic(User $user, WorkCase $workcase)
    {
        if ($workcase->private) {
            if (Auth::user()->permissions->admin) return true;
            if ($workcase->user_id == $user->id) {
                return true;
            }
        } else {    
            if ($workcase->team->users->contains($user->id)) {
                return true;
            }
        }
        if (SharedCase::where('case_id', $workcase->id)
                      ->where('shared_type', 'team')
                      ->where('shared_team_id', $user->team_id)
                      ->exists()) {
            return true;
        }
        if (SharedCase::where('case_id', $workcase->id)
                      ->where('shared_type', 'user')
                      ->where('shared_user_id', $user->id)
                      ->exists()) {
            return true;
        }
        return false;
    }
}
