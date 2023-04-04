<?php

namespace App\Http\Livewire\UserUploadToMaster;

use Livewire\Component;

class NewUserUpload extends Component
{
    public $upload_id = null;
    public $file_chosen = false;

    public function mount($upload_id)
    {
        $this->upload_id = $upload_id;
    }

    public function fileChosen()
    {
        $this->file_chosen = true;
    }

    public function render()
    {
        return view('livewire.user-upload-to-master.new-user-upload');
    }
}
