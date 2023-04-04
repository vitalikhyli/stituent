<?php

namespace App\Http\Livewire\UserUploadToMaster;

use App\UserUpload;
use Livewire\Component;

class EditUserUpload extends Component
{
    public $upload_id = null;

    public $upload_name = null;
    public $preview = null;
    public $preview_count = null;
    public $show_preview = true;

    public $column_matches = [];
    public $all_matches = false;

    public $matchable_fields = [

            'voter_id' => 'Voter ID',
            '****1' => null,
            'full_name' => 'Full Name',
            'full_name_middle' => 'Full Name with Middle',
            '****2' => null,
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'suffix_name' => 'Suffix Name',
            '****3' => null,
            'primary_email' => 'Primary Email',
            '****4' => null,
            'full_address' => 'Full Address',
            'address_prefix' => 'Address Prefix',
            'address_number' => 'Address Number',
            'address_fraction' => 'Address Fraction',
            'address_street' => 'Address Street',
            'address_apt' => 'Address Apt',
            'address_city' => 'Address City',
            'address_state' => 'Address State',
            '****4' => null,
            'dob' => 'DOB',
                                ];

    public function shortcutMatches($array_string)
    {
        $array = explode(',', $array_string);

        $db_fields = [];
        foreach ($array as $field) {
            $db_fields[] = ['user' => null, 'db' => trim($field)];
        }
        $this->column_matches = $db_fields;
    }

    public function mount($upload_id, $preview, $preview_count)
    {
        $this->upload_id = $upload_id;
        $this->preview = $preview;
        $this->preview_count = $preview_count;

        $upload = UserUpload::find($this->upload_id);
        $this->upload_name = $upload->name;
        $this->column_matches = $upload->column_matches;
    }

    public function addMatch()
    {
        $upload = UserUpload::find($this->upload_id);
        $column_matches = $upload->column_matches;
        $column_matches[] = ['db' => null, 'user' => null];
        $upload->column_matches = $column_matches;
        $upload->save();
        $this->column_matches = $column_matches;
    }

    public function deleteMatch($key)
    {
        $upload = UserUpload::find($this->upload_id);
        $column_matches = $upload->column_matches;
        unset($column_matches[$key]);
        $upload->column_matches = $column_matches;
        $upload->save();
        $this->column_matches = $column_matches;
    }

    public function togglePreview()
    {
        $this->show_preview = ($this->show_preview) ? false : true;
    }

    public function toggleMap()
    {
        $this->show_map = ($this->show_map) ? false : true;
    }

    public function render()
    {

        //Because Updated() not called with $set()
        $upload = UserUpload::find($this->upload_id);
        $upload->name = $this->upload_name;
        $upload->save();

        $matches = $this->column_matches;
        $upload->column_matches = $matches;
        $upload->save();

        // Makes sure all the fields are filled in before showing next button
        $all_matches = ($this->column_matches) ? true : false;
        foreach ($this->column_matches as $match) {
            if ($match['user'] == null) {
                $all_matches = false;
            }
            if ($match['db'] == null) {
                $all_matches = false;
            }
        }
        $this->all_matches = $all_matches;

        // Prepare View
        $upload = UserUpload::find($this->upload_id);

        return view('livewire.user-upload-to-master.edit-user-upload', [
                            'upload' 	=> $upload,
                            'preview'	=> $this->preview,
                            'preview_count'	=> $this->preview_count,
                        ]);
    }
}
