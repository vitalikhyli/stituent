<?php

namespace App\Observers;

use App\Group;
use App\GroupPerson;
use Auth;

class GroupPersonObserver
{
    // https://glennkimble.me/blog/listen-to-events-from-pivot-tables-in-laravel/

    ////////////////////////////////////////////////////////

    public function setCreatedBy($groupPerson)
    {
        // Needs to do this because does not get whole model for some reason
        $groupPerson = $groupPerson::find($groupPerson->id);
        if ($groupPerson) {
            if (Auth::user()) {
                $groupPerson->created_by = Auth::user()->id;
                $groupPerson->save();
            }
        }
    }

    public function setUpdatedBy($groupPerson)
    {
        // Needs to do this because does not get whole model for some reason
        $groupPerson = $groupPerson::find($groupPerson->id);
        if ($groupPerson) {
            if (Auth::user()) {
                $groupPerson->updated_by = Auth::user()->id;
                $groupPerson->save();
            }
        }
    }

    public function setDeletedBy($groupPerson)
    {
        // Needs to do this because does not get whole model for some reason
        $groupPerson = $groupPerson::find($groupPerson->id);
        if ($groupPerson) {
            if (Auth::user()) {
                $groupPerson->deleted_by = Auth::user()->id;
                $groupPerson->save();
            }
        }
    }

    ////////////////////////////////////////////////////////

    public function updateCounts($groupPerson)
    {
        $group = Group::find($groupPerson->group_id);
        if ($group) {
            $group->updatePeopleCounts();
        }
    }

    /**
     * Handle the group person "created" event.
     *
     * @param  \App\GroupPerson  $groupPerson
     * @return void
     */
    public function created(GroupPerson $groupPerson)
    {
        $this->setCreatedBy($groupPerson);

        $this->updateCounts($groupPerson);
    }

    /**
     * Handle the group person "updated" event.
     *
     * @param  \App\GroupPerson  $groupPerson
     * @return void
     */
    public function updated(GroupPerson $groupPerson)
    {
        $this->setUpdatedBy($groupPerson);

        $this->updateCounts($groupPerson);
    }

    /**
     * Handle the group person "deleted" event.
     *
     * @param  \App\GroupPerson  $groupPerson
     * @return void
     */
    public function deleted(GroupPerson $groupPerson)
    {
        $this->setDeletedBy($groupPerson);

        $this->updateCounts($groupPerson);
    }

    /**
     * Handle the group person "restored" event.
     *
     * @param  \App\GroupPerson  $groupPerson
     * @return void
     */
    public function restored(GroupPerson $groupPerson)
    {
        $this->setCreatedBy($groupPerson); // Restored is as if created anew

        $this->updateCounts($groupPerson);
    }

    /**
     * Handle the group person "force deleted" event.
     *
     * @param  \App\GroupPerson  $groupPerson
     * @return void
     */
    public function forceDeleted(GroupPerson $groupPerson)
    {
        $this->updateCounts($groupPerson);
    }
}
