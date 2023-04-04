<?php

namespace App\Http\Livewire\UserUploadToMaster;

use Livewire\Component;
use Carbon\Carbon;
use Auth;

use App\Tag;
use App\Voter;
use App\ParticipantTag;
use App\UserUpload;
use App\CampaignList;

use App\Traits\LivewirePasteTrait;


class Paste extends Component
{
	use LivewirePasteTrait;

	//--------------------------------------------------------------------------------
	// Non-Generic Paste Tool Variables (others in the Trait)
	//

	public $list;

	public $add_emails	= false;
	public $add_phones	= false;

	public $tag_mode;
	public $tag_options;
	public $selected_tag;
	public $tag_count;
	public $tag_model;


	//--------------------------------------------------------------------------------
	// Component Functions
	//

	public function mount()
	{
		$this->traitMount();

		$this->model_class = 'App\UserUpload';

		$this->model_options = UserUpload::thisTeam()->orderBy('created_at', 'desc')->get();

		$this->delimiter = "\t"; //"|";
		
		$this->chunk = 100;

		$this->tag_mode = 'none';

		$this->tag_options = Tag::thisTeam()->orderBy('name')->get();

		$this->initial_results_categories 	= [ 'Filtered Out' 		=> 0,
										   		'Uploads Added' 	=> 0,
												'Duplicate Uploads' => 0,
												'Invalid ID' 		=> 0,
												'Voters Found' 		=> 0,
												'Voters Not Found' 	=> 0,
										 		'Tags Added' 		=> 0,
										 		'Tags Removed' 		=> 0,
												'Emails Added' 		=> 0,
												'Phones Added' 		=> 0];

												//dd("Laz");

		$this->buildMap(['voter_id', 'email', 'phone']);

	}

    public function storeModel()
    {
        $model = new $this->model_class;

        $model->team_id = Auth::user()->team->id;
        $model->user_id = Auth::user()->id;
        $model->name    = $this->model_new_name;

        $model->save();

        //dd($model, $this->visible_chunk);

        $this->model = $model;
    }


	//--------------------------------------------------------------------------------
	// Selectors
	//

	public function setIndex($index, $colname)
	{
		$this->map[$colname] = $index;

		if ($colname == '') {
			foreach($this->map as $key => $value) {
				$this->map[$key] = 'ignore';
			}
		}
	}

	//--------------------------------------------------------------------------------
	// Livewire Lifecycle
	//

	public function updated()
	{
    	if ($this->selected_model) {
			$this->model = UserUpload::find($this->selected_model);
		}

		if ($this->model) {
			$this->list = CampaignList::thisTeam()
									  ->where('name', 'like', '%[Upload #'.$this->model->id.']%')
									  ->first();
		}
	}

    public function render()
    {
    	if ($this->selected_tag) {
			$this->tag_model = Tag::find($this->selected_tag);
			$this->tag_count = ParticipantTag::thisTeam()
											 ->where('tag_id', $this->tag_model->id)
											 ->count();
    	}

    	$this->processChunk();

        return view('livewire.user-upload-to-master.paste',
		        	['lines' 			=> $this->lines,
		        	'tag_options' 		=> $this->tag_options,
		        	'selected_tag' 		=> $this->selected_tag,
		        	'process' 			=> $this->process,
		        	'tag_count' 		=> $this->tag_count,
		        	'model' 			=> $this->model,
		        	'elapsed' 			=> $this->elapsed,
		        	'time_remaining'	=> $this->time_remaining,
		        	'count' 			=> $this->count,
        	]);
    }

	//--------------------------------------------------------------------------------
	// Process
	//
	// This function contains the line by line processing instructions
	// for this specific component

	public function processLine($line)
	{
		$id 	= null;
		$phone 	= null;
		$email 	= null;
		$cols 	= explode($this->delimiter, $line);

		if ($this->model) {
			if (!$this->model->columns) {
				$this->model->columns = $cols;
				$this->model->save();
			}
		}

		////////////////////////////////////////////////////////////////////

		$pass = $this->runFiltersOnColumns($cols);

		if (!$pass) {
			$this->resultsIncrement('Filtered Out', 1);
			return;
		}

		if (empty($cols)) {
			$this->resultsIncrement('Invalid ID', 1);
			return;
		}
		
		////////////////////////////////////////////////////////////////////

		if (is_numeric($this->map['voter_id'])) {
			$id = $this->state.'_'.$cols[$this->map['voter_id']];
			if (strlen($id) < 15) {				// Invalid Voter ID
				$this->resultsIncrement('Invalid ID', 1);
				return;
			}
		}
		if (is_numeric($this->map['email'])) {
			$email = $cols[$this->map['email']];
		}
		if (is_numeric($this->map['phone'])) {
			$phone = $cols[$this->map['phone']];
		}

		////////////////////////////////////////////////////////////////////

		if ($id) {

			$result = $this->model->addUserUploadData($id, $cols);
			if ($result === 1) $this->resultsIncrement('Uploads Added', 1);
			if ($result === 0) $this->resultsIncrement('Duplicate Uploads', 1);

			if ($voter = Voter::find($id)) {

				$this->resultsIncrement('Voters Found', 1);

				if ($this->tag_mode == 'add' && $this->tag_model) {
					$success = $voter->tagWith($this->tag_model->id);
					$this->resultsIncrement('Tags Added', $success);
				}

				if ($this->tag_mode == 'remove' && $this->tag_model) {
					$success = $voter->removeTag($this->tag_model->id);
					$this->resultsIncrement('Tags Removed', $success);
				}

				if ($this->add_emails && $email) {
					$success = $voter->addEmail($email);
					$this->resultsIncrement('Emails Added', $success);
				}

				if ($this->add_phones && $phone) {
					$success = $voter->addPhone($phone);
					$this->resultsIncrement('Phones Added', $success);
				}

			} else {

				$this->resultsIncrement('Voters Not Found', 1);

			}

		}

	}

	//--------------------------------------------------------------------------------
	// Special Functions
	//

	public function reduceColumns()
	{
		$remaining  = explode("\n", $this->textarea);

		$new_filter_columns = [];

		foreach($remaining as $key => $line) {
			$line = explode($this->delimiter, $line);
			$new_line = [];
			$t = 0;

			$needed_indexes = [$this->map['voter_id'], $this->map['email'], $this->map['phone']];

			foreach ($this->filters as $f => $filter_array) {
				if (!in_array($filter_array['column'], $needed_indexes)) {
					$new_line[] = $line[$filter_array['column']];
					$new_filter_columns[$f]['column'] = $t++;
				}
			}

			if (is_numeric($this->map['voter_id'])) {
				$new_line[] = $line[$this->map['voter_id']];
				$new_voter_col = $t++;
			}

			if (is_numeric($this->map['email'])) {
				$new_line[] = $line[$this->map['email']];
				$new_email_col = $t++;
			}

			if (is_numeric($this->map['phone'])) {
				$new_line[] = $line[$this->map['phone']];
				$new_phone_col = $t++;
			}

			$remaining[$key] = implode($this->delimiter, $new_line);
		}

		if(isset($new_voter_col)) $this->map['voter_id'] = $new_voter_col;
		if(isset($new_email_col)) $this->map['email'] = $new_email_col;
		if(isset($new_phone_col)) $this->map['phone'] = $new_phone_col;

		foreach ($this->filters as $f => $filter_array) {
			if(isset($new_filter_columns[$f]['column'])) {
				$this->filters[$f]['column'] = $new_filter_columns[$f]['column'];
			}
		}

		$this->textarea = implode("\n", $remaining);
	}

	public function storeList()
	{
		if (!$this->model) return;

		$list = new CampaignList;
		$list->name = $this->model->name.' [Upload #'.$this->model->id.']';
		$list->team_id = Auth::user()->team->id;
		$list->user_id = Auth::user()->id;
		$list->form = ['imports' => [$this->model->id]];
		$list->save();

		$this->updated();
	}

}
