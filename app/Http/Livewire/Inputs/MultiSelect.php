<?php

namespace App\Http\Livewire\Inputs;

use Livewire\Component;

class MultiSelect extends Component
{

	public $allOptions = [];
	public $selectedOptions = [];
	public $anyAll;
	public $field;
	public $query_mode;
	public $query_mode_name;

	public $new_selected_option;

	public function mount()
	{
		if (empty($this->selectedOptions)) {
			$this->selectedOptions = array_merge($this->selectedOptions, ['0']);
		}

		if (request($this->field)) {
            $this->selectedOptions = collect(request($this->field))->unique();
        } else {
			$this->selectedOptions = collect($this->selectedOptions);
        }

		$this->selectedOptions = $this->selectedOptions->map(function ($item) {
            							return $item.'';
            						})->toArray();

		$this->query_mode_name = $this->field."_query_mode";

		if (request($this->query_mode_name)) {
			$this->query_mode = request($this->query_mode_name);
		}

		$arr = [];

		foreach($this->allOptions as $option) {

			if (is_array($option)) {

				// Some future use case

				$arr[$option['id']] = $option['name'];

			} else if (is_object($option)) {

				// This is an object like a model

				$arr[$option->id] = $option->name;

			} elseif (array_key_exists(0, $this->allOptions)) {

				// This is a sequential array of just values,
				// PHP assigned the key starting with 0

				$arr[$option] = $option;

			} else {

				// This is already an array in the form we want

				$arr = $this->allOptions;

			}

		};

		$this->allOptions = $arr;
	}

	public function selectNewOption()
	{
		$this->selectedOptions[] = $this->new_selected_option;
		$this->selectedOptions = collect($this->selectedOptions)->unique()->toArray();
	}

    public function render()
    {
    	$this->new_selected_option = "";
        return view('livewire.inputs.multi-select');
    }
}
