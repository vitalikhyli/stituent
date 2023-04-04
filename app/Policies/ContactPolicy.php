<?php

namespace App\Policies;

use App\Contact;
use App\User;
use App\SharedCase;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
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

    public function basic(User $user, Contact $contact)
    {
        if ($contact->private) {
            
            if (Auth::user()->permissions->admin) {
                return true;
            }

            return $contact->user_id == $user->id;

        } else {

            if($contact->case) {
                if (SharedCase::where('case_id', $contact->case->id)
                              ->where('shared_type', 'team')
                              ->where('shared_team_id', $user->team_id)
                              ->exists()) {
                    return true;
                }
                if (SharedCase::where('case_id', $contact->case->id)
                              ->where('shared_type', 'user')
                              ->where('shared_user_id', $user->id)
                              ->exists()) {
                    return true;
                }
            }

            return $contact->team->users->contains($user->id);
        }
    }
}
