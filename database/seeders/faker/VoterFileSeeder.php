<?php

namespace Database\Seeders\Faker;

use App\Models\Admin\DataFolder;
use App\Models\Admin\DataImport;
// use Database\Seeds\DatabaseSeeder;
use App\Models\Admin\DataJob;
use App\Team;
use App\Voter;
use App\VotingHousehold;
use Database\Seeders\Faker\VoterFileSeeder;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class VoterFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $n = 1;

        ////////////////////////////////// DATA FOLDERS //////////////////////////////

        $folder = new DataFolder;
        $folder->name = 'Massachusetts';
        $folder->save();

        $folder = new DataFolder;
        $folder->name = 'Connecticut';
        $folder->save();

        $folder = new DataFolder;
        $folder->name = 'Maryland';
        $folder->save();

        $folder = new DataFolder;
        $folder->name = 'Rhode Island';
        $folder->save();

        $folder = new DataFolder;
        $folder->name = 'New York';
        $folder->save();

        //////////////////////////// DATA_IMPORT TEMPLATES ////////////////////////////

        // $template = new DataImport('t',$team_id = 1, "SOS Format One Town");

        // $template->header_columns = '["{SKIP}","id","last_name","first_name","middle_name","name_title","address_number","address_fraction","address_street","address_apt","address_zip","{SKIP}","{SKIP}","{SKIP}","{SKIP}","{SKIP}","party","gender","{DATETIME} registration_date","{DATETIME} dob","{SKIP}","{SKIP}","congress_district","senate_district","house_district","voter_status"]';

        // $template->save();

        //////////////////////////// VOTER FILE ////////////////////////////

        $import = new DataImport('v', $team_id = 1, 'Massachusetts Statewide');

        $import->header_columns = '["{SKIP}","id","last_name","first_name","middle_name","name_title","address_number","address_fraction","address_street","address_apt","address_zip","{SKIP}","{SKIP}","{SKIP}","{SKIP}","{SKIP}","party","gender","{DATETIME} dob","{DATETIME} registration_date","ward","precinct","congress_district","senate_district","house_district","voter_status"]';

        $import->extra_columns = '{"address_city":"CHELMSFORD","address_state":"MA","state":"MA"}';

        $import->skip_first = 0;

        $file_name = 'CHELMSFORD-4000.txt';
        $path = base_path().'/database/seeds/voterfiles/'.$file_name;
        // $file_name = 'CHELMSFORD-25000.txt';
        // $path = base_path().'/database/seeds/voterfiles_old/'.$file_name;

        $import->file_path = $path;
        $import->delimiter = '|';
        $import->file_stored = 1;
        $import->file_hash = md5_file($path);
        $import->notes = 'Really just the town of Chelmsford right now.';
        $import->save();

        (new DataJob)->add('import', $import->id);
        (new DataJob)->add('enrich', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        (new DataJob)->add('ready', $import->id);
        (new DataJob)->add('deploy', $import->id);
        (new DataJob)->add('deployHouseholds', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// COPY  ////////////////////////////

        $import = new DataImport('v', $team_id = 1, 'Exact Copy');
        $import->save();

        $arguments = ['slice_of_id'    => 1,
                           'slice_sql'      => 'first_name <> "Borke W. Phillipides Jr."',
                            ];

        (new DataJob)->add('defineSlice', $import->id, $arguments);
        (new DataJob)->add('populateSlice', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        // (new DataJob)->add('createHouseholdsBySlice', $import->id, $arguments);
        (new DataJob)->add('ready', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// BTU FILE ////////////////////////////

        $import = new DataImport('v', $team_id = 3, 'BTU 100');

        $import->header_columns = '["{SKIP}","id","last_name","first_name","middle_name","name_title","address_number","address_fraction","address_street","address_apt","address_zip","{SKIP}","{SKIP}","{SKIP}","{SKIP}","{SKIP}","party","gender","{DATETIME} dob","{DATETIME} registration_date","ward","precinct","congress_district","senate_district","house_district","voter_status"]';

        $import->extra_columns = '{"address_city":"NORTH ADAMS","address_state":"MA","state":"MA"}';

        $import->skip_first = 0;

        $file_name = 'LORDEGUVA-100.txt';
        $path = base_path().'/database/seeds/voterfiles/'.$file_name;

        $import->file_path = $path;
        $import->delimiter = '|';
        $import->file_stored = 1;
        $import->file_hash = md5_file($path);
        $import->notes = 'For testing merges';
        $import->save();

        (new DataJob)->add('import', $import->id);
        (new DataJob)->add('enrich', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        (new DataJob)->add('ready', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// TORRES FILE ////////////////////////////

        $import = new DataImport('v', $team_id = 3, 'TORRES 150');

        $import->header_columns = '["{SKIP}","id","last_name","first_name","middle_name","name_title","address_number","address_fraction","address_street","address_apt","address_zip","{SKIP}","{SKIP}","{SKIP}","{SKIP}","{SKIP}","party","gender","{DATETIME} dob","{DATETIME} registration_date","ward","precinct","congress_district","senate_district","house_district","voter_status"]';

        $import->extra_columns = '{"address_city":"SCITUATE","address_state":"MA","state":"MA"}';

        $import->skip_first = 0;

        $file_name = 'TORRES-150.txt';
        $path = base_path().'/database/seeds/voterfiles/'.$file_name;

        $import->file_path = $path;
        $import->delimiter = '|';
        $import->file_stored = 1;
        $import->file_hash = md5_file($path);
        $import->notes = 'For testing merges';
        $import->save();

        (new DataJob)->add('import', $import->id);
        (new DataJob)->add('enrich', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        (new DataJob)->add('ready', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// MERGE BTU INTO COPY ////////////////////////////

        $arguments = ['merge_this_id' => 3,
                      'merge_into_id' => 2, ];

        (new DataJob)->add('notReady', 2);
        (new DataJob)->add('merge', 2, $arguments);
        (new DataJob)->add('clearHouseholds', 2);
        (new DataJob)->add('createHouseholds', 2);
        (new DataJob)->add('ready', 2);

        //////////////////////////// MERGE TORRES INTO COPY ////////////////////////////

        $arguments = ['merge_this_id' => 4,
                      'merge_into_id' => 2, ];

        (new DataJob)->add('notReady', 2);
        (new DataJob)->add('merge', 2, $arguments);
        (new DataJob)->add('clearHouseholds', 2);
        (new DataJob)->add('createHouseholds', 2);
        (new DataJob)->add('ready', 2);

        //////////////////////////// VOTER FILE ////////////////////////////

        $import = new DataImport('v', $team_id = 4, 'Chelmsford Catherines');
        $import->save();

        $arguments = ['slice_of_id'    => 1,
                           'slice_sql'      => 'first_name="Catherine" or last_name="Catherine"',
                            ];

        (new DataJob)->add('defineSlice', $import->id, $arguments);
        (new DataJob)->add('populateSlice', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        // (new DataJob)->add('createHouseholdsBySlice', $import->id, $arguments);
        (new DataJob)->add('ready', $import->id);
        (new DataJob)->add('deploy', $import->id);
        (new DataJob)->add('deployHouseholds', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// VOTER FILE ////////////////////////////

        $import = new DataImport('v', $team_id = 5, 'Connecticut Statewide');
        $import->save();

        $arguments = ['slice_of_id'    => 1,
                           'slice_sql'      => 'gender="F"',
                            ];

        (new DataJob)->add('defineSlice', $import->id, $arguments);
        (new DataJob)->add('populateSlice', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        // (new DataJob)->add('createHouseholdsBySlice', $import->id, $arguments);
        (new DataJob)->add('ready', $import->id);
        (new DataJob)->add('deploy', $import->id);
        (new DataJob)->add('deployHouseholds', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// VOTER FILE ////////////////////////////

        $import = new DataImport('v', $team_id = 2, 'House District 133');
        $import->save();

        $arguments = ['slice_of_id'    => 1,
                           'slice_sql'      => 'house_district=133',
                            ];

        (new DataJob)->add('defineSlice', $import->id, $arguments);
        (new DataJob)->add('populateSlice', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        (new DataJob)->add('ready', $import->id);
        (new DataJob)->add('deploy', $import->id);
        (new DataJob)->add('deployHouseholds', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// VOTER FILE ////////////////////////////

        $import = new DataImport('v', $team_id = 3, 'Rep Morrison House District 119');
        $import->save();

        $arguments = ['slice_of_id'    => 1,
                           'slice_sql'      => 'house_district=119',
                            ];

        (new DataJob)->add('defineSlice', $import->id, $arguments);
        (new DataJob)->add('populateSlice', $import->id);

        // MERGE IN OTHER FILES FOR MORE CITIES
        $arguments = ['merge_this_id' => 3, 'merge_into_id' => $import->id];
        (new DataJob)->add('merge', $import->id, $arguments);
        $arguments = ['merge_this_id' => 4, 'merge_into_id' => $import->id];
        (new DataJob)->add('merge', $import->id, $arguments);

        (new DataJob)->add('createHouseholds', $import->id);
        (new DataJob)->add('ready', $import->id);
        (new DataJob)->add('deploy', $import->id);
        (new DataJob)->add('deployHouseholds', $import->id);

        echo 'Jobs queued for voterfile #'.$n++.' ';
        $this->command->info($import->name);

        //////////////////////////// VOTER FILE ////////////////////////////

        // echo "Making a few copies for testing...\r\n";

        // $copy = DataImport::find(3);

        // $arguments = array('copy_of_id'    => 3);
        // (new DataJob)->add('copy', $copy->id), $arguments;
        // (new DataJob)->add('createHouseholds', $copy->id);

        // (new DataJob)->add('runFakerSeeder', 0);
    }
}
