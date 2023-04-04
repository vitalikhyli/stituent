<?php

namespace App\Http\Livewire\UserUploadToMaster;

use App\Participant;
use App\Person;
use App\UserUpload;
use App\UserUploadData;
use Livewire\Component;

class IntegrateUserUpload extends Component
{
    public $upload_id = null;

    public function mount($upload_id)
    {
        $this->upload_id = $upload_id;
    }

    public function render()
    {
        $records = UserUploadData::where('upload_id', $this->upload_id)
                                 ->where(function ($q) {
                                     $q->orWhere('person_id', '!=', null);
                                     $q->orWhere('participant_id', '!=', null);
                                 })
                                 ->paginate(500);

        $upload = UserUpload::find($this->upload_id);

        ////////////////////////////////////////////////////////////////////////

        $summary['total'] = $upload->lines()->count();

        ////////////////////////////////////////////////////////////////////////

        $summary['participants_new'] = Participant::where('upload_id', $upload->id)
                                                         ->count();

        $summary['participants_new_vf'] = Participant::where('upload_id', $upload->id)
                                                         ->whereNotNull('voter_id')
                                                         ->count();

        $summary['participants_matches'] = $upload->lines()
                                             ->whereNotNull('participant_id')
                                             ->whereNotIn('participant_id',
                                                Participant::where('upload_id', $upload->id)->pluck('id')->toArray()
                                             )->count();

        $summary['participants_matches_vf'] = $upload->lines()
                                             ->whereNotNull('participant_id')
                                             ->whereNotNull('voter_id')
                                             ->whereNotIn('participant_id',
                                                Participant::where('upload_id', $upload->id)->pluck('id')->toArray()
                                             )->count();

        ////////////////////////////////////////////////////////////////////////

        $summary['people_new'] = Person::where('upload_id', $upload->id)
                                                         ->count();

        $summary['people_new_vf'] = Person::where('upload_id', $upload->id)
                                                         ->whereNotNull('voter_id')
                                                         ->count();

        $summary['people_matches'] = $upload->lines()
                                         ->whereNotNull('person_id')
                                         ->whereNotIn('person_id',
                                                Person::where('upload_id', $upload->id)->pluck('id')->toArray()
                                            )->count();

        $summary['people_matches_vf'] = $upload->lines()
                                         ->whereNotNull('person_id')
                                         ->whereNotNull('voter_id')
                                         ->whereNotIn('person_id',
                                            Person::where('upload_id', $upload->id)->pluck('id')->toArray()
                                         )->count();

        ////////////////////////////////////////////////////////////////////////

        $summary['skipped'] = $summary['total'] - $summary['participants_new'] - $summary['participants_matches'] - $summary['people_new'] - $summary['people_matches'];

        ////////////////////////////////////////////////////////////////////////

        return view('livewire.user-upload-to-master.integrate-user-upload', compact('upload', 'records', 'summary'));
    }
}
