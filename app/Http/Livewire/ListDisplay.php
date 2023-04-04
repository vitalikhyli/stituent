<?php

namespace App\Http\Livewire;

use App\Tag;
use App\ParticipantTag;
use App\Participant;
use App\Voter;

use Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListDisplay extends Component
{
    use WithPagination;

    public $list;
    public $perpage;
    public $count;
    public $edit_mode;
    public $tag_with;
    public $affected_count;
    public $guest;
    public $sort;

    protected $updatesQueryString = ['perpage', 'edit_mode', 'tag_with'];

    public function mount($list, $guest = null)
    {
        // dd("Laz");
        $this->list = $list;
        $this->sort = 'last_name';

        $this->perpage = 25;
        if (request('perpage')) {
            $this->perpage = request('perpage');
        }
        $this->edit_mode = false;
        if (request('edit_mode') == 'true') {
            $this->edit_mode = true;
        }

        $this->count = $list->count();
        //dd("Laz");
        if ($guest) {
            $this->guest = true;
            $this->edit_mode = true;
        }

    }

    public function tagWholeList($remove = null)
    {
        if (!$this->tag_with) return;

        $affected_count = 0;

        if (!$remove) {

            $this->list->voters()->chunk(100, function ($voters) use (&$affected_count) {

                foreach($voters as $voter) {
                    $affected_count += $voter->tagWith($this->tag_with);
                }

            });

        } else {

            $this->list->voters()->chunk(100, function ($voters) use (&$affected_count) {
                foreach($voters as $voter) {
                    $affected_count += $voter->removeTag($this->tag_with);
                }
            });
        }

        $this->affected_count = $affected_count;

    }

    // public function workToTagWholeList()
    // {
    //     $tagged_participants = ParticipantTag::where('tag_id', $this->tag_with)
    //                                          ->get()
    //                                          ->pluck('participant_id');
    //     $untagged_voters = Participant::whereIn('id', $tagged_participants)
    //                                   ->whereNotNull('voter_id')
    //                                   ->get()
    //                                   ->pluck('voter_id');
    //     $chunk = $this->list->voters()->whereNotIn('id', $untagged_voters)->take(50)->get();


    //     if ($chunk) {

    //         foreach($chunk as $voter) {
    //             $voter->tagWith($this->tag_with);
    //             $this->updating_tags_count++;
    //         }

    //     } else {

    //         //Done
    //         $this->updating_tags_work = null;
    //         $this->updating_tags_count = 0;
    //         $this->emit('pass_tag_with', $this->tag_with);
            
    //     }

    // }

    public function updatedTagWith()
    {
        $this->affected_count = null; 
    }

    public function toggleEditMode()
    {
        $this->edit_mode = ! $this->edit_mode;
    }

    public function render()
    {

// dd("Laz2");
        $voters = $this->list->voters();

        //dd($voters->toSql());
        //////////////////////////////////////////////////

        if ($this->sort == 'last_name') {
            $voters = $voters->orderBy('last_name');
        }

        if ($this->sort == 'address') {
            $voters = $voters->orderBy('address_city')
                             ->orderBy('address_street')
                             ->orderByRaw('CAST(address_number AS unsigned)')
                             ->orderBy('last_name');
        }

        if ($this->sort == 'address_zip') {
            $voters = $voters->orderBy('address_zip')
                             ->orderBy('last_name');
        }
        ////////////////////////////////////////

        

        $this->perpage = ($this->perpage < 10) ? 10 : $this->perpage;

        //dd($voters->toSql());
        if ($this->perpage != 'all') {
            $voters = $voters->paginate($this->perpage);
            //dd($voters);
        } else {
            $voters = $voters->get();
        }
        //dd("Laz");
        $available_tags = Tag::thisTeam()->get();
        //dd($available_tags);

        $door_count = $voters->groupBy('household_id')->count();
        //dd($door_count);
        return view('livewire.list-display', compact('voters', 'available_tags', 'door_count'));
    }

    public function paginationView()
    {
        return 'livewire.list-paginate-links';
    }
}