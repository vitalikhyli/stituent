<?php

namespace App\Policies;

use App\Donation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DonationPolicy
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

    public function basic(User $user, Donation $donation)
    {
        return $donation->team->users->contains($user->id);
    }
}
