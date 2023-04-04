<?php

namespace App\Http\Livewire\UserUploadToMaster;

use App\UserUpload;
use App\UserUploadData;
use Auth;
use Livewire\Component;

class MatchUserUpload extends Component
{
    public $upload_id = null;
    public $column_map = [];
    public $new_rules = [];
    public $show_review = true;
    public $paginate_offset = 0;
    public $review_offset = 0;

    public $updatable_fields = [

            'voter_id' => 'MA Voter ID',
            'name_title' => 'Title',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'suffix_name' => 'Suffix Name',
            'address_prefix' => 'Address Prefix',
            'address_number' => 'Address Number',
            'address_fraction' => 'Address Fraction',
            'address_street' => 'Address Street',
            'address_apt' => 'Addresss Apt',
            'address_city' => 'Address City',
            'address_state' => 'Address State',
            'address_zip' => 'Address Zip',
            'address_zip4' => 'Address Zip + 4',
            'gender' => 'Gender',
            'party' => 'Party',
            'dob' => 'DOB',
                                ];

    public function setReviewOffset($dir)
    {
        $sign = ($dir == 'prev') ? -1 : 1;
        $max = UserUpload::find($this->upload_id)->lines->count() - 1; //-1 b/c array starts at 0
        $this->review_offset += $sign;
        if ($this->review_offset < 0) {
            $this->review_offset = 0;
        }
        if ($this->review_offset > $max) {
            $this->review_offset = $max;
        }
    }

    public function setPaginateOffset($dir, $n)
    {
        $sign = ($dir == 'prev') ? -1 : 1;
        $this->paginate_offset += ($n * $sign);
        if ($this->paginate_offset < 0) {
            $this->paginate_offset = 0;
        }
    }

    public function toggleReview()
    {
        $this->show_review = ($this->show_review) ? false : true;
    }

    public function mount($upload_id)
    {
        $this->upload_id = $upload_id;

        $upload = UserUpload::find($this->upload_id);
        $this->column_map = $upload->column_map;
        $this->new_rules = (! $upload->new_rules) ? '' : $upload->new_rules;
    }

    public function render()
    {

        //Because Updated() not called with $set()
        $upload = UserUpload::find($this->upload_id);
        $upload->column_map = $this->column_map;
        $upload->new_rules = $this->new_rules;
        $upload->save();

        // Prepare View
        $upload = UserUpload::find($this->upload_id);

        return view('livewire.user-upload-to-master.match-user-upload', [
                            'upload' 	=> $upload,
                        ]);
    }

    public function removeMatch($id)
    {
        $match = UserUploadData::find($id);
        $match->voter_id = null;
        $match->person_id = null;
        $match->participant_id = null;
        $match->save();
    }

    public function addRule($column)
    {
        $upload = UserUpload::find($this->upload_id);
        $column_map = $upload->column_map;
        $column_map[$column][] = ['action'    => null,
                                  'qual'      => null,
                                  'if'        => null,
                                  'if-qual'   => null,
                                 ];
        $upload->column_map = $column_map;
        $upload->save();
        $this->column_map = $column_map;
    }

    public function deleteRule($column, $rule)
    {
        $upload = UserUpload::find($this->upload_id);
        $column_map = $upload->column_map;
        unset($column_map[$column][$rule]);
        $upload->column_map = $column_map;
        $upload->save();
        $this->column_map = $column_map;
    }
}
