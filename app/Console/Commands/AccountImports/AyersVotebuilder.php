<?php

namespace App\Console\Commands\AccountImports;

use Illuminate\Console\Command;

class AyersVotebuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:ayers_votebuilder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Spreadsheet from Ayers with lots of stuff from VAN';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folder = storage_path().'/app/csvs';

        $csv_master = $this->getFullData($folder."/ayers-votebuilder-master.csv");
        $csv_acs    = $this->getFullData($folder."/ayers-votebuilder-acs.csv");
        $csv_notes  = $this->getFullData($folder."/ayers-votebuilder-notes.csv");

        $people = [];
        foreach ($csv_master as $master_index => $master_row) {
            $vanid = $master_row['Voter File VANID'];
            if (!isset($people[$vanid])) {
                $person_arr = [];
                $person_arr['first_name']     = $master_row['FirstName'];
                $person_arr['middle_name']    = $master_row['MiddleName'];
                $person_arr['last_name']      = $master_row['LastName'];
                $person_arr['suffix']         = $master_row["Suffix"];
                $person_arr['sex']            = $master_row["Sex"];
                $person_arr['party']          = $master_row["Party"];
                $person_arr['age']            = $master_row["Age"];
                $person_arr['address_street'] = $master_row["Address"];
                $person_arr['address_city']   = $master_row["City"];
                $person_arr['address_state']  = $master_row["State"];
                $person_arr['address_zip']    = str_pad($master_row["Zip5"], 5, '0', STR_PAD_LEFT);
                $person_arr['primary_email']  = $master_row["PreferredEmail"];
                $person_arr['primary_phone']  = $master_row["Preferred Phone"];
                $people[$vanid] = $person_arr;
            }
            $colcount = 0;
            $groups = [];
            foreach ($master_row as $master_key => $master_val) {
                $colcount++;
                if ($colcount > 18) {
                    // Groups start here
                    if ($master_val) {
                        $groupname = str_replace('_(HD096__Ayers)', '', $master_key);
                        $groupname = str_replace('_', ' ', $groupname);
                        $groups[$groupname] = [];
                    }
                }
            }
            $people[$vanid]['groups'] = $groups;
        }

        $fixes = [
        ];

        foreach ($csv_acs as $acs_index => $acs_row) {
            $vanid = $acs_row['Voter File VANID'];
            if ($people[$vanid]) {
                $people_groups = $people[$vanid]['groups'];
                $groupname = $acs_row['ActivistCodeName'];
                $groupname = str_replace(['-', "'"], '', $groupname);
                foreach ($fixes as $bad => $good) {
                    if ($groupname == $bad) {
                        $groupname = $good;
                    }
                }
                if (isset($people_groups[$groupname])) {
                    //dd($acs_row, $people[$vanid]);
                    $group_arr = [];
                    $group_arr['van_category'] = $acs_row['ActivistCodeType'];
                    $group_arr['note'] = $acs_row['ActivistCodeDescription'];
                    $group_arr['date'] = $acs_row['DateCanvassed'];
                    $group_arr['user'] = $acs_row['CanvassedBy'];
                    //dd($group_arr);
                    $people[$vanid]['groups'][$groupname][] = $group_arr;
                    //dd($people[$vanid]);
                } else {
                    dd($people[$vanid], $acs_row, "Missing group");
                }
            } else {
                dd($acs_row, "Missing van id");
            }
        }

        foreach ($csv_notes as $note_row) {
            $vanid = $note_row['Voter File VANID'];
            if (!isset($people[$vanid])) {
                dd($note_row);
            }
            if ($note_row['NoteCategory']) {
                dd($note_row);
            }
            if ($note_row['NoteTags']) {
                dd($note_row);
            }
            $note_arr = [];
            $note_arr['date'] = $note_row['DateEntered'];
            $note_arr['subject'] = $note_row['ContactName'];
            $note_arr['notes'] = $note_row['NoteText'];
            $note_arr['user'] = $note_row['EnteredBy'];
            $people[$vanid]['notes'][] = $note_arr;
        }
        foreach ($people as $vanid => $person) {
            echo $person['first_name']." ".$person['last_name'].' '.$person['address_street']."\n";
            if (isset($person['groups'])) {
                echo "\t\t".count($person['groups'])." Groups\n";
            }
            if (isset($person['notes'])) {
                echo "\t\t".count($person['notes'])." Notes\n";
            }
        }
        return Command::SUCCESS;
    }

    public function getFullData($file)
    {
        try {
            $row = 1;
            $full_data = [];
            $fields = [];
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

                    if ($row == 1) {
                        foreach ($data as $tempkey => $val) {
                            $data[$tempkey] = trim($val);
                        }
                        $fields = $data;
                        //dd($fields);
                        $row++;
                        continue;
                    }
                    
                    $onerow = [];
                    foreach ($data as $key => $val) {
                        $onerow[$fields[$key]] = trim($val);
                    }
                    $full_data[] = $onerow;
                    $row++;
                    
                }
                //dd($full_data);
                fclose($handle);
            }
        } catch (\Exception $e) {
            dd($full_data, $fields, $row, $data, $e->getMessage());
        }
        return $full_data;
    }
}
