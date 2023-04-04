<?php

namespace App\Http\Livewire;

use Livewire\Component;

class WebSignups extends Component
{
	public $webform;
    public $volunteers = [];

    public function mount()
    {
        $options = $this->webform->options;
        if (!is_array($options)) {
            $this->volunteers = CurrentCampaign()->volunteer_options(null, true);
        }
        if (!isset($options['volunteers'])) {
            $this->volunteers = CurrentCampaign()->volunteer_options(null, true);
        } else {
            $this->volunteers = $options['volunteers'];
        }
        
    }

	protected $rules = [
        'webform.button' => '',
    ];

    public function render()
    {
        $options = $this->webform->options;
        if (!is_array($options)) {
            $options = [];
        }
        $options['volunteers'] = $this->volunteers;
        $this->webform->options = $options;
    	$this->webform->save();
        return view('livewire.web-signups');
    }
    public function delete()
    {
    	$this->webform->delete();
    }
    public function restore()
    {
    	$this->webform->restore();
    }
}
