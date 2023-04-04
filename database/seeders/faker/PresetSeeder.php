<?php

namespace Database\Seeders\Faker;

use App\Account;
use App\Group;
use App\Team;
use App\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Faker\PresetSeeder;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function createUser($name, $email, $password, $username = null)
    {
        DB::table('users')->insert([
            'name'           => $name,
            'email'          => $email,
            'username'       => substr($email, 0, strpos($email, '@')),
            'password'       => bcrypt($password),
            'language'       => 'en',
            'accepted_terms' => now(),
            'username'       => $username,
        ]);
    }

    public function teamUser($email, $team, $title, $permissions)
    {
        DB::table('team_user')->insert([
            'team_id' => Team::where('name', $team)->first()->id,
            'user_id' => User::where('email', $email)->first()->id,
        ]);

        DB::table('permissions')->insert([
            'team_id'           => Team::where('name', $team)->first()->id,
            'user_id'           => User::where('email', $email)->first()->id,
            'title'             => $title,
            'developer'         => (in_array('developer', $permissions)) ? true : false,
            'admin'             => (in_array('admin', $permissions)) ? true : false,
            'constituents'      => (in_array('constituents', $permissions)) ? true : false,
            'campaign'          => (in_array('campaign', $permissions)) ? true : false,
            'reports'           => (in_array('reports', $permissions)) ? true : false,
            'metrics'           => (in_array('metrics', $permissions)) ? true : false,
            'chat'              => (in_array('chat', $permissions)) ? true : false,
            'chat_external'     => (in_array('chat_external', $permissions)) ? true : false,
            'creategroups'      => (in_array('creategroups', $permissions)) ? true : false,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function currentTeam($email, $team)
    {
        $user = User::where('email', $email)->first();
        $user->current_team_id = Team::where('name', $team)->first()->id;
        $user->save();
    }

    public function ownsTeam($email, $team)
    {
        $team = Team::where('name', $team)->first();
        $team->owner_id = User::where('email', $email)->first()->id;
        $team->save();
    }

    public function run()
    {
        $faker = Faker::create();
        $s = new DatabaseSeeder;
        $pro = 0;
        $this->command->info('Adding Preset Data...');

        ////////////////////////////////////////////////////////////////////////////////////////////////
        //
        //   ACCOUNTS AND TEAMS
        //////////////////////////////////////////////////////////////////////////////////////////////////

        DB::table('accounts')->insert([
            'name' => 'Fluency Main Account',
            'contact_name' => 'Lazarus Morrison',
            'address' => '24 Spruce Ln',
            'city' => 'Ashburnham',
            'state' => 'MA',
            'zip' => '01010',
            'email' => 'lazarusm2@gmail.com',
            'phone' => '(413) 342-1234',
        ]);

        DB::table('teams')->insert([
                'name'              => 'FluencyBase Massachusetts',
                'app_type'          => 'campaign',
                'admin'             => 1,
                'account_id'        => Account::where('name', 'Fluency Main Account')->first()->id,
                'data_folder_id'    => 1,
                'short_name'         => 'Administrator',
                'district_name'     => 'Everything',
                'logo_img'          => '',
                'logo_orient'       => 'landscape',
                'active'            => true,
                'activated_at'      => date('Y-m-d', time()),
                'db_slice'          => 'x_MA_STATE',
            ]);

        DB::table('teams')->insert([
                'name'              => 'Northeastern University',
                'app_type'          => 'uni',
                'account_id'        => Account::where('name', 'Fluency Main Account')->first()->id,
                'data_folder_id'    => 1,
                'short_name'         => 'Northeastern U.',
                'district_name'     => 'Boston Area',
                'logo_img'          => '/images/logos/northeastern.png',
                'logo_orient'       => 'landscape',
                'active'            => true,
                'activated_at'      => date('Y-m-d', time()),
                'pilot'             => 1,
                'db_slice'          => 'x_MA_M_Boston',
            ]);

        // DB::table('searches')->insert([
        //     'team_id' => Team::where('name','Northeastern University')->first()->id,
        //     'user_id' => 2,
        //     'name' => 'Age over 30',
        //     'sql' => '(dob < CURRENT_DATE - INTERVAL 30 YEAR)',
        // ]);

        // DB::table('searches')->insert([
        //     'team_id' => Team::where('name','Northeastern University')->first()->id,
        //     'user_id' => 2,
        //     'name' => 'Women',
        //     'sql' => '(gender="F")',
        // ]);

        DB::table('teams')->insert([
                'name'              => 'Rep. Morrison Office',
                'app_type'          => 'office',
                'account_id'        => Account::where('name', 'Fluency Main Account')->first()->id,
                'data_folder_id'    => 1,
                'short_name'         => 'Morrison Office',
                'district_name'     => '1st, 3rd + 7th N. Worcester',
                'logo_img'          => '/images/logos/rep-morrison.png',
                'logo_orient'       => 'landscape',

                'active'            => true,
                'activated_at'      => date('Y-m-d', time()),
                'db_slice'          => 'x_MA_STATE',
            ]);

        // DB::table('searches')->insert([
        //     'team_id' => Team::where('name','Rep. Morrison Office')->first()->id,
        //     'user_id' => 2,
        //     'name' => 'McIntosh Rd',
        //     'sql' => '(full_address LIKE "%MCINTOSH%")',
        // ]);

        // DB::table('searches')->insert([
        //     'team_id' => Team::where('name','Rep. Morrison Office')->first()->id,
        //     'user_id' => 2,
        //     'name' => 'All Catherines',
        //     'sql' => '(full_name LIKE "%Catherine%")',
        // ]);

        // DB::table('searches')->insert([
        //     'team_id' => Team::where('name','Rep. Morrison Office')->first()->id,
        //     'user_id' => 2,
        //     'name' => 'Democratic Women',
        //     'sql' => '(gender="F" AND party="D")',
        // ]);

        // DB::table('searches')->insert([
        //     'team_id' => Team::where('name','Rep. Morrison Office')->first()->id,
        //     'user_id' => 2,
        //     'name' => 'Republican Men and all Unenrolled',
        //     'sql' => '(((gender="M" AND party="R") OR (party="U")) AND (dob > "1980-01-01"))',
        //     'scope_voters' => 0,
        // ]);

        // DB::table('searches')->insert([
        //     'team_id' => Team::where('name','Rep. Morrison Office')->first()->id,
        //     'user_id' => 2,
        //     'name' => 'Olden Folk',
        //     'sql' => '(dob < "1925-01-01")',
        // ]);

        DB::table('teams')->insert([
                'name' => 'Morrison Campaign Committee',    'app_type' => 'campaign',
                'account_id' => Account::where('name', 'Fluency Main Account')->first()->id,
                'data_folder_id' => 1,
                'short_name' => 'Morrison Campaign',
                'district_name' => '2nd Worcester District',
                'logo_img' => 'https://pbs.twimg.com/media/C6UTlW4UsAALen-.jpg',
                'logo_orient' => 'bulky',
                'active' => true,
                'activated_at' => date('Y-m-d', time()),
                'db_slice'          => 'x_MA_STATE',
            ]);

        DB::table('campaigns')->insert([
                            'team_id' => Team::where('name', 'Morrison Campaign Committee')->first()->id,
                            'name' => 'Gubernatorial Primary',
                            'election_day' => '2022-09-09',
                            'current' => 1,
                        ]);

        DB::table('teams')->insert([
                'name' => 'Fluency Business Development',    'app_type' => 'business',
                'admin' => 1,
                'account_id' => Account::where('name', 'Fluency Main Account')->first()->id,
                'data_folder_id' => 2,
                'short_name' => 'Fluency Biz',
                'district_name' => '(No District)',
                'logo_img' => null,
                'logo_orient' => 'bulky',
                'active' => true,
                'activated_at' => date('Y-m-d', time()),
                'db_slice'          => 'x_MA_STATE',
            ]);

        //////////////////////////////////////////////////////////////////////////////////////////////////
        //
        //   USERS
        //
        //////////////////////////////////////////////////////////////////////////////////////////////////

        $this->CreateUser('Lazarus Morrison', 'lazarusm@gmail.com', 'test');

        $this->teamUser('lazarusm@gmail.com', 'Northeastern University', 'Friend', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->teamUser('lazarusm@gmail.com', 'Rep. Morrison Office', 'Legislator', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->teamUser('lazarusm@gmail.com', 'Morrison Campaign Committee', 'Candidate', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->teamUser('lazarusm@gmail.com', 'Fluency Business Development', 'Staff', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->currentTeam('lazarusm@gmail.com', 'Rep. Morrison Office');

        $this->ownsTeam('lazarusm@gmail.com', 'FluencyBase Massachusetts');
        $this->ownsTeam('lazarusm@gmail.com', 'Northeastern University');
        $this->ownsTeam('lazarusm@gmail.com', 'Rep. Morrison Office');
        $this->ownsTeam('lazarusm@gmail.com', 'Morrison Campaign Committee');
        $this->ownsTeam('lazarusm@gmail.com', 'Fluency Business Development');

        ////////////////////////////////////////////////////////////////////////////////

        $this->CreateUser('Lorde Slothe', 'lordslothe@gmail.com', 'test');

        $this->teamUser('lordslothe@gmail.com', 'Northeastern University', 'Friend', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->teamUser('lordslothe@gmail.com', 'Rep. Morrison Office', 'Legislator', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->teamUser('lordslothe@gmail.com', 'Morrison Campaign Committee', 'Candidate', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->teamUser('lordslothe@gmail.com', 'Fluency Business Development', 'Staff', ['developer', 'admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->currentTeam('lordslothe@gmail.com', 'Rep. Morrison Office');

        ////////////////////////////////////////////////////////////////////////////////

        $this->CreateUser('David Isberg', 'communityaffairs@neu.edu', 'test', 'disberg');

        $this->teamUser('communityaffairs@neu.edu', 'Northeastern University', 'Director', ['reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->currentTeam('communityaffairs@neu.edu', 'Rep. Morrison Office');

        ////////////////////////////////////////////////////////////////////////////////

        $this->CreateUser('Campaigny McGee', 'flunky@lazcampaign.info', 'test');

        $this->teamUser('flunky@lazcampaign.info', 'Northeastern University', 'Director', ['admin', 'constituents', 'campaign', 'reports', 'metrics', 'chat', 'chat_external', 'creategroups']);

        $this->currentTeam('flunky@lazcampaign.info', 'Rep. Morrison Office');

        ////////////////////////////////////////////////////////////////////////////////
        //
        //   MISC
        //
        ////////////////////////////////////////////////////////////////////////////////

        DB::table('accounts')->insert([
                'name' => 'Rep. Lorde Slothe',
                'contact_name' => 'Lazarus Morrison',
                'address' => '24 Spruce Ln',
                'city' => 'Ashburnham',
                'state' => 'MA',
                'zip' => '01010',
            ]);

        DB::table('teams')->insert([
                        'name' => 'Slothe Campaign',
                        'app_type' => 'campaign',
                        'account_id' => Account::where('name', 'Rep. Lorde Slothe')->first()->id,
                        'data_folder_id' => 1,
                        'short_name' => 'Slothe Campaign',
                        'district_name' => 'Boston Area',
                        'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                        'logo_img' => null,
                        'logo_orient' => 'landscape',
                        'active' => true,
                        'activated_at' => date('Y-m-d', time()),

                    ]);

        $userid = DB::table('users')->insert([
                            'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                            'name' => $faker->firstName,
                            'email' => $faker->email,
                            'password' => bcrypt('test'),
                            'language'   => 'en',
                        ]);

        // DB::table('team_user')->insert([
        //     'team_id' => DB::getPdo()->lastInsertId(), 'user_id' => 4
        // ]);

        DB::table('teams')->insert([
                        'name' => 'Rep. Slothe Office',  'app_type' => 'office',
                        'account_id' => Account::where('name', 'Rep. Lorde Slothe')->first()->id,
                        'data_folder_id' => 1,
                        'short_name' => 'Slothe Office',
                        'district_name' => '2nd Worcester District',
                        'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                        'logo_img' => null,
                        'logo_orient' => 'bulky',
                        'active' => true,
                        'activated_at' => date('Y-m-d', time()),

                    ]);

        $userid = DB::table('users')->insert([
                            'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                            'name' => $faker->firstName,
                            'email' => $faker->email,
                            'password' => bcrypt('test'),
                            'language'   => 'en',
                        ]);

        // DB::table('team_user')->insert([
        //     'team_id' => DB::getPdo()->lastInsertId(), 'user_id' => 4
        // ]);

        DB::table('accounts')->insert([
                'name' => 'Selectman Mustachio',
                'contact_name' => 'Mustachio Jones',
                'address' => '24 Spruce Ln',
                'city' => 'Cohasset',
                'state' => 'MA',
                'zip' => '01010',
            ]);

        DB::table('teams')->insert([
                        'name' => 'Mustachio Campaign',    'app_type' => 'campaign',
                        'account_id' => Account::where('name', 'Selectman Mustachio')->first()->id,
                        'data_folder_id' => 1,
                        'short_name' => 'Mustachio Campaign',
                        'district_name' => 'Boston Area',
                        'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                        'logo_img' => null,
                        'logo_orient' => 'landscape',
                        'active' => true,
                        'activated_at' => date('Y-m-d', time()),

                    ]);

        $userid = DB::table('users')->insert([
                            'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                            'name' => $faker->firstName,
                            'email' => $faker->email,
                            'password' => bcrypt('test'),
                            'language'   => 'en',
                        ]);

        // DB::table('team_user')->insert([
        //     'team_id' => DB::getPdo()->lastInsertId(), 'user_id' => 3
        // ]);

        DB::table('teams')->insert([
                        'name' => 'Rep. Mustachio Office',  'app_type' => 'office',
                        'account_id' => Account::where('name', 'Selectman Mustachio')->first()->id,
                        'data_folder_id' => 1,
                        'short_name' => 'Mustachio Office',
                        'district_name' => '2nd Worcester District',
                        'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                        'logo_img' => null,
                        'logo_orient' => 'bulky',
                        'active' => true,
                        'activated_at' => date('Y-m-d', time()),

                    ]);

        $userid = DB::table('users')->insert([
                            'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                            'name' => $faker->firstName,
                            'email' => $faker->email,
                            'password' => bcrypt('test'),
                            'language'   => 'en',
                        ]);

        // DB::table('team_user')->insert([
        //     'team_id' => DB::getPdo()->lastInsertId(), 'user_id' => 3
        // ]);

        DB::table('accounts')->insert([
                'name' => 'Margaret Thatcher',
                'contact_name' => 'Mrs. Thatcher',
                'address' => '24 Spruce Ln',
                'city' => 'North Adams',
                'state' => 'MA',
                'zip' => '01010',
                'email' => 'primeminister@rollo.com',
            ]);

        DB::table('teams')->insert([
                        'name' => 'Thatcher Incorporated',    'app_type' => 'business',
                        'account_id' => DB::getPdo()->lastInsertId(),
                        'data_folder_id' => 1,
                        'short_name' => 'ThatcherCorp',
                        'district_name' => 'Greater London',
                        'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                        'logo_img' => null,
                        'logo_orient' => 'landscape',
                        'active' => true,
                        'activated_at' => date('Y-m-d', time()),

                    ]);

        $userid = DB::table('users')->insert([
                            'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                            'name' => $faker->firstName,
                            'email' => $faker->email,
                            'password' => bcrypt('test'),
                            'language'   => 'en',
                        ]);

        // DB::table('team_user')->insert([
        //     'team_id' => DB::getPdo()->lastInsertId(), 'user_id' => 4
        // ]);

        DB::table('accounts')->insert([
                'name' => 'Galvin Office',
                'contact_name' => 'Erin O\'Connor',
                'address' => 'Room 166, State House',
                'city' => 'Boston',
                'state' => 'MA',
                'zip' => '02114',
                'email' => 'lazarusm@gmail.com',
                'phone' => '(413) 342-1234',
            ]);

        DB::table('teams')->insert([
                'name' => 'Galvin Office',    'app_type' => 'office',
                'admin' => 0,
                'account_id' => DB::getPdo()->lastInsertId(),
                'data_folder_id' => 2,
                'short_name' => 'Galvin Testing',
                'district_name' => '6th Norfolk',
                'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                'logo_img' => null,
                'logo_orient' => 'bulky',
                'active' => true,
                'activated_at' => date('Y-m-d', time()),
            ]);

        $userid = DB::table('users')->insert([
                'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                'name' => "Erin O'Connor",
                'email' => "erin.o'connor@mahouse.gov",
                'password' => bcrypt('testing'),
                'language'   => 'en',
            ]);

        // Lauren
        DB::table('accounts')->insert([
                'name' => 'Welch Office',
                'contact_name' => 'Lauren Corcoran',
                'address' => 'Room 413-B',
                'city' => 'Boston',
                'state' => 'MA',
                'zip' => '02114',
                'email' => 'saccoemail@gmail.com',
                'phone' => '(617) 722-1660',
            ]);

        DB::table('teams')->insert([
                'name' => 'Senator James T. Welch',    'app_type' => 'office',
                'admin' => 0,
                'account_id' => DB::getPdo()->lastInsertId(),
                'data_folder_id' => 2,
                'short_name' => 'Welch Testing',
                'district_name' => 'Hampden',
                'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                'logo_img' => 'https://upload.wikimedia.org/wikipedia/commons/6/62/Seal_of_the_Senate_of_Massachusetts.svg',
                'logo_orient' => 'bulky',
                'active' => true,
                'activated_at' => date('Y-m-d', time()),
            ]);

        $userid = DB::table('users')->insert([
                'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                'name' => 'Lauren Corcoran',
                'email' => 'saccoemail@gmail.com',
                'password' => bcrypt('testing'),
                'language'   => 'en',
            ]);

        // Stripe
        DB::table('accounts')->insert([
                'name' => 'Stripe Login Account',
                'contact_name' => 'Stripe Person',
                'address' => 'Room 413-B',
                'city' => 'Boston',
                'state' => 'MA',
                'zip' => '02114',
                'email' => 'support@stripe.com',
                'phone' => '(617) 722-1660',
            ]);

        DB::table('teams')->insert([
                'name' => 'Stripe Support',    'app_type' => 'office',
                'admin' => 0,
                'account_id' => DB::getPdo()->lastInsertId(),
                'data_folder_id' => 2,
                'short_name' => 'Stripe Support',
                'district_name' => 'Testing District',
                'owner_id' => User::where('email', 'lazarusm@gmail.com')->first()->id,
                'logo_img' => 'https://upload.wikimedia.org/wikipedia/commons/6/62/Seal_of_the_Senate_of_Massachusetts.svg',
                'logo_orient' => 'bulky',
                'active' => true,
                'activated_at' => date('Y-m-d', time()),
            ]);

        $userid = DB::table('users')->insert([
                'current_team_id' => DB::getPdo()->lastInsertId(), // OFFICE
                'name' => 'Stripe User',
                'email' => 'support@stripe.com',
                'password' => bcrypt('stripetest1!'),
                'language'   => 'en',
            ]);

        /////////////////////////////////////////////////////////////////////////////////
        //
        //  CATEGORIES --- OFFICE
        //

        $english_cats = ['__--__initial--__--']; //for easy reference (ID = 0)

        DB::table('categories')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'name' => 'constituent groups',
            'data_template' => json_encode(['notes' => null]),
            'has_position' => false,
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        DB::table('categories')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'name' => 'issue groups',
            'data_template' => json_encode(['notes' => null, 'position' => null]),
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        DB::table('categories')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'name' => 'legislation',
            'data_template' => json_encode(['notes' => null, 'position' => null]),
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        DB::table('categories')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'name' => 'email lists',
            'data_template' => null, //No Pivot Data
            'has_position' => false,
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        /////////////////////////////////////////////////////////////////////////////
        //
        //  CATEGORIES --- CAMPAIGN
        //

        DB::table('categories')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'name' => 'volunteers',
            'data_template' => json_encode(['notes' => null]),
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        DB::table('categories')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'name' => 'campaign issues',
            'data_template' => json_encode(['notes' => null, 'position' => null]),
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        /////////////////////////////////////////////////////////////////////////////
        //
        //  CATEGORIES  --- UNIVERSITY
        //

        DB::table('categories')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'name' => 'interests',
            'data_template' => json_encode(['notes' => null]),
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        DB::table('categories')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'name' => 'issues',
            'data_template' => json_encode(['notes' => null, 'position' => null]),
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        DB::table('categories')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'name' => 'labels',
            'data_template' => json_encode(['notes' => null]),
        ]);
        $q = \App\Category::where('id', DB::getPdo()->lastInsertId())->first();
        array_push($english_cats, $q->preset.'_'.$q->name);

        /////////////////////////////////////////////////////////////////////////////
        //
        //  GROUPS --- OFFICE
        //

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'issue groups', $english_cats),
            'name' => 'Yes on 9',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'issue groups', $english_cats),
            'name' => 'Climate Change',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'issue groups', $english_cats),
            'name' => 'Healthcare',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'issue groups', $english_cats),
            'name' => 'Taxes',
        ]);

        /////////////////////////////////////////////

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'legislation', $english_cats),
            'name' => 'The Higher Education Act 2019',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'legislation', $english_cats),
            'name' => 'H. 199 (2017)',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'legislation', $english_cats),
            'name' => 'H. 139 (2019)',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'legislation', $english_cats),
            'name' => 'Budget Amd #515',
        ]);

        /////////////////////////////////////////////

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'constituent groups', $english_cats),
            'name' => 'Business Person',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'constituent groups', $english_cats),
            'name' => 'Educator',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'constituent groups', $english_cats),
            'name' => 'Journalist',
        ]);

        /////////////////////////////////////////////

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'email lists', $english_cats),
            'name' => 'Master List',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'email lists', $english_cats),
            'name' => 'Newsletter',
        ]);

        DB::table('groups')->insert([
            'preset' => 'office',
            'team_id' => 0,
            'category_id' => array_search('office_'.'email lists', $english_cats),
            'name' => 'Session Update',
        ]);

        /////////////////////////////////////////////////////////////////////////////
        //
        //  GROUPS --- CAMPAIGN
        //

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'volunteers', $english_cats),
            'name' => 'Door knocking',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'volunteers', $english_cats),
            'name' => 'Phone Calls',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'volunteers', $english_cats),
            'name' => 'Lawnsign',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'volunteers', $english_cats),
            'name' => 'House Party',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'volunteers', $english_cats),
            'name' => 'Send Postcards',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'volunteers', $english_cats),
            'name' => 'Social Media',
        ]);

        //////////

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'campaign issues', $english_cats),
            'name' => 'Taxes',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'campaign issues', $english_cats),
            'name' => 'Environment',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'campaign issues', $english_cats),
            'name' => 'Education',
        ]);

        DB::table('groups')->insert([
            'preset' => 'campaign',
            'team_id' => 0,
            'category_id' => array_search('campaign_'.'campaign issues', $english_cats),
            'name' => 'Open Government',
        ]);

        /////////////////////////////////////////////////////////////////////////////
        //
        //  GROUPS -- UNIVERSITY
        //

        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'School Funding',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'Arts',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'Sciences',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'Language Education',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'Environment',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'Job Fairs',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'Community Engagement Awards',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'interests', $english_cats),
            'name' => 'Public Art',
        ]);

        ////////////////////////////////////

        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'labels', $english_cats),
            'name' => 'Students',
        ]);

        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'labels', $english_cats),
            'name' => 'Faculty',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'labels', $english_cats),
            'name' => 'Parents',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'labels', $english_cats),
            'name' => 'Landlords',
        ]);
        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'labels', $english_cats),
            'name' => 'Local Elected Officials',
        ]);

        ////////////////////////////////////

        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'issues', $english_cats),
            'name' => 'Admission Policy',
        ]);

        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'issues', $english_cats),
            'name' => 'Community Park',
        ]);

        DB::table('groups')->insert([
            'preset' => 'university',
            'team_id' => 0,
            'category_id' => array_search('university_'.'issues', $english_cats),
            'name' => 'Burke Street Residence Hall',
        ]);

        //////////////////////////////////////////////////////////////////////

        //Assign Preset Groups
        $teams_all = Team::all();

        foreach ($teams_all as $theteam) {
            switch ($theteam->app_type) {
                case 'uni':
                    $groups = Group::where('preset', 'university')->get();
                    break;

                case 'office':
                    $groups = Group::where('preset', 'office')->get();
                    break;

                case 'campaign':
                    $groups = Group::where('preset', 'campaign')->get();
                    break;

                case 'admin':
                    $groups = null;
                    break;
            }

            if ($groups) {
                foreach ($groups as $thegroup) {
                    DB::table('groups')->insert([
                        'team_id'      => $theteam->id,
                        'category_id'  => $thegroup->category_id,
                        'name'         => $thegroup->name,
                     ]);
                }
            }
        }

        $pro = $s->ProgressBar($pro, 1, 'Set of Data', 'static');
    }
}
