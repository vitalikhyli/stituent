<?php

namespace App;

use App\Category;
use App\Group;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $table = 'searches';

    protected $casts = ['form' 		=> 'array',
                        'fields'	=> 'array', ];

    public function getFormEnglishAttribute()
    {
        $data = $this->form;

        $english = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 9) == 'category_') {
                $cat_id = substr($key, 9);
                $cat_english = Category::find($cat_id)->name;

                foreach ($value as $thegroup) {
                    if (substr($thegroup, -8) == '_opposed') {
                        $group_id = substr($thegroup, 0, -8);
                        $group_name = Group::find($group_id)->name;
                        $english[$cat_english][] = $group_name.' (Opposed)';
                    } elseif (substr($thegroup, -9) == '_supports') {
                        $group_id = substr($thegroup, 0, -9);
                        $group_name = Group::find($group_id)->name;
                        $english[$cat_english][] = $group_name.' (Supports)';
                    } else {
                        $group_id = $thegroup;
                        $group_name = Group::find($group_id)->name;
                        $english[$cat_english][] = $group_name;
                    }
                }
            } elseif ($key == 'has_not_received_emails') {
                $cat_english = 'Has Not Received Emails';

                foreach ($value as $theemail_id) {
                    $theemail_subject = BulkEmail::find($theemail_id)->name;
                    $english[$cat_english][] = $theemail_subject;
                }
            } elseif ($key == 'has_received_emails') {
                $cat_english = 'Has Received Emails';

                foreach ($value as $theemail_id) {
                    $theemail_subject = BulkEmail::find($theemail_id)->name;
                    $english[$cat_english][] = $theemail_subject;
                }
            } elseif ($key == 'ignore_tracker_code') {
                $cat_english = 'Ignore Tracker Code';
                $english[$cat_english][] = $value;
            } else {
                $english[$key] = [];
            }
        }

        return $english;
    }
}
