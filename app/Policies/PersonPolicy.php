<?php

namespace App\Policies;

use App\Person;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
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

    public function basic(User $user, Person $person)
    {
        return $person->team->users->contains($user->id);
    }

    // public function new(User $user)
    // {
    //     return $user->permissions->create_people;
    // }
}
