<?php

namespace App\Console\Commands\Admin;

use App\Voter;
use Carbon\Carbon;
use DB;
use Faker\Factory as Faker;
use Illuminate\Console\Command;

class CreateFakeSliceFromVoterFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:newbridge {--slice=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a fake slice for recording videos.';

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
     * @return mixed
     */
    public function handle()
    {
        $faker = Faker::create();

        $fake_town_name = 'Newbridge';

        $at_a_time = 100;
        $count = 0;
        $limit = 0;

        while (! $limit) {
            $limit = $this->ask('How many voters do you want to create?');
            if (! $limit || ! is_numeric($limit)) {
                $this->info('Pick a number, Chief');
                $limit = null;
            }
        }

        if (! $limit) {
            $limit = 40000;
        }

        $new_slice = 'x_XX_'.$fake_town_name;
        $statewide_slice = 'x_MA_STATE';
        DB::statement('CREATE TABLE IF NOT EXISTS `'.$new_slice.'` LIKE '.$statewide_slice);

        session()->put('team_table', $statewide_slice);
        $fields_to_insert = collect(Voter::first())->keys();

        $max = Voter::where('house_district', 173)->count();
        if ($limit > $max) {
            $limit = $max;
        }

        // dd($fields_to_insert);

        while ($count < $limit) {
            session()->put('team_table', $statewide_slice);
            $originals = Voter::where('house_district', 173)
                              ->skip($count)
                              ->take($at_a_time)
                              ->get();

            session()->put('team_table', $new_slice);
            foreach ($originals as $original) {
                if ($count + 1 > $limit) {
                    continue;
                }

                $state_prefix = 'XX_'; //substr($value, 0, 3);
                $the_id = substr($original->id, 3);
                $new_id = base64_encode($the_id);
                $idvalue = $state_prefix.$new_id;
                $count++;
                if (Voter::where('id', $idvalue)->exists()) {
                    continue;
                }

                $the_gender = ($original->gender == 'M') ? 'male' : 'female';

                $new = new Voter;

                foreach ($fields_to_insert as $field) {
                    if (! $original->$field) {
                        continue;
                    } //Do not do anything if null

                    switch ($field) {

                        case 'import_order':
                            $value = $count;
                            break;

                        case 'id':
                            // Scrambles the Voter ID

                            $value = $original->$field;
                            $state_prefix = 'XX_'; //substr($value, 0, 3);
                            $the_id = substr($value, 3);

                            // $new_id = null;
                            // foreach(str_split($the_id) as $char) {
                            //     if (!is_numeric($char)) {
                            //         $new_id .= strtoupper($faker->randomLetter);
                            //     } else {
                            //         $new_id .= rand(0,9);
                            //     }
                            // }

                            $new_id = base64_encode($the_id);
                            $value = $state_prefix.$new_id;

                            break;

                        case 'dob':
                            $value = $original->$field;
                            $value = Carbon::parse($value)->addDays(rand(1, 10));
                            break;

                        case 'emails':
                            $emails = $original->$field;
                            $value = [];
                            foreach ($emails as $email) {
                                $value[] = $faker->email;
                            }
                            // $value = json_encode($value);
                            break;

                        case 'first_name':
                            $value = $faker->firstName($gender = $the_gender);
                            break;

                        case 'last_name':
                            $value = $faker->lastName;
                            break;

                        case 'middle_name':
                            $value = strtoupper($faker->randomLetter);
                            break;

                        case 'cell_phone':
                            $value = $faker->phoneNumber;
                            break;

                        case 'home_phone':
                            $value = $faker->phoneNumber;
                            break;

                        case 'spouse_name':
                            $value = $faker->name;
                            break;

                        case 'business_info':
                            //{"mcrc16":0,"occupation":"HOMEMAKER","work_phone":null,"work_phone_ext":null,"fax":null,"name":null,"address_1":null,"address_2":null,"city":null,"state":null,"zip":null,"zip4":null,"web":null}
                            $biz = $original->$field;
                            if ($biz['work_phone']) {
                                $biz['work_phone'] = $faker->phoneNumber;
                            }
                            if ($biz['fax']) {
                                $biz['fax'] = $faker->phoneNumber;
                            }
                            $biz['work_phone_ext'] = null;
                            $value = $biz;
                            break;

                        default:
                            $value = $original->$field;
                            break;
                    }

                    $new->$field = $value;
                }

                unset($new->voter_id);
                $new->save();

                // Progress Bar
                $percent_complete = round(($count / $limit * 100), 0);
                $bar_length = $percent_complete * .01 * 50;
                echo str_repeat('*', $bar_length);
                if (50 - $bar_length > 0) {
                    echo str_repeat('_', 50 - $bar_length);
                }
                echo ' '.number_format($count).' '.$percent_complete.'%'."\r";
            }
        }

        echo "\n";
        $this->info('All set, Chief');
    }
}
