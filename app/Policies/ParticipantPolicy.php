<?php

namespace App\Policies;

use App\Participant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParticipantPolicy
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

    public function basic(User $user, Participant $participant)
    {
        return $participant->team->users->contains($user->id);
    }
}
