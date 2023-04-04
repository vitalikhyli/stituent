<?php

namespace App\Policies;

use App\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
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

    public function basic(User $user, Group $group)
    {
        return $group->team->users->contains($user->id);
    }

    public function new(User $user)
    {
        return $user->permissions->creategroups;
    }

    public function delete(User $user)
    {
        return $user->permissions->creategroups;
    }

    public function archive(User $user)
    {
        return $user->permissions->creategroups;
    }
}
