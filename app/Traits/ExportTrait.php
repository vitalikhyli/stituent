<?php

namespace App\Traits;

use Auth;
use Response;

use App\Person;
use App\Voter;


trait ExportTrait
{
    public function createCSV($output)
    {
        if (! Auth::user()->permissions->export) {
            dd('Error: You do not have full permissions to Export.');
        }
        $headers = [
            'Content-Type' => 'text/csv',
        ];

        //dd($output);

        if (!is_array($output)) {
            $output = $output->toArray();
        }
        
        $filename = 'CF-Export-'.time().'.csv';

        $dir = '/app/user_exports/'
                .Auth::User()->team->app_type
                .'/team_'.str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT)
                .'/';

        if (! file_exists(storage_path().$dir)) {
            mkdir(storage_path().$dir, 0777, true);
        }

        $filename_full = storage_path().$dir.$filename;
        $file = fopen($filename_full, 'w');
        //dd($output);
        if (isset($output[0])) {
            $field_names = array_keys($output[0]);
            foreach ($field_names as $index => $field_name) {
                $field_names[$index] = ucwords(str_replace('_', ' ', $field_name));
            }
        } else {
            $field_names = ['No contacts found for time frame selected'];
        }
        fputcsv($file, $field_names);
        // dd(array_keys($output[0]));
        foreach ($output as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        // dd('Before Response', $output, $dir);

        return Response::download($filename_full, $filename, $headers);
    }

    public function getConstituentFields($basic = null)
    {
        $fields = [

            //=======================================================
            [false, 'Title',            'title'],
            [true,  'Full Name',        'full_name'],
            [true,  'Full Name Middle', 'full_name_middle'],
            [false, 'First',            'first_name'],
            [false, 'Last',             'last_name'],
            //=======================================================
            [true,  'Mailing Address',  'mailing_address'],
            [true,  'Full Address',     'full_address'],
            [false, 'Street No.',       'address_number'],
            [false, 'Street Fraction',  'address_fraction'],
            [false, 'Street',           'address_street'],
            [false, 'Apt',              'address_apt'],
            [true,  'City',             'address_city'],
            [true,  'State',            'address_state'],
            [true,  'Zip',              'address_zip'],
            //=======================================================
            [true,  'Primary Phone',    'primary_phone'],
            [true,  'Cell Phone',       'cell_phone'],
            [true,  'Primary Email',    'primary_email'],
            [true,  'Work Email',       'work_email'],
            [true,  'Master Email List', 'master_email_list'], // master_email = form name
            //=======================================================
            [false, 'Birthday',        'dob'],
            [false, 'Gender',          'gender'],
            //=======================================================
            [false, 'Party',           'party'],
        ];

        // if (Auth::user()->permissions->developer) {

            $fields[] = [false, 'Voter ID',        'voter_id'];

        // }

        if (!$basic) {

            return collect($fields); 

        } else {

            $basic = [];
            foreach($fields as $data) {
                $basic[] = $data[2];
            }

            return collect($basic); 

        }
        
    }

    public function createCSVFileFromConstituents($input,
                                                  $constituents, 
                                                  $column_names, 
                                                  $filename, 
                                                  $include_groups, 
                                                  $include_voter_phones,
                                                  $householding)
    {
        $headers = [
            'Content-Type' => 'text/csv',
        ];

        $filename = time().'_'.$filename;

        $team_dir = Auth::user()->team->app_type.'/team_'.str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT).'/';

        if (! file_exists(storage_path().'/app/user_exports/'.$team_dir)) {
            mkdir(storage_path().'/app/user_exports/'.$team_dir, 0777, true);
        }
        $filename_full = storage_path().'/app/user_exports/'.$team_dir.$filename;

        $file = fopen($filename_full, 'w');

        ////////////////////////////////////////////////////////////////////////

        $group_by = ($householding) ? true : false;

        if ($group_by) {

            //dd("Laz");

            $constituents_householded = $constituents->groupBy('full_address');

            $constituents = collect([]);
            $count = 0;
            foreach ($constituents_householded as $household => $constituents_in_house) {

                // Group by last names of residents
                $count++;
                $last_names = $constituents_in_house->where('last_name', '<>', null)
                                                    ->groupBy('last_name')
                                                    ->map(function ($last_names) {
                                                        return $last_names->count();
                                                    });
                                                 
                if ($last_names->count() > 2) {


                    $family = "";
                    $loopcount = 0;
                    $total = $last_names->count();
                    foreach ($last_names as $lastname => $lastname_count) {
                        if (strlen($family) > 20) {
                            $family .= '+';
                            continue;
                        }
                        $loopcount++;
                        if ($loopcount == $total) {
                            $family .= $lastname;
                        } else {
                            $family .= $lastname."/";
                        }
                    }
                    if (strlen($family) < 25) {
                        $family .= " Household";
                    }

                } elseif ($last_names->count() == 2) {
                    $family = "";
                    $loopcount = 0;
                    foreach ($last_names as $lastname => $lastname_count) {
                        $loopcount++;
                        if ($loopcount == 2) {
                            $family .= $lastname;
                        } else {
                            $family .= $lastname." and ";
                        }
                    }
                    $family .= " Household";
                } elseif ($last_names->count() == 1) {
                    if ($constituents_in_house->count() > 1) {

                        // One last name, multiple people
                        $family = $constituents_in_house->first()->last_name.' Household';
                    } else {

                        // One person
                        $family = $constituents_in_house->first()->full_name;
                    }
                } else {

                    //For some reason, no last names

                    $family = 'Current Residents';
                }

                // if ($constituents_in_house->first()->person) {
                //     $new_household = new Person;
                // } else {
                //     $new_household = new Voter;
                // }

                $new_household = ($constituents_in_house->first()->person) ? new Person : new Voter;

                $new_household->full_name = $family;
                $new_household->full_address = $household;
                $new_household->mailing_info = $constituents_in_house->first()->mailing_info;
                $new_household->address_number = $constituents_in_house->first()->address_number;
                $new_household->address_fraction = $constituents_in_house->first()->address_fraction;
                $new_household->address_number = $constituents_in_house->first()->address_number;
                $new_household->address_street = $constituents_in_house->first()->address_street;
                $new_household->address_apt = $constituents_in_house->first()->address_apt;
                $new_household->address_city = $constituents_in_house->first()->address_city;
                $new_household->address_state = $constituents_in_house->first()->address_state;
                $new_household->address_zip = $constituents_in_house->first()->address_zip;

                $constituents->push($new_household);
            }
        }

        ////////////////////////////////////////////////////////////////////////

        // Use full_address for mailing_address if latter is blank:

        // Rename because mailing_address is an attribute composed of mailing_info array data

        $constituents = $constituents->keyBy(function ($value, $key) {
            if ($key == 'mailing_address') {
                return 'the_mailing_address';
            } else {
                return $key;
            }
        });

        // If mailing_address attribute is blank, use full_address

        $constituents = $constituents->each(function ($item, $key) {
            if ($item->mailing_address == '' || ! $item->mailing_address) {
                $item['the_mailing_address'] = $item->full_address;
            } else {
                $item['the_mailing_address'] = $item->mailing_address;
            }

            return $item;
        });

        // Change name of mailing_address column name to use in CSV

        $column_names = collect($column_names)->map(function ($value, $key) {
            if ($value == 'mailing_address') {
                return 'the_mailing_address';
            } else {
                return $value;
            }
        });

        ////////////////////////////////////////////////////////////////////////
        // Include Voter File Groups
        //

        if ($include_voter_phones) {
            $column_names[] = 'Voter Home Phone';
            $column_names[] = 'Voter Cell Phone';
        }

        ////////////////////////////////////////////////////////////////////////
        // Interpret Groups to add them as Columns
        //
        // "category_7" => array:1 [▼
        //   0 => "5"
        // ]
        // "category_8" => array:3 [▼
        //   0 => "60"
        //   1 => "60_supports"
        //   2 => "61"
        // ]

        if ($include_groups) {

            if ($include_groups == 'specific') {

                if (isset($input['selected_groups'])) {

                    // Livewire Search Form Method
                    $group_ids = collect($input['selected_groups'])->keys();
                    $groups = \App\Group::whereIn('id',$group_ids)
                                        ->pluck('id')
                                        ->toArray();

                } else {

                    // Original Search Form Method

                    $groups = [];
                    foreach ($input as $maybe_cat_field => $group_data) {
                        if (substr($maybe_cat_field, 0, 9) == 'category_') {
                            foreach ($group_data as $group_string) {
                                $underscore = stripos($group_string, '_');
                                $group_id = ($underscore) ? substr($group_string, 0, $underscore * 1) : $group_string;
                                $groups[] = $group_id;
                            }
                        }
                    }
                    $groups = collect($groups)->unique();

                }


            }

            if ($include_groups == 'all') {

                $groups = \App\Group::where('team_id', Auth::user()->team->id)
                                    // ->whereNull('archived_at') // Include archived groups
                                    ->pluck('id')
                                    ->toArray();

                // Remove groups that apply to no one on the list
                $people_array = $constituents->where('person', true)->pluck('id')->toArray();

                foreach ($groups as $key => $check_group_id) {
                    $pivot = \App\GroupPerson::where('group_id', $check_group_id)
                                             ->where('team_id', Auth::user()->team->id)
                                             ->whereIn('person_id', $people_array)
                                             
                                             ->first();

                    if (!$pivot) unset($groups[$key]);
                }

            }

        }

        if (isset($groups)) {
            $groups_index = [];
            foreach ($groups as $group_id) {
                $group = \App\Group::find($group_id);

                $column_names[] = '{GROUP} '.$group->name;
                $groups_index[count($column_names) - 1] = $group->id; // Remembers GroupID by where it is in column_names array

                if ($group->cat->has_position) {
                    $column_names[] = '__Position';
                }
                $groups_index[count($column_names) - 1] = $group->id;

                if ($group->cat->has_title) {
                    $column_names[] = '__Title';
                }
                $groups_index[count($column_names) - 1] = $group->id;

                if ($group->cat->has_notes) {
                    $column_names[] = '__Notes';
                }
                $groups_index[count($column_names) - 1] = $group->id;
            }
        }

        ////////////////////////////////////////////////////////////////////////

        $constituents = $constituents->each(function ($item, $key) {
            if ($item->mailing_address == '' || ! $item->mailing_address) {
                $item['the_mailing_address'] = $item->full_address;
            } else {
                $item['the_mailing_address'] = $item->mailing_address;
            }

            return $item;
        });

        $column_names = $column_names->toArray();

        ////////////////////////////////////////////////////////////////////////

        fputcsv($file, $column_names);

        $rows = [];
        foreach ($constituents as $constituent) {
            $row = [];
            foreach ($column_names as $key => $column) {

                if (substr($column, 0, 16) == 'Voter Home Phone' 
                    && $constituent->voter
                    && $constituent->voter->home_phone) {

                    $val = $constituent->voter->home_phone;

                } elseif (substr($column, 0, 16) == 'Voter Home Phone' 
                    && $constituent->home_phone) {

                    $val = $constituent->home_phone;

                } elseif (substr($column, 0, 16) == 'Voter Cell Phone' 
                    && $constituent->voter
                    && $constituent->voter->cell_phone) {

                    $val = $constituent->voter->cell_phone;

                } elseif ($constituent->person && substr($column, 0, 7) == '{GROUP}') {
                    $group_id = $groups_index[$key];
                    $pivot = \App\GroupPerson::where('person_id', $constituent->id)
                                             ->where('group_id', $group_id)
                                             ->where('team_id', Auth::user()->team->id)
                                             
                                             ->first();
                    $val = ($pivot) ? 'Member' : null;
                } elseif ($constituent->person && substr($column, 0, 10) == '__Position') {
                    $group_id = $groups_index[$key];
                    $pivot = \App\GroupPerson::where('person_id', $constituent->id)
                                             ->where('group_id', $group_id)
                                             ->where('team_id', Auth::user()->team->id)
                                             
                                             ->first();
                    $val = ($pivot) ? $pivot->position : null;
                } elseif ($constituent->person && substr($column, 0, 7) == '__Title') {
                    $group_id = $groups_index[$key];
                    $pivot = \App\GroupPerson::where('person_id', $constituent->id)
                                             ->where('group_id', $group_id)
                                             ->where('team_id', Auth::user()->team->id)
                                             
                                             ->first();
                    $val = ($pivot) ? $pivot->title : null;
                } elseif ($constituent->person && substr($column, 0, 7) == '__Notes') {
                    $group_id = $groups_index[$key];
                    $pivot = \App\GroupPerson::where('person_id', $constituent->id)
                                             ->where('group_id', $group_id)
                                             ->where('team_id', Auth::user()->team->id)
                                             
                                             ->first();
                    $val = ($pivot) ? $pivot->notes : null;
                } else {
                    $val = $constituent->$column;
                    if (is_array($val)) {
                        $val = json_encode($column);
                    }
                    if (! $val) {
                        $val = '';
                    }
                }
                $row[] = "$val";
            }
            if (count($row) != count($column_names)) {
                dd($constituent, $row, $column_names);
            }
            $rows[] = $row;
        }
        foreach ($rows as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        return ['headers'       => $headers,
                'filename_full' => $filename_full,
                'filename'      => $filename];
    }
}
