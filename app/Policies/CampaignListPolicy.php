<?php

namespace App\Policies;

use App\CampaignList;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignListPolicy
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

    public function basic(User $user, CampaignList $list)
    {
        return $list->team->users->contains($user->id);
    }

    public function hasBeenAssignedTo(User $user, CampaignList $list)
    {
        return $list->assignedUsers->contains($user->id);
    }
}
