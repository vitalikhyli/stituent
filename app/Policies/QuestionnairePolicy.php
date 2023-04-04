<?php

namespace App\Policies;

use App\Questionnaire;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionnairePolicy
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

    public function basic(User $user, Questionnaire $questionnaire)
    {
        return $questionnaire->team->users->contains($user->id);
    }
}
