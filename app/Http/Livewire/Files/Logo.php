<?php

namespace App\Http\Livewire\Files;

use Illuminate\Support\Facades\Storage;

use App\Team;

use Livewire\Component;
use Livewire\WithFileUploads;

use Auth;
use File;

use Carbon\Carbon;


class Logo extends Component
{
	use WithFileUploads;

	public $display;
	public $theFile;
    public $confirmDelete;
    public $formMode;

    public function uploadFile()
    {
        $this->validate([
            'theFile' => 'image|max:4096', // 4 MB
        ]);


        $the_name = Carbon::now()->timestamp.'-'.$this->theFile->getClientOriginalName();
      
        $dir = '/logos_'.str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT);

        try {

    		$this->theFile->storeAs('public/user_uploads/'.$dir, $the_name);

    	} catch (\Exception $e) {

    		dd($e->getMessage());

    	}

     	$team = Auth::user()->team;
     	$team->logo_img = $the_name;
     	$team->save();

        $this->theFile = null;
        $this->display = true;
    }

    public function setLogo($filename)
    {
     	$team = Auth::user()->team;
     	$team->logo_img = $filename;
     	$team->save();

     	$this->display = true;
        $this->confirmDelete = null;
    }

    public function displayFalse()
    {
        if (Auth::user()->permissions->admin || Auth::user()->permissions->developer) {
            $this->display = false;
        }
    }


    public function deleteIsConfirmed($filename)
    {
        try {

            $path = storage_path().'/app/public/user_uploads/logos_'.str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT).'/'.$filename;

            if (strpos($path, '..')) return; //Security? Prevent hard-coded "../../"

            $success = File::delete($path);

        } catch (\Exception $e) {

            dd($e->getMessage());

        }

        if ($success) {

            $team = Auth::user()->team;

            if ($team->logo_img == $filename) {

                $team->logo_img = null;
                $team->save();

            }

        }

        $this->confirmDelete = null;
    }

	public function mount($formMode = null)
	{
		$this->display = true;
        $this->formMode = $formMode;

	}

    public function render()
    {

		$dir = storage_path().'/app/public/user_uploads/logos_'.str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT);

        if (!file_exists($dir)) {

            mkdir($dir, 0777, true);
        }

	    $files = File::files($dir);

	    $logos = [];
	    foreach($files as $file) {
	    	$logos[] = $file->getFilename();
	    }

        return view('livewire.files.logo', ['logos' => $logos]);
    }
}
