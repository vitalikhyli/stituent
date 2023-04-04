<?php

namespace Database\Seeders\Faker;

use App\Account;
use App\BulkEmail;
use App\BulkEmailQueue;
// use Database\SeedsDatabaseSeeder;

use App\CaseFile;
use App\CasePerson;
use App\Category;
use App\Contact;
use App\ContactEntity;
use App\ContactPerson;
use App\Entity;
use App\EntityPerson;
use App\Group;
use App\GroupPerson;
use App\HistoryItem;
use App\Household;
use App\Models\Admin\AdminHistoryItem;
use App\Models\Pilot\PilotBeneficiary;
use App\Models\Pilot\PilotItem;
use App\Models\Pilot\PilotProgram;
use App\Models\Pilot\PilotReport;
use App\Person;
use App\Relationship;
use App\Team;
use App\User;
use App\Voter;
use App\VoterSlice;
use App\VotingHousehold;
use App\WorkCase;
use App\WorkFile;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Faker\FakerSeeder;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FakerSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function PopulateGroupInstanceData($data, $faker)
    {
        if (! $data) {
            return null;
        }

        $new_data = [];
        foreach ($data as $key => $datum) {
            if ($key == 'notes') {
                $new_data = array_merge($new_data, [$key => $faker->realText($maxNbChars = rand(40, 100), $indexSize = 2)]);
            }
        }

        $new_data = json_encode($new_data);

        return $new_data;
    }

    public function run($neu_mode = null)
    {
        $faker = Faker::create();
        $s = new DatabaseSeeder;
        $pro = 0;

        global $neu_mode;

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Teams and Users
        //

        $this->command->info('Faking Users...');
        //$teams = \App\Team::factory()->count(2)->create();
        $users = \App\User::factory()->count(10)->create();
        $pro = $s->ProgressBar($pro, $users->count(), 'Users', 'static');

        foreach (Team::all() as $theteam) {
            $slice = VoterSlice::inRandomOrder()->take(1)->first();
            $theteam->db_slice = $slice->name;
            $theteam->save();
        }

        $this->command->info('Faking Teams');

        $teams_all = ($neu_mode) ? Team::where('id', 2)->get() : Team::all();

        foreach ($teams_all as $theteam) {

            //Assign Ownership of Team
            $firstInTeam = User::where('current_team_id', $theteam->id)->first();
            if ($firstInTeam) {
                $theteam->owner_id = $firstInTeam->id;
                $theteam->save();
            } else {
                $firstInTeam = User::find(1);
            }
            foreach (User::where('current_team_id', $theteam->id)->get() as $theuser) {
                if (! DB::table('team_user')->where('team_id', $theteam->id)
                                          ->where('user_id', $theuser->id)
                                          ->exists()) {
                    DB::table('team_user')->insert([
                        'team_id' => $theteam->id, 'user_id' => $theuser->id,
                    ]);
                    DB::table('permissions')->insert([
                        'team_id'           => $theteam->id,
                        'user_id'           => $theuser->id,
                        'title'             => $faker->randomElement(['Legislator', 'Staff', 'Chief of Staff']),
                        'developer'         => false,
                        'admin'             => false,
                        'constituents'      => $faker->boolean,
                        'campaign'          => $faker->boolean,
                        'reports'           => $faker->boolean,
                        'metrics'           => $faker->boolean,
                        'chat'              => true,
                        'chat_external'     => $faker->boolean,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                    //echo "team_user ".$theteam->id.' '.$theuser->id."\n";
                }
            }

            $pro = $s->ProgressBar($pro, $teams_all->count(), 'Teams', '');
        }

        // $this->call([
        //     AccountsAndUsersSeeder::class,
        // ]);

        ////////////////////////////////////////////////////////////////////////////////
        //
        // People
        //

        $this->command->info('Creating People from Voter File');

        $total_people = 0;

        $teams_all = ($neu_mode) ? Team::where('id', 2)->get() : Team::all();

        foreach ($teams_all as $theteam) {

        // if (Schema::hasTable('x_voters_'.str_pad($theteam->id, 4, '0', STR_PAD_LEFT))) {
            //     session(['team_table' => 'x_voters_'.str_pad($theteam->id, 4, '0', STR_PAD_LEFT)]);
            // } else {
            //     session(['team_table' => 'x_voters_0001']);
            // }

            session(['team_table' => $theteam->db_slice]);

            $voters_size = rand(100, 300); //rand(100,Voter::count());
            $voters = Voter::inRandomOrder()->limit($voters_size)->get();
            foreach ($voters as $thevoter) {
                $person = new Person;
                $team_id = $theteam->id;
                $person->team_id = $team_id;

                // Enriched Data:
                $person->full_address = $thevoter->full_address;
                $person->household_id = $thevoter->household_id;
                $person->full_name = titleCase($thevoter->full_name);
                $person->full_name_middle = titleCase($thevoter->full_name_middle);

                // Basic VoterFile Data:
                if (rand(0, 100) >= 3) {
                    //Fake 3% of people as unlinked
                    $person->voter_id = $thevoter->id;
                }
                $person->name_title = ucwords(strtolower($thevoter->name_title));
                $person->first_name = titleCase($thevoter->first_name);
                $person->middle_name = titleCase($thevoter->middle_name);
                $person->last_name = titleCase($thevoter->last_name);
                $person->address_number = ucwords(strtolower($thevoter->address_number));
                $person->address_fraction = ucwords(strtolower($thevoter->address_fraction));
                $person->address_street = ucwords(strtolower($thevoter->address_street));
                $person->address_city = ucwords(strtolower($thevoter->address_city));
                $person->address_state = strtoupper($thevoter->address_state);
                $person->address_zip = $thevoter->address_zip;
                $person->gender = $thevoter->gender;
                $person->party = $thevoter->party;
                $person->dob = $thevoter->dob;

                $person->master_email_list = $faker->boolean;
                $person->primary_email = $thevoter->safeEmail;
                $person->work_email = $thevoter->safeEmail;

                // Emails JSON
                $emails = [];
                for ($i = 0; $i <= rand(0, 2); $i++) {
                    ($i == 0) ? $main = 1 : $main = 0;
                    $emails[] = [
                                'email' => $faker->safeEmail,
                                'main' => $main,
                                'notes' => $faker->randomElement(['personal', 'work', 'official']),
                                ];
                }
                $person->other_emails = json_encode($emails);

                $person->primary_phone = $thevoter->phoneNumber;
                // Phones JSON
                $phones = [];
                for ($i = 0; $i <= rand(0, 2); $i++) {
                    ($i == 0) ? $main = 1 : $main = 0;
                    $phones[] = [
                                'phone' => $faker->phoneNumber,
                                'main' => $main,
                                'notes' => $faker->randomElement(['personal', 'work', 'official']),
                                ];
                }
                $person->other_phones = json_encode($phones);

                $person->social_twitter = '@'.str_replace(' ', '_', strtolower($thevoter->first_name)).$faker->randomDigit;

                $person->private = titleCase($thevoter->first_name).' is a '.$faker->jobTitle.' and her favorite number is '.rand(0, 100).'. We first met in '.$faker->city.', '.$faker->stateAbbr.'.';

                $person->save();

                //Master Email List Group
                if ($person->master_email_list) {
                    $master_email_list_group = Group::where('team_id', $theteam->id)
                                                ->where('name', 'Master List')
                                                ->first();

                    if ($master_email_list_group) {
                        DB::table('group_person')->insert(
                        ['team_id'       => $theteam->id,
                         'person_id'     => $person->id,
                         'group_id'      => $master_email_list_group->id, ]
                    );
                    }
                }

                // Fake Voter Support
                if ($person->team->app_type == 'campaign') {
                    $support_options = [1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 5, 5]; //To fake more 1s than 5s
                    $person->support = [
                                    'campaign_1' => $support_options[array_rand($support_options)],
                                    'campaign_2' => rand(1, 5),
                                    'campaign_3' => rand(1, 5),
                                ];
                    $person->save();

                    // // Add 70% to Universe
                // if (rand(0,10) <= 7) {
                //     $universemember = new \App\UniverseMember;
                //     $universemember->member_id = $person->voter_id;
                //     $universemember->campaign_id = 1;
                //     $universemember->save();
                // }
                }

                $total_people = $total_people + 1;
                $pro = $s->ProgressBar($pro, $voters->count(), 'People for Team '.$team_id.' / ('.$theteam->app_type.')', '');
            }
        }

        $this->command->info('(Total Person Records = '.number_format($total_people, 0, '.', ',').')');

        ////////////////////////////////////////////////////////////////////////////////
        //
        // BULK EMAIL
        //

        $this->command->info('Bulk Emails');
        $j = 0;

        $teams = ($neu_mode) ? Team::where('id', 2)->get() : Team::all();

        $path = base_path().'/database/seeds/emails/fakeemail.txt';
        $fake_content = file_get_contents($path); //fopen($$path, 'r');

        foreach ($teams as $theteam) {
            for ($i = 0; $i < 10; $i++) {
                $userids = DB::table('team_user')->select('user_id')
                                                 ->where('team_id', $theteam->id)
                                                 ->get()
                                                 ->pluck('user_id')
                                                 ->toArray();
                if ($userids) {
                    $email = new BulkEmail;
                    $email->team_id = $theteam->id;
                    $email->user_id = $faker->randomElement($userids);
                    $email->name = $faker->randomElement(['Annual', 'Weekly', 'Daily', 'Random', 'Regular', 'Impromput', 'Special', 'Emergency']).' '.$faker->randomElement(['legislative', 'community', 'event', 'neighborhood', 'city', 'rural', 'activist', 'winter', 'spring', 'summer', 'fall', 'holiday']).' '.$faker->randomElement(['newsletter', 'update', 'report', 'letter', 'invitation', 'broadcast']);
                    $email->subject = $faker->realText($maxNbChars = 45, $indexSize = 1);
                    $email->content = $fake_content;
                    $email->sent_from = $faker->name;
                    $email->sent_from_email = $faker->safeEmail;
                    $email->started_at = Carbon::now();
                    if (rand(0, 1) == 1) {
                        $email->completed_at = Carbon::now()->setTimezone('UTC')->subDays(rand(0, 120));
                        $email->queued = 1;
                    }
                    $email->save();
                }
            }

            $pro = $s->ProgressBar($pro, $teams->count(), 'Email for Team '.$theteam->id, '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // EMAIL QUEUES
        //

        $this->command->info('Bulk Email Queues');
        $j = 0;

        $teams = ($neu_mode) ? Team::where('id', 2)->get() : Team::all();

        foreach ($teams as $theteam) {
            $bulk_emails = BulkEmail::where('team_id', $theteam->id)
                                    ->where('queued', 1)
                                    ->take(3)
                                    ->get();

            foreach ($bulk_emails as $theemail) {
                $people = Person::inRandomOrder()
                                ->where('team_id', $theemail->team_id)
                                ->get();

                foreach ($people as $person) {
                    $queue = new BulkEmailQueue;
                    $queue->person_id = $person->id;
                    $queue->bulk_email_id = $theemail->id;
                    $queue->email = $person->email;
                    $queue->team_id = $theteam->id;
                    $queue->save();
                }
            }

            $pro = $s->ProgressBar($pro, $teams->count(), 'Bulk Email Queue', '');
        }

        //////////////////////////////////////////////////////////////////////////////
        //
        // Households
        //

        $this->command->info('Building Households from People');
        $j = 0;

        $teams = ($neu_mode) ? Team::where('id', 2)->get() : Team::all();

        foreach ($teams as $theteam) {
            $households = DB::table('people')
                            ->select(DB::raw('any_value(team_id) as team_id'),
                                     DB::raw('any_value(full_address) as full_address'),
                                     DB::raw('group_concat(id) as residents'),
                                     DB::raw('count(distinct id) as residents_count'),
                                     'household_id'
                                    )
                            ->where('team_id', $theteam->id)
                            ->groupBy('household_id')
                            ->get();
            $j += $households->count();
            foreach ($households as $thehousehold) {
                if (! $thehousehold->household_id) {
                    //dd($thehousehold);
                } else {
                    $new_household = new Household;
                    $new_household->team_id = $thehousehold->team_id;
                    $new_household->id = $thehousehold->household_id;
                    $new_household->full_address = $thehousehold->full_address;

                    $res_arr = explode(',', $thehousehold->residents);
                    $res_arr_final = [];
                    foreach ($res_arr as $res) {
                        $res_arr_final[] = $res * 1; //To avoid quotes around numbers
                    }
                    $new_household->residents = json_encode($res_arr_final);

                    $new_household->residents_count = $thehousehold->residents_count;
                    $new_household->save();

                    // if (Schema::hasTable('x_households_'.str_pad($theteam->id, 4, '0', STR_PAD_LEFT))) {
                    //     session(['team_households_table' => 'x_households_'.str_pad($theteam->id, 4, '0', STR_PAD_LEFT)]);
                    // } else {
                    //     session(['team_households_table' => 'x_households_0001']);
                    // }

                    session(['team_households_table' => $theteam->db_slice.'_hh']);

                    $new_household->updateTotalResidents();
                }
            }
            $pro = $s->ProgressBar($pro, $teams->count(), 'Teams -> '.$j.' Households', '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Group to People Pivot
        //

        $this->command->info('Assigning Groups to People...');
        $j = 0;
        $people = Person::all();

        foreach ($people as $theperson) {
            $groups = Group::inRandomOrder()
                           ->where('team_id', $theperson->team_id)
                           ->where('name', '<>', 'master list');

            if ($theperson->official === 1) {
                $groups = $groups->where('preset', 'office');
            }
            if ($theperson->official === 0) {
                $groups = $groups->where('preset', 'campaign');
            }

            $groups = $groups->limit(rand(0, 3))->get();

            if ($groups) {
                // $j += $groups->count();
                foreach ($groups as $thegroup) {
                    $pivot = new GroupPerson;
                    $pivot->team_id = $theperson->team_id;
                    $pivot->person_id = $theperson->id;
                    $pivot->group_id = $thegroup->id;
                    $thecategory = Category::where('id', $thegroup->category_id)->first();
                    $pivot->position = $faker->RandomElement(['support', 'oppose', 'undecided', null]);

                    if ($thecategory) {
                        $pivot->data = $this->PopulateGroupInstanceData($thecategory->data_template, $faker); //Default JSON data
                    }

                    $j++;

                    $pivot->save();
                }
            }
            $pro = $s->ProgressBar($pro, $people->count(), 'People -> '.$j.' Groups', '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Files
        //
        $this->command->info('Faking Files...');
        $cases = \App\WorkFile::factory()->count(50)->create();
        $pro = $s->ProgressBar($pro, $cases->count(), 'Files', 'static');

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Cases
        //
        $this->command->info('Faking Cases...');
        $avg_people_per_team = round(Person::select(DB::raw('count(*) as c'))->groupBy('team_id')->get()->avg('c'), 0);
        $num_cases_to_fake = $avg_people_per_team * .1 * Team::all()->count();
        $cases = \App\WorkCase::factory()->count($num_cases_to_fake)->create();

        $pro = $s->ProgressBar($pro, $cases->count(), 'Cases', 'static');

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Cases to People Pivot
        //

        $this->command->info('Assigning People to Cases...');
        $j = 0;
        $cases = WorkCase::all();
        foreach ($cases as $thecase) {
            $people = Person::inRandomOrder()
                            ->where('team_id', $thecase->team_id)
                            ->limit(rand(1, 3))
                            ->get();
            if ($people) {
                $j += $people->count();
                foreach ($people as $theperson) {
                    $pivot = new CasePerson;
                    $pivot->team_id = $thecase->team_id;
                    $pivot->person_id = $theperson->id;
                    $pivot->case_id = $thecase->id;
                    $pivot->save();
                }
            }
            $pro = $s->ProgressBar($pro, $cases->count(), 'Cases -> '.$j.' People', '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Cases to HOUSEHOLDS Pivot
        //

        $this->command->info('Assigning Households to Cases...');
        $j = 0;
        $cases = WorkCase::all();
        foreach ($cases as $thecase) {
            $hh = Household::inRandomOrder()
                            ->where('team_id', $thecase->team_id)
                            ->take(1)
                            ->get();
            if ($hh) {
                $j += $hh->count();
                foreach ($hh as $thehh) {
                    DB::table('case_household')->insert(
                        ['team_id'          => $thecase->team_id,
                         'household_id'     => $thehh->id,
                         'case_id'          => $thecase->id, ]
                    );
                }
            }
            $pro = $s->ProgressBar($pro, $cases->count(), 'Cases -> '.$j.' Households', '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Case_File
        //

        $this->command->info('Assigning Files to Cases...');
        $j = 0;
        $cases = WorkCase::all();
        foreach ($cases as $thecase) {
            $files = WorkFile::inRandomOrder()->where('team_id', $thecase->team_id)->limit(rand(0, 3))->get();
            if ($files) {
                $j += $files->count();
                foreach ($files as $thefile) {
                    $pivot = new CaseFile;
                    $pivot->team_id = $thecase->team_id;
                    $pivot->case_id = $thecase->id;
                    $pivot->file_id = $thefile->id;
                    $pivot->save();
                }
            }
            $pro = $s->ProgressBar($pro, $cases->count(), 'Cases <- '.$j.' Files', '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Contacts
        //

        $this->command->info('Faking Contacts...');

        $avg_people_per_team = round(Person::select(DB::raw('count(*) as c'))->groupBy('team_id')->get()->avg('c'), 0);
        $num_contacts_to_fake = $avg_people_per_team * .4 * Team::all()->count();
        $contacts = \App\Contact::factory()->count($num_contacts_to_fake)->create();
        $pro = $s->ProgressBar($pro, $contacts->count(), 'Contacts', 'static');

        ////////////////////////////////////////////////////////////////////////////////
        //
        // Contacts to People Pivot
        //

        $this->command->info('Assigning People to Contacts...');
        $j = 0;
        $contacts = Contact::all();
        foreach ($contacts as $thecontact) {
            $people = Person::inRandomOrder()->where('team_id', $thecontact->team_id);
            $people = $people->limit(rand(1, 2))->get();
            if ($people) {
                $j += $people->count();
                foreach ($people as $theperson) {
                    $pivot = new ContactPerson;
                    $pivot->team_id = $thecontact->team_id;
                    $pivot->person_id = $theperson->id;
                    $pivot->contact_id = $thecontact->id;
                    $pivot->save();
                }
            }
            $pro = $s->ProgressBar($pro, $contacts->count(), 'Contacts -> '.$j.' People', '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // History
        //

        $this->command->info('Faking Team Histories');
        $teams_all = Team::all();
        $num_cases_open = 31;
        $rand_inc = [-4, -2, -2, -1, -1, -1, 0, 0, 0, 0, 1, 1, 1, 2, 2];

        foreach ($teams_all as $theteam) {
            $num_people = Person::where('team_id', $theteam->id)->count();

            $the_date = Carbon::now();

            while ($num_people >= 0) {
                $history_item = new HistoryItem;

                $history_item->team_id = $theteam->id;
                $history_item->created_at = $the_date;
                $history_item->num_people = $num_people;

                $history_item->num_cases_open = WorkCase::where('team_id', $theteam->id)
                                            ->where('resolved', 0)
                                            ->count();

                $history_item->num_cases_new = WorkCase::where('team_id', $theteam->id)
                                           ->whereDate('created_at', '>', Carbon::parse($the_date)->subDays(1))
                                           ->whereDate('created_at', '<=', Carbon::parse($the_date))
                                           ->count();

                $history_item->num_contacts_new = WorkCase::where('team_id', $theteam->id)
                                           ->whereDate('created_at', '>', Carbon::parse($the_date)->subDays(1))
                                           ->whereDate('created_at', '<=', Carbon::parse($the_date))
                                           ->count();
                $history_item->save();

                $num_people += (-1 * rand(0, 10));
                // $num_cases_open += (-1 *$rand_inc[array_rand($rand_inc)]);
                // if ($num_cases_open < 0) { $num_cases_open = 0; }
                $the_date->setTimezone('UTC')->subDays(1);
            }
            $pro = $s->ProgressBar($pro, $teams_all->count(), 'Histories', '');
        }

        $this->command->info('Faking Admin History');

        $num_accounts = 25; //Account::all()->count();
        $the_date = Carbon::now();
        $rand_inc = [-1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2];

        $j = 0;
        while ($num_accounts > 0) {
            $history_item = new AdminHistoryItem;
            $history_item->num_accounts = $num_accounts;
            $history_item->num_users = round($num_accounts * 1.4, 0);
            $history_item->created_at = $the_date;
            $history_item->save();
            $num_accounts += (-1 * $rand_inc[array_rand($rand_inc)]);
            $the_date->setTimezone('UTC')->subDays(1);
            $j++;
        }
        $pro = $s->ProgressBar($pro, $j, 'Admin History Items', 'static');

        ////////////////////////////////////////////////////////////////////////////////
        //
        // ENTITIES
        //

        $this->command->info('Faking Entities');
        $j = 0;

        $teams = ($neu_mode) ? Team::where('id', 2)->get() : Team::all();

        foreach ($teams as $theteam) {
            if ($theteam->id == 2) {
                continue;
            }

            for ($i = 0; $i < 20; $i++) {
                $entity = new Entity;
                $entity->team_id = $theteam->id;

                $entity->address_number = rand(1, 100);
                $entity->address_fraction = null;
                $entity->address_street = $faker->streetName;
                $entity->address_city = $faker->city;
                $entity->address_state = $faker->stateAbbr;
                $entity->address_zip = substr($faker->postcode, 0, 5);

                $entity->full_address = $entity->generateFullAddress();
                $entity->household_id = $entity->generateHouseholdId();

                $entity->name = $faker->city.$faker->randomElement([' University', ' Inc', ' Foundation']);
                $entity->created_at = Carbon::now()->setTimezone('UTC')->subDays(rand(0, 120));

                $contact_info = [];
                // for ($i=0; $i<rand(1,3); $i++) {
                $contact_info[] = ['name' => $faker->RandomElement(['Main Office', 'Mobile', 'Director']), 'email' => $faker->safeEmail, 'phone' => $faker->phoneNumber];
                // }

                $entity->contact_info = $contact_info;

                $entity->save();
            }

            $j++;
            $pro = $s->ProgressBar($pro, $teams->count(), 'Teams -> '.$j.' Entities', '');
        }

        $this->command->info('Assigning Entities to Contacts...');
        $j = 0;
        $c = 0;
        $contacts = Contact::all();
        foreach ($contacts as $contact) {
            $c++;
            if (rand(0, 1) == 1) {                        //50% of Contacts are connected to Entities
                $entities = Entity::inRandomOrder()
                                  ->where('team_id', $contact->team_id)
                                  ->take(rand(1, 3))     //..1 to 3 of them
                                  ->get();
                if ($entities) {
                    foreach ($entities as $entity) {
                        $j++;
                        DB::table('contact_entity')->insert(
                            ['team_id'       => $contact->team_id,
                             'entity_id'     => $entity->id,
                             'contact_id'    => $contact->id, ]
                        );
                    }
                }
            }
            $pro = $s->ProgressBar($pro, $contacts->count(), $j.' Entities -> '.$c.' Contacts', '');
        }

        ////////////////////////////////////////////////////////////////////////////////
        //
        // RELATIONSHIPS
        //

        $this->command->info('Faking Relationships');
        $j = 0;

        $teams = ($neu_mode) ? Team::where('id', 2)->get() : Team::all();

        foreach ($teams as $theteam) {

            // This skips creating any relationships for NEU
            if ($theteam->id == 2) {
                continue;
            }

            $entities = Entity::inRandomOrder()->where('team_id', $theteam->id)->get();

            foreach ($entities as $entity) {
                $people = Person::inRandomOrder()->where('team_id', $theteam->id)
                                                       ->limit(rand(1, 4))
                                                       ->get();

                foreach ($people as $person) {
                    $ep = new EntityPerson;
                    $ep->team_id = $theteam->id;
                    $ep->user_id = $entity->user_id;

                    $ep->entity_id = $entity->id;
                    $ep->person_id = $person->id;
                    $ep->relationship = $faker->randomElement(['Communications Director', 'Board Member', 'Chief Financial Officer', 'Liaison']);

                    $ep->save();

                    $j++;
                }
            }

            $people = Person::inRandomOrder()->where('team_id', $theteam->id)->get();
            foreach ($people as $person) {
                $other_person = Person::inRandomOrder()->where('team_id', $theteam->id)->first();
                $relationship = new Relationship;
                $relationship->team_id = $theteam->id;
                $relationship->subject_id = $person->id;
                $relationship->subject_type = 'p';
                $relationship->kind = $faker->randomElement(['Spouse', 'Friend', 'Sibling', 'Business Partner', 'Coworker', 'Parent']);
                $relationship->object_id = $other_person->id;
                $relationship->object_type = 'p';
                $relationship->save();

                $other_entity = Entity::inRandomOrder()->where('team_id', $theteam->id)->first();
                $relationship = new Relationship;
                $relationship->team_id = $theteam->id;
                $relationship->subject_id = $person->id;
                $relationship->subject_type = 'p';
                $relationship->kind = $faker->randomElement(['Employee', 'Director', 'President', 'Mayor', 'CEO', 'Executive Director', 'Communications Director']);
                $relationship->object_id = $other_entity->id;
                $relationship->object_type = 'e';
                $relationship->save();

                $other_entity = Entity::inRandomOrder()->where('team_id', $theteam->id)->first();
                $relationship = new Relationship;
                $relationship->team_id = $theteam->id;
                $relationship->subject_id = $other_entity->id;
                $relationship->subject_type = 'e';
                $relationship->kind = $faker->randomElement(['Employer', 'Alma Mater']);
                $relationship->object_id = $person->id;
                $relationship->object_type = 'p';
                $relationship->save();

                $j = $j + 3;
            }

            $entities = Entity::inRandomOrder()->where('team_id', $theteam->id)->get();
            foreach ($entities as $entity) {
                $other_entity = Entity::inRandomOrder()->where('team_id', $theteam->id)
                                                       ->where('id', '<>', $entity->id)
                                                       ->first();
                $relationship = new Relationship;
                $relationship->team_id = $theteam->id;
                $relationship->subject_id = $entity->id;
                $relationship->subject_type = 'e';
                $relationship->kind = $faker->randomElement(['Subsidiary', 'Partner', 'Funder', 'Grantee']);
                $relationship->object_id = $other_entity->id;
                $relationship->object_type = 'e';
                $relationship->save();
            }

            $pro = $s->ProgressBar($pro, $teams->count(), 'Teams -> '.$j.' Relationships', '');
        }
    }
}
