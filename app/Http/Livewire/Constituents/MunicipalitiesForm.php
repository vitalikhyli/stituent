<?php

namespace App\Http\Livewire\Constituents;

use Livewire\Component;

use App\Traits\LivewireConstituentQueryTrait;


class MunicipalitiesForm extends Component
{
	use LivewireConstituentQueryTrait;

    //////////////////////////////////[ LISTENERS ]////////////////////////////////////////

    protected $listeners = [
        'clear_all'                 => 'clearAllMunicipalities'
    ];

    public function clearAllMunicipalities()
    {
        $this->selected_cities = [];
        $this->precincts = null;
        $this->selected_city_precincts = [];
        $this->selected_zips   = [];
        $this->lookup_city     = null;
        $this->lookup_zip      = null;

        $this->is_open['cities'] = false;
        $this->is_open['zips']  = false;        
    }

    //////////////////////////////////[ VARIABLES ]////////////////////////////////////////

	public $selected_cities = [];
    public $precincts;
	public $selected_zips 	= [];
	public $is_open         = [];
    public $lookup_city;
    public $lookup_zip;

    //////////////////////////////////[ FUNCTIONS ]////////////////////////////////////////

    public function cleanUpSelected($array)
    {
        foreach($array as $id => $value) {
            if (!$value) unset($array[$id]);
        }
        return $array;
    }    

	public function toggleOpen($key)
	{
		$this->is_open[$key] = ($this->is_open[$key]) ? false : true;
		// $this->dispatchBrowserEvent('focus-search', ['field' => $key]);
	}

    public function clearLookup($code)
    {
        $this->{'lookup_'.$code} = null;
    }

    ////////////////////////////////////[ LIFECYCLE ]////////////////////////////////////////

    public function mount()
    {
        $this->is_open['cities'] = false;
        $this->is_open['zips']  = false;
    }

    public function updatedSelectedCities()
    {
        $this->selected_cities = $this->cleanUpSelected($this->selected_cities);
        $this->emit('pass_selected_cities', $this->selected_cities);        
    }

    public function updatedPrecincts()
    {
        $this->emit('pass_precincts', $this->precincts);        
    }

    public function updatedSelectedZips()
    {
        $this->selected_zips = $this->cleanUpSelected($this->selected_zips);
        $this->emit('pass_selected_zips', $this->selected_zips);        
    }

    public function render()
    {
        //dd($this->precincts);
        return view('livewire.constituents.municipalities-form',
    			[
    				'cities' => $this->getMunicipalities($this->lookup_city),
                    'selected_cities_chosen' => $this->getMunicipalities()
                                                     ->whereIn('id', $this->selected_cities),
    				'zips' => $this->getZips($this->lookup_zip),
                    'zips_chosen' => $this->getZips()->intersect($this->selected_zips)
    			]);
    }
}
