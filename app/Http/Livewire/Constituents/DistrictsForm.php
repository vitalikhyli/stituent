<?php

namespace App\Http\Livewire\Constituents;

use App\District;

use Livewire\Component;
use App\Traits\LivewireConstituentQueryTrait;

use Auth;


class DistrictsForm extends Component
{
	use LivewireConstituentQueryTrait;

   //////////////////////////////////[ LISTENERS ]////////////////////////////////////////

    protected $listeners = [
        'clear_all'                 => 'clearAllDistricts'
    ];

    public function clearAllDistricts()
    {
        $this->selected_districts  = [];
        $this->lookup_F            = null;
        $this->lookup_H            = null;
        $this->lookup_S            = null;

        foreach(['F', 'S', 'H'] as $code) {
            $this->is_open[$code] = false;
        }        
    }

    //////////////////////////////////[ VARIABLES ]////////////////////////////////////////
	
    public $selected_districts = [];
	public $is_open            = [];
	public $lookup_F           = null;
	public $lookup_H           = null;
	public $lookup_S           = null;

    //////////////////////////////////[ FUNCTIONS ]////////////////////////////////////////

	public function toggleOpen($key)
	{
		$this->is_open[$key] = ($this->is_open[$key]) ? false : true;
		$this->dispatchBrowserEvent('focus-search', ['field' => $key]);
	}

	public function clearSearch($code)
	{
		$this->{'lookup_'.$code} = null;
	}

    public function cleanUpSelectedDistricts($districts)
    {
        foreach($districts as $id => $value) {
            if (!$value) unset($districts[$id]);
        }
        return $districts;
    }

    ////////////////////////////////////[ LIFECYCLE ]////////////////////////////////////////

    public function mount()
    {
        foreach(['F', 'S', 'H'] as $code) {
            $this->is_open[$code] = false;
        }
    }

    public function updatedSelectedDistricts()
    {
    	$this->selected_districts = $this->cleanUpSelectedDistricts($this->selected_districts);
    	$this->emit('pass_selected_districts', $this->selected_districts); 
    }

    public function render()
    {
        $district_ids   = $this->getDistricts()->pluck('id')->unique();
        $selected_district_ids = ($this->selected_districts) ? $this->selected_districts : [];

        $district_options = [];
        foreach(['F' => 'Congress',
    			 'H' => 'House',
    			 'S' => 'Senate'] as $code => $english) {

            $look_for = $this->{'lookup_'.$code};
            $the_districts = District::where('type', $code)
                                     ->where('state', session('team_state'))
                                     ->whereIn('id', $district_ids);
            $districts_count = $the_districts->count();

            if ($look_for && strlen($look_for) > 2) {
                // $the_districts = $the_districts->where('name', 'like', '%'.$look_for.'%');
                $the_districts = $the_districts->where(function ($q) use ($look_for) {
                    $q->orWhere('name', 'like', '%'.$look_for.'%');
                    $q->orWhere('elected_official_name', 'like', '%'.$look_for.'%');
                });
            }

            $the_districts = $the_districts->get();

            if ($districts_count < 10) {

                // No need to put selected ones up top if it's a short list
                $the_districts_chosen = collect([]);

            } else {

              $the_districts_chosen = District::where('type', $code)
                                ->whereIn('id', $this->selected_districts)
                                ->get();

            }

       		$district_options[] = (object) [
					   'code' => $code,
                       'english' => $english,
                       'num_selected' => District::whereIn('id', $selected_district_ids)
                                                 ->where('type', $code)
                                                 ->count(),
                       'districts' => (object) $the_districts,
                       'chosen' => (object) $the_districts_chosen
 						];
		}

        return view('livewire.constituents.districts-form', 
        	[
        		'district_options' => $district_options
        	]);
    }

}
