<?php

namespace App\Policies;

use App\Relationship;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RelationshipPolicy
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

    public function basic(User $user, Relationship $relationship)
    {
        return $relationship->team->users->contains($user->id);
    }
}
