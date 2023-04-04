<?php

namespace App\Policies;

use App\Entity;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
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

    public function basic(User $user, Entity $entity)
    {
        return $entity->team->users->contains($user->id);
    }
}
