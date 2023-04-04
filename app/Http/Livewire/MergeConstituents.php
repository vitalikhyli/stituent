<?php

namespace App\Http\Livewire;

use App\Person;
use App\Contact;
use App\WorkCase;
use App\CasePerson;
use App\ContactPerson;
use App\BulkEmailQueue;
use App\GroupPerson;
use App\Group;
use App\BulkEmail;
use App\Traits\ConstituentQueryTrait;
use App\Voter;
use Livewire\Component;

class MergeConstituents extends Component
{
    use ConstituentQueryTrait;

    public $lookup_one;
    public $constituents_one;
    public $constituent_one;

    public $lookup_two;
    public $constituents_two;
    public $constituent_two;

    public $combined;
    public $merge_log;

    public function mount($one)
    {
        if ($one) {
            $this->constituent_one = $one;
        }
        // $this->merge_log = [];
    }

    public function render()
    {
        $this->constituents_one = [];
        if ($this->lookup_one) {
            $this->constituents_one = $this->normalizeConstituents($this->lookup_one);
        }
        $this->constituents_two = [];
        if ($this->lookup_two) {
            $this->constituents_two = $this->normalizeConstituents($this->lookup_two);
        }
        //echo $this->lookup;
        //dd($this->constituents);
        if ($this->constituent_one && $this->constituent_two) {
            if (! $this->combined) {
                $combined = $this->constituent_one->replicate();
                if (is_numeric($this->constituent_one->id)) {
                    $combined->id = $this->constituent_one->id;
                } elseif (is_numeric($this->constituent_two->id)) {
                    $combined->id = $this->constituent_two->id;
                }
                if (! $combined->voter_id) {
                    $combined->voter_id = $this->constituent_two->voter_id;
                }

                $this->combined = $combined->toArray();
                $this->combined['title'] = $combined->title;
            }
        } else {
            $this->combined = null;
        }

        return view('livewire.merge-constituents');
    }

    public function selectOne($id)
    {
        if (is_numeric($id)) {
            $this->constituent_one = Person::find($id);
        } else {
            $this->constituent_one = Voter::find($id);
        }
        $this->constituents_one = [];
        $this->lookup_one = [];
    }

    public function selectTwo($id)
    {
        if (is_numeric($id)) {
            $this->constituent_two = Person::find($id);
        } else {
            $this->constituent_two = Voter::find($id);
        }
        $this->constituents_two = [];
        $this->lookup_two = [];
    }

    public function removeOne()
    {
        $this->constituent_one = null;
    }

    public function removeTwo()
    {
        $this->constituent_two = null;
    }

    public function switch()
    {
        $temp = $this->constituent_one;
        $this->constituent_one = $this->constituent_two;
        $this->constituent_two = $temp;
        $this->combined = null;
        $this->merge_log = [];
    }

    public function normalizeConstituents($lookup)
    {
        $limited = [];
        //dd("Laz");
        $people = $this->getPeopleFromName($lookup);
        foreach ($people as $person_or_voter) {
            if ($this->constituent_one) {
                if ($person_or_voter->id == $this->constituent_one->id) {
                    continue;
                }
            }
            if ($this->constituent_two) {
                if ($person_or_voter->id == $this->constituent_two->id) {
                    continue;
                }
            }
            $oneperson = collect([]);
            $oneperson->id = $person_or_voter->id;
            $oneperson->name = $person_or_voter->name;
            $oneperson->full_address = $person_or_voter->full_address;
            $oneperson->is_person = $person_or_voter->is_person;
            $limited[] = $oneperson;
        }

        return $limited;
    }

    public function merge()
    {
        //dd("Laz");
        // ===================================> save recovery info
        $merge_log = [];

        // ===================================> Person Info

        // ===================================> Contacts
        foreach ($this->constituent_two->contactPerson as $cp) {
            $contact = Contact::find($cp->contact_id);
            if ($contact) {
                $merge_log['contacts'][$cp->id] = [
                    'name' => $contact->subject,
                    'person' =>
                        [$this->constituent_two->id => $this->constituent_one->id]
                ];
            }
            
        }

        // ===================================> Groups
        foreach ($this->constituent_two->groupPerson as $gp) {
            $group = Group::find($gp->group_id);
            if ($group) {
                $merge_log['groups'][$gp->id] = [
                    'name' => $group->name,
                    'person' =>
                        [$this->constituent_two->id => $this->constituent_one->id]
                ];
            }
        }

        
        //dd($merge_log);

        // ===================================> Cases

        foreach ($this->constituent_two->casePerson as $casep) {
            $case = WorkCase::find($casep->case_id);
            if ($case) {
                $merge_log['cases'][$casep->id] = [
                    'name' => $case->name,
                    'person' =>
                        [$this->constituent_two->id => $this->constituent_one->id]
                ];
            }
        }

        // ===================================> Bulk Emails
        foreach ($this->constituent_two->bulkEmails as $be) {
            $bulkemail = BulkEmail::find($be->bulk_email_id);
            if ($bulkemail) {
                $merge_log['bulk_emails'][$be->id] = [
                    'name' => $bulkemail->name,
                    'person' =>
                        [$this->constituent_two->id => $this->constituent_one->id]
                ];
            }
        }
        

        // ===================================> Set recovery array

        $this->merge_log = $merge_log;

        //dd($merge_log);
        
    }

    public function confirmMerge()
    {

        $this->constituent_one->updateMergeLog($this->constituent_two, $this->merge_log);

        foreach ($this->merge_log as $section => $pivots) {
            foreach ($pivots as $pivot_id => $items) {
                $persons = $items['person'];
                $new_person_id = $persons[$this->constituent_two->id];
                //dd($new_person_id);

                $pivot = null;
                if ($section == 'contacts') {
                    $pivot = ContactPerson::find($pivot_id);
                }
                if ($section == 'groups') {
                    $pivot = GroupPerson::find($pivot_id);
                }
                if ($section == 'cases') {
                    $pivot = CasePerson::find($pivot_id);
                }
                if ($section == 'bulk_emails') {
                    $pivot = BulkEmailQueue::find($pivot_id);
                }

                if ($pivot) {
                    $pivot->person_id = $new_person_id;
                    $pivot->save();
                }
            }
        } 

        $other_emails = [];
        if ($this->constituent_one->other_emails) {
            $other_emails = $this->constituent_one->other_emails;
        }
        $other_emails[] = $this->constituent_two->email;
        if ($this->constituent_one->primary_email) {
            $this->constituent_one->other_emails = $other_emails;
        } else {
            $this->constituent_one->primary_email = $this->constituent_two->email;
        }

        $other_phones = [];
        if ($this->constituent_one->other_phones) {
            $other_phones = $this->constituent_one->other_phones;
        }
        $other_phones[] = $this->constituent_two->phone;
        if ($this->constituent_one->primary_phone) {
            $this->constituent_one->other_phones = $other_phones;
        } else {
            $this->constituent_one->primary_phone = $this->constituent_two->phone;
        }

        $this->constituent_two->delete();
        $this->constituent_one->save();

        $this->constituent_one = Person::find($this->constituent_one->id);
        $this->constituent_two = null;
        $this->merge_log = null;
        // $cp->person_id = $this->constituent_one->id;
        // $constituent_one->updateMergeLog($merge_log);
        // $cp->save();
    }
}
