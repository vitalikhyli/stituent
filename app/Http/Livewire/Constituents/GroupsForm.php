<?php

namespace App\Http\Livewire\Constituents;

use App\Group;

use Livewire\Component;

use Auth;


class GroupsForm extends Component
{

    //////////////////////////////////[ LISTENERS ]////////////////////////////////////////

    protected $listeners = [
        'clear_all'                 => 'clearAllGroups'
    ];

    public function clearAllGroups()
    {
        $this->selected_groups = [];
        
        foreach(Auth::user()->team->categories as $cat) {
            $this->category_is_open[$cat->id] = false;
        }        
    }

    //////////////////////////////////[ VARIABLES ]////////////////////////////////////////

	public $selected_groups 	= [];
	public $category_is_open 	= [];

    //////////////////////////////////[ FUNCTIONS ]////////////////////////////////////////

	public function toggleOpen($cat_id)
	{
		$this->category_is_open[$cat_id] = ($this->category_is_open[$cat_id]) ? false : true;
	}

    public function cleanUpSelectedGroups($groups)
    {
    	// Get rid of null / false values
        if (!$groups) return;
        foreach($groups as $id => $data) {
            foreach($data as $key => $value) {
                if (!$value) unset($groups[$id][$key]);
            }
            if (empty($groups[$id])) unset($groups[$id]);
        }
        return $groups;
    }

    ////////////////////////////////////[ LIFECYCLE ]////////////////////////////////////////

    public function mount()
    {
        foreach(Auth::user()->team->categories as $cat) {
            $this->category_is_open[$cat->id] = false;
        }
    }

    public function updatedSelectedGroups()
    {
    	$this->selected_groups = $this->cleanUpSelectedGroups($this->selected_groups);
    	$this->emit('pass_selected_groups', $this->selected_groups); 
    }

    public function render()
    {
        $selected_group_ids = ($this->selected_groups) ? array_keys($this->selected_groups) : null;

        // array:1 [▼
        //   74 => array:2 [▼
        //     "main" => "true"
        //     "support" => "true"
        //   ]
        // ]=

        $with_positions = [];
        foreach($this->selected_groups as $group_id => $positions) {
            foreach($positions as $position => $tof) {
                $group = Group::find($group_id);
                if ($group) {
                    $position_name = '';
                    $position_name = ucfirst($position);
                    if ($position_name == 'Main') $position_name = '';
                    $with_positions[$group->category_id][] = (object) [
                                                                'group_id' => $group->id,
                                                                'position' => $position,
                                                                'name' => $group->name,
                                                                'position_name' => $position_name
                                                            ];
                                
                }
            }
            
        }

        $categories     = Auth::user()->team
                                      ->categories
                                      ->each(function ($category) use ($selected_group_ids) {

                                        if (!$selected_group_ids) return;

                                        $num = Group::whereIn('id', $selected_group_ids)
                                                    ->where('category_id', $category->id)
                                                    ->count();

                                        $category['num_selected'] = $num;

                                      })
                                      ->each(function ($category) use ($with_positions) {

                                        if (!isset($with_positions[$category->id])) return;

                                        $chosen = collect($with_positions[$category->id]);

                                        $category['chosen'] = (object) $chosen;

                                      });

        // dd($categories);
                             
        return view('livewire.constituents.groups-form', 
            [
                'categories' => $categories
            ]);
    }
}
