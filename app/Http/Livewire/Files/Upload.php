<?php

namespace App\Http\Livewire\Files;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\WorkFile;

use Auth;

class Upload extends Component
{
	use WithFileUploads;

	public $theFile;
	public $model;

    public function uploadFile()
    {
        $this->validate([
            'theFile' => 'max:5000', // 
        ]);

        if ($this->model && Auth::user()->cannot('basic', $this->model)) abort(403);

        //CREATE MODEL
        $record = new WorkFile;
        $record->name 	= $this->theFile->getClientOriginalName();
        $record->user_id = Auth::user()->id;
        $record->team_id = Auth::user()->team->id;
        // if (! isset($options['directory_id'])) {
            $record->directory_id = Auth::user()->team->defaultDirectory();
        // } else {
            // $record->directory_id = $options['directory_id'];
        // }
        $record->save();

        $name_to_save = str_pad($record->id, 8, '0', STR_PAD_LEFT).'_'.$record->name;
       
        $dir = '/'
        		.Auth::user()->team->app_type
        		.'/team_'
                .str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT);


        try {

        	$this->theFile->storeAs(config('app.user_upload_dir').$dir, $name_to_save);

        } catch (\Exception $e) {

        	// dd($e->getMessage(), Auth::user()->team->uploadFolder());
            $record->delete();

        }

        $record->path = $dir.'/'.$name_to_save;
        $record->save();

        if ($this->model) {

            //ATTACH TO MODEL
            switch (get_class($this->model)) {

            	case 'App\Group':
            	$record->groups()->attach($this->model, ['team_id' => Auth::user()->team->id]);

    			case 'App\WorkCase':
    			$record->cases()->attach($this->model, ['team_id' => Auth::user()->team->id]);

    			case 'App\Person':
    			$record->people()->attach($this->model, ['team_id' => Auth::user()->team->id]);
            }

        }

        $this->theFile = null;
        $this->display = true;
    }

    public function mount()
    {

    }

    public function render()
    {
        // For some reason, must re-fetch the model or it will not refresh after uploadFile()

        if ($this->model) {

            $model_name = '\\'.get_class($this->model);
            $the_model = $model_name::find($this->model->id);

        } else {

            $the_model = null;
        }

        return view('livewire.files.upload', ['the_model' => $the_model]);
    }
}
