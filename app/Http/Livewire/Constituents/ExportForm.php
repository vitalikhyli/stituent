<?php

namespace App\Http\Livewire\Constituents;

use Livewire\Component;

use Carbon\Carbon;

use App\Traits\ExportTrait;


class ExportForm extends Component
{
	use ExportTrait;

	public $export_fields 			= [];
	public $include_groups 			= false;
	public $include_voter_phones 	= false;
	public $householding 			= false;
	public $filename;

	public function triggerDownload()
	{
		$this->emit('send_export_fields_and_begin_download', 
						['export_fields' 		=> $this->export_fields,
						 'include_groups' 		=> $this->include_groups,
						 'include_voter_phones' => $this->include_voter_phones,
						 'householding' 		=> $this->householding,
						 'filename' 			=> $this->filename]);
	}

	public function selectAll($tof)
	{
		foreach($this->export_fields as $key => $field) {
			$this->export_fields[$key] = $tof;
		}
	}

	public function mount()
	{
		foreach($this->getConstituentFields() as $field) {
			$this->export_fields[$field[2]] = $field[0];
		}

		$this->filename = 'CF-Export-'.Carbon::now()->toDateString().'.csv';
	}

    public function render()
    {

    	$fields = $this->getConstituentFields();
    	$field_count = collect($this->export_fields)->reject(function ($item) { return $item !== true; })->count();
    	$total_fields = count($fields);

        return view('livewire.constituents.export-form', 
        	[
        		'fields' => $fields,
        		'field_count' => $field_count,
        		'total_fields' => $total_fields
        	]);
    }
}
