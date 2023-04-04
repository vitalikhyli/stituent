<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

use App\UserUploadData;
use App\Participant;

class UserUpload extends Model
{
    protected $table = 'user_uploads';

    protected $casts = [
            'columns'  => 'array',
            'column_matches'  => 'array',
            'column_map'  => 'array',
        ];

    public function filterOptions($filtercol) 
    {
        $index = 0;
        foreach ($this->columns as $col) {
            if ($col == $filtercol) {
                break;
            }
            $index++;
        }
        $upload_data = $this->lines;
        $values = [];
        foreach ($upload_data as $ud) {
            if (isset($ud->data[$index])) {
                $value = $ud->data[$index];
                if (isset($values[$value])) {
                    $values[$value] += 1;
                } else {
                    $values[$value] = 1;
                }
            }
        }
        return $values;
    }

    public function addUserUploadData($voter_id, $columns)
    {
        $data = UserUploadData::where('upload_id', $this->id)
                              ->where('voter_id', $voter_id)
                              ->first();
        if (!$data) {

            $data = new UserUploadData;
            
            $data->team_id = Auth::user()->team->id;
            $data->voter_id = $voter_id;
            $data->upload_id = $this->id;
            $data->data = $columns;

            $participant = Participant::thisTeam()
                                      ->where('voter_id', $voter_id)
                                      ->first();

            if($participant) {
                $data->participant_id = $participant->id;
            }

            $data->save();

            return 1;
        }

        return 0;
    }

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function lines()
    {
        return $this->hasMany(UserUploadData::class, 'upload_id');
    }

    public function matched_lines()
    {
        return $this->hasMany(UserUploadData::class, 'upload_id')
                    ->where(function ($q) {
                        $q->orWhere('voter_id', '!=', null);
                        $q->orWhere('participant_id', '!=', null);
                        $q->orWhere('person_id', '!=', null);
                    });
    }

    public function hasMatchedColumn($column)
    {
        foreach ($this->column_matches as $match) {
            if ($match['db'] == $column) {
                return true;
            }
        }
    }
}
