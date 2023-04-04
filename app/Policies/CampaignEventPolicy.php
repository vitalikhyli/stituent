<?php

namespace App\Policies;

use App\CampaignEvent;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignEventPolicy
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

    public function basic(User $user, CampaignEvent $event)
    {
        return $event->team->users->contains($user->id);
    }
}
