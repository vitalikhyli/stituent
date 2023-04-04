<?php

namespace App\Traits;

use App\SharedCase;
use App\CasePerson;
use App\Person;

trait SharedCasesTrait
{
    public function getSharedCases($voter_id)
    {
        $shared_cases = collect([]);
        if ($voter_id) {
            $shared_with_me = SharedCase::sharedWithMe()->get();

            $person = Person::find($voter_id);
            if ($person) {
                foreach ($shared_with_me as $sc) {
                    $case_id = $sc->case_id;
                    $cp = CasePerson::where('case_id', $case_id)
                                    ->where('voter_id', $voter_id)
                                    ->first();
                    if ($cp) {
                        $shared_cases[] = $sc;
                    }
                }
            } else {
                foreach ($shared_with_me as $sc) {
                    $case_id = $sc->case_id;
                    $cp = CasePerson::where('case_id', $case_id)
                                    ->where('person_id', $voter_id)
                                    ->first();
                    if ($cp) {
                        $shared_cases[] = $sc;
                    }
                }
            }
        }
        return $shared_cases;
    }
}
