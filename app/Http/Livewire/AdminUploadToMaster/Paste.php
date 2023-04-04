<?php

namespace App\Http\Livewire\AdminUploadToMaster;

use Livewire\Component;

use App\Traits\LivewirePasteTrait;

use App\Import;
use App\Municipality;

use DB;
use Auth;


class Paste extends Component
{
	use LivewirePasteTrait;

	//--------------------------------------------------------------------------------
	// Non-Generic Paste Tool Variables (others in the Trait)
	//

	public $municipality_lookup;
	public $municipalities = [];
	public $municipality_id;
	public $municipality;


	//--------------------------------------------------------------------------------
	// Component Functions
	//

	public function mount()
	{
		$this->traitMount();

		$this->setAvailableMunicipalities();
		$this->setAvailableModels();

		$this->municipality_id = null;

		$this->model_class = 'App\Import';

		$this->delimiter = "\t"; //"\t";
		dd($this->delimiter);
		
		$this->chunk = 100;

		$this->initial_results_categories 	= [ 'Filtered Out' 		=> 0,
										   		'Uploads Added' 	=> 0,
												'Duplicate Uploads' => 0,
												'Invalid ID' 		=> 0,
												'Voters Found' 		=> 0,
												'Voters Not Found' 	=> 0,
										 		'Tags Added' 		=> 0,
										 		'Tags Removed' 		=> 0,
												'Emails Added' 		=> 0,
												'Phones Added' 		=> 0 ];

		$this->buildMap(['voter_id', 'email', 'phone', 'import_order', 'id', 'last_name', 'first_name']);

		
		$this->template = ['import_order',		// 0 => "Record Sequence Number"
						    'id', 				// 1 => "Voter ID Number"
						    'last_name', 		// 2 => "Last Name"
						    'first_name',		// 3 => "First Name"
						    'middle_name',		// 4 => "Middle Name"
						    'title',			// 5 => "Title"
						    // 6 => "Residential Address Street Number"
						    // 7 => "Residential Address Street Suffix"
						    // 8 => "Residential Address Street Name"
						    // 9 => "Residential Address Apartment Number"
						    // 10 => "Residential Address Zip Code"
						    // 11 => "Mailing Address"
						    // 12 => "Street Number and Name"
						    // 13 => "Mailing Address - Apartment Number"
						    // 14 => "Mailing Address - City or Town"
						    // 15 => "Mailing Address - State"
						    // 16 => "Mailing Address - Zip Code"
						    'party',			// 17 => "Party Affiliation"
						    'dob',				// 18 => "Date of Birth"
						    // 19 => "Date of Registration"
						    'ward',				// 20 => "Ward Number"
						    'precinct',			// 21 => "Precinct Number"
						    // 22 => "Congressional District Number"
						    // 23 => "Senatorial District Number"
						    // 24 => "State Representative District"
						    // 25 => "Voter Status"
						];
	}

    public function storeModel()
    {
        $model = new $this->model_class;
        $model->municipality_id = $this->municipality_id;
        $model->user_id = Auth::user()->id;

        $model->save();

        $this->model = $model;
    }

	//--------------------------------------------------------------------------------
	// Livewire Lifecycle
	//

    public function setAvailableMunicipalities()
    {
        $this->municipalities = \App\Municipality::
                                where('state', $this->state)
                                ->where('name', 'like', $this->municipality_lookup.'%')
                                ->orderBy('name')
                                ->get();
    }

    public function setAvailableModels()
    {
		$this->model_options = Import::orderBy('created_at', 'desc')->get();
    }

	public function updated()
	{
		$this->setAvailableMunicipalities();

   		if ($this->municipality_id) {

			$this->municipality = Municipality::find($this->municipality_id);

		}

    	if ($this->selected_model) {

			$this->model = Import::find($this->selected_model);

			if (!$this->model->table) {
				$table_name = strtolower('x_'.$this->model->name.' '.now());
				$table_name = str_replace(' ', '_', $table_name);
				$table_name = str_replace(':', '_', $table_name);
				
				try {

					DB::connection('imports')->statement('CREATE TABLE IF NOT EXISTS `'.$table_name.'` LIKE '.config('database.connections.main.database').'.x__template_voters');

					$this->model->table_name = $table_name;
					$this->model->save();

				} catch (\Exception $e) {

					$this->model = null;
					$this->selected_model = null;
				}

			}

		}
	}

    public function render()
    {
		$this->setAvailableModels();

		$this->processChunk();

        return view('livewire.admin-upload-to-master.paste',
		        	['lines' 			=> $this->lines,
		        	'process' 			=> $this->process,
		        	'model' 			=> $this->model,
		        	'elapsed' 			=> $this->elapsed,
		        	'time_remaining'	=> $this->time_remaining,
		        	'count' 			=> $this->count
        	]);
    }

	//--------------------------------------------------------------------------------
	// Process
	//
	// This function contains the line by line processing instructions
	// for this specific component

	public function processLine($line)
	{


		$this->resultsIncrement('Filtered Out', 1); // Test

	}
}
