<?php

namespace App\Console\Commands;

use App\Account;
use App\Category;
use App\Contact;
use App\ContactPerson;
use App\County;
use App\Group;
use App\GroupPerson;
use App\Municipality;
use App\Permission;
use App\Person;
use App\Team;
use App\TeamUser;
use App\User;
use App\Voter;
use App\VoterMaster;
use App\VoterSlice;
use App\VotingHousehold;
use App\WorkCase;
use App\WorkCasePerson;
use Artisan;
use Carbon\Carbon;
use DB;
use Faker\Factory as Faker;
use Illuminate\Console\Command;

class CreateFakeSlice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:fake {--repopulate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a fake team / slice for demo purposes';

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
    public function chooseFromList($list, $previous = null)
    {
        switch ($list) {

            case 'Legislation':
                $list = [
                        'Civil Rights Act',
                        'Budget Amendment',
                        'Telecommunications Act',
                        'HR 2010',
                        'Draft Bill to Amend Constitution',
                        'Voting Rights Act',
                        'War Powers Resolution',
                        'Sarbanes-Oxley Act of 2002',
                        'Do-Not-Call Implementation Act',
                        'Social Security Act',
                        'Digital Millennium Copyright Act',
                        'Carbon Pricing Legislation',
                        'Housing Legislation',
                        ];
                break;

                case 'Issue Groups':
                    $list = [
                            'Climate Change',
                            'Fiscal Responsibility',
                            'Local City Issue',
                            'Automation',
                            'Universal Basic Income',
                            'Freedom of Speech',
                            'Civil Rights',
                            'Education',
                            'MBTA',
                            'Community Colleges',
                            'Veterans Administration',
                            'Casinos',
                            'Local Economy',
                            'Immigration',
                            'Student Loans',
                            'Net Neutrality',
                            'Green New Deal',
                            'Sustainability',
                            ];
                    break;

                case 'Constituent Groups':
                    $list = [
                            'SEIU',
                            'Mass Municipal Assoc',
                            'League of Women Voters',
                            'Ward 20 Democrats',
                            'Local Selectmen',
                            'Activists',
                            'University of Massachusetts',
                            'Trustees of the Reservations',
                            'Harvard',
                            'MIT',
                            'Worcester County Horticultural Society',
                            'Community Action',
                            'Massachusett Libraries',
                            'Boston Foundation',
                            'Boston Redevelopment Authority',
                            'Office of the Governor',
                            'Dept of the Environment',
                            'AARP',
                            'Environment Massachusetts',
                            'ACLU',
                            'Museum of Science',
                            'YWCA of Lowell',
                            'Massachusetts Nonprofit Network',
                            ];
                    break;

            case 'Case Subject':
                $list = [
                        'Trouble at RMV',
                        'Passport Issue',
                        'Pension Question',
                        'Scholarship Request',
                        'Find Out About School Funding',
                        'General inquiry',
                        'Problem with UMass',
                        'Requests help with street paving',
                        ];
                break;
        }

        $finished = false;

        $i = 0;
        $choice = null;

        while ($finished == false) {
            $num = count($list);

            $choice = $list[rand(0, $num - 1)];

            if ($previous) {
                if (! in_array($choice, $previous)) {
                    $finished = true;
                }
                if ($i > 40) {
                    $finished = true;
                }
            } else {
                $finished = true;
            }

            $i++;
        }

        return $choice;
    }

    public function handle()
    {
        $state = 'MA';

        $team_name = 'Rudyard B. Guvna';

        $team = Team::where('name', $team_name)->first();
        if (! $team) {
            $team = new Team();
        }

        $team->name = $team_name;
        $team->short_name = $team_name;
        $team->account_id = 1; // FOR NOW -- updated below
        $team->district_type = 'FAKE'; //Fake
        $team->district_id = 46;
        $team->app_type = 'office';
        $team->data_folder_id = $state;
        $team->save();

        Person::where('team_id', $team->id)->delete();
        Contact::where('team_id', $team->id)->delete();
        ContactPerson::where('team_id', $team->id)->delete();
        WorkCase::where('team_id', $team->id)->delete();
        WorkCasePerson::where('team_id', $team->id)->delete();
        Group::where('team_id', $team->id)->delete();
        GroupPerson::where('team_id', $team->id)->delete();
        Category::where('team_id', $team->id)->delete();

        $fluency = User::whereIn('username', ['fluency1', 'lmorrison'])->get();

        foreach ($fluency as $the_fluency) {
            $team->account_id = $the_fluency->team->account_id;

            $teamuser = TeamUser::where('user_id', $the_fluency->id)
                                ->where('team_id', $team->id)
                                ->first();
            if (! $teamuser) {
                $teamuser = new TeamUser();
            }

            $teamuser->user_id = $the_fluency->id;
            $teamuser->team_id = $team->id;
            $teamuser->save();

            $permission = Permission::where('user_id', $the_fluency->id)
                                    ->where('team_id', $team->id)
                                    ->first();
            if (! $permission) {
                $permission = new Permission();
            }

            $permission->user_id = $the_fluency->id;
            $permission->team_id = $team->id;
            $permission->admin = 1;
            $permission->developer = 1;
            $permission->creategroups = 1;
            $permission->save();
        }

        //////////////////////////////////////////////////////

        $where = '';
        $table_name = '';

        if ($team->district_type == 'FAKE') {
            $where = 'senate_district = '.$team->district_id; //Senate District
            // $where = 1; //Statewide
            $table_name = 'x_'.$state.'_FAKE_'.str_pad($team->district_id, '0', 4, STR_PAD_LEFT);
        }

        $slice = VoterSlice::where('name', $table_name)->first();

        if (! $slice) {
            $slice = $this->createTable($where, $table_name);
            $this->createHouseholds($table_name);
        }
        $team->db_slice = $slice->name;

        $team->save();

        //////////////////////////////////////////////////////////////////////////

        if ($this->option('repopulate')) {
            Artisan::call('cf:populate_slices --just_voters --overwrite --slice='.$slice->name);
        }

        //////////////////////////////////////////////////////////////////////////

        $faker = Faker::create();

        session()->put('team_table', $slice->name);

        Voter::chunk(1000, function ($voters) use ($faker, $team) {
            echo "1000\n";
            foreach ($voters as $voter) {
                if ($this->option('repopulate')) {
                    $voter->first_name = $faker->firstName;
                    $voter->middle_name = ucwords($faker->randomLetter);
                    $voter->last_name = $faker->lastName;
                    $voter->home_phone = (rand(0, 2) == 1) ? $faker->phoneNumber : null;
                    $voter->cell_phone = (rand(0, 2) == 1) ? $faker->phoneNumber : null;
                    $voter->dob = $faker->date($format = 'Y-m-d', $max = '2001-10-01');
                    $voter->registration_date = $faker->date;

                    $voter->save();
                }

                $percentage_in_database = 8;
                if (rand(1, 100) <= $percentage_in_database) {
                    $person = $this->Fake_findPersonOrImportVoter($voter->id, $team->id);
                    $person->primary_email = $faker->safeEmail;
                    $person->created_at = Carbon::now()->setTimezone('UTC')->subDays(rand(0, 2000));
                    $person->save();
                }
            }
        });

        //////////////////////////////////////////////////////////////////////////

        echo "\r\n".'Groups...'."\r\n";

        foreach (['Issue Groups', 'Constituent Groups', 'Legislation'] as $catname) {
            $team_category = Category::where('team_id', $team->id)
                                         ->where('name', $catname)
                                         ->first();
            if (! $team_category) {
                $template_category = Category::whereNotNull('preset')
                                             ->where('name', $catname)
                                             ->first();
                $team_category = $template_category->replicate();
                $team_category->team_id = $team->id;
                $team_category->preset = null;
                $team_category->save();

                $prev_leg = [];
                $prev_const = [];
                $prev_issue = [];

                for ($g = 0; $g < rand(6, 14); $g++) {
                    $group = new Group();
                    $group->category_id = $team_category->id;
                    $group->team_id = $team->id;
                    $group->name = $faker->realText($maxNbChars = 20); // Just a noun
                    if ($catname == 'Legislation') {
                        $group->name = $this->chooseFromList('Legislation', $prev_leg);
                        $prev_leg[] = $group->name;
                    }
                    if ($catname == 'Constituent Groups') {
                        $group->name = $this->chooseFromList('Constituent Groups', $prev_const);
                        $prev_const[] = $group->name;
                    }
                    if ($catname == 'Issue Groups') {
                        $group->name = $this->chooseFromList('Issue Groups', $prev_issue);
                        $prev_issue[] = $group->name;
                    }
                    $group->save();
                }
            }
        }

        //////////////////////////////////////////////////////////////////////////

        echo "\r\n".'Contacts, Cases and all Pivots...'."\r\n";

        $available_priorities = ['High', 'Medium', 'Low'];
        $available_statuses = ['resolved', 'open', 'held'];
        $available_titles = ['Director', 'President', 'Coordinator', 'Volunteer'];

        $people = Person::where('team_id', $team->id)->get();

        $person_count = 1;

        foreach ($people as $person) {
            echo 'Person: '.$person_count++."    \r";

            $i = 0;

            while ($i < rand(0, 10)) {
                $i++;

                $contact = new Contact();
                $contact->created_at = Carbon::now()->setTimezone('UTC')->subDays(rand(0, 500));
                $contact->date = $contact->created_at;
                $contact->team_id = $team->id;
                $contact->user_id = $the_fluency->id;
                $contact->notes = $faker->realText; //$this->getContactText($person)
                $contact->followup = (rand(1, 4) == 1) ? true : false;
                $contact->save();

                $j = 0;

                while ($j < rand(1, 2)) {
                    $j++;

                    $pivot = new ContactPerson();
                    $pivot->contact_id = $contact->id;
                    $pivot->person_id = Person::where('team_id', $team->id)
                                                    ->inRandomOrder()
                                                    ->first()
                                                    ->id;
                    $pivot->team_id = $team->id;
                    $pivot->save();
                }

                if (rand(1, 3) == 1) {
                    $case = new WorkCase();
                    $case->team_id = $team->id;
                    $case->user_id = $the_fluency->id;
                    $case->created_at = Carbon::now()->setTimezone('UTC')->subDays(rand(0, 2000));
                    $case->date = $case->created_at;
                    $case->subject = $this->chooseFromList('Case Subject'); //$faker->realText($maxNbChars = 40);
                    $case->notes = $contact->notes;
                    $case->priority = $available_priorities[rand(0, 2)];
                    $case->status = $available_statuses[rand(0, 2)];
                    if ($case->status == 'resolved') {
                        $case->closing_remarks = $faker->realText;
                    }
                    $case->save();

                    $contact->case_id;
                    $contact->save();

                    $contacts_people = ContactPerson::where('contact_id', $contact->id)->get();
                    foreach ($contacts_people as $cp) {
                        $pivot = new WorkCasePerson();
                        $pivot->person_id = $cp->person_id;
                        $pivot->case_id = $case->id;
                        $pivot->team_id = $team->id;
                        $pivot->save();
                    }
                }
            }

            $g = 0;

            while ($g < rand(1, 8)) {
                $g++;

                $random_group = Group::where('team_id', $team->id)->inRandomOrder()->first();

                $pivot = new GroupPerson();
                $pivot->person_id = $person->id;
                $pivot->group_id = $random_group->id;
                if (in_array($random_group->cat->name, ['Legislation', 'Issue Groups'])) {
                    switch (rand(1, 3)) {
                        case 1:
                            $pivot->position = 'Supports';
                            break;

                        case 2:
                            $pivot->position = 'Opposed';
                            break;

                        case 3:
                            $pivot->position = 'Undecided';
                            break;
                    }
                } else {
                    $pivot->title = $available_titles[rand(0, 2)];
                }
                $pivot->team_id = $team->id;
                $pivot->created_at = Carbon::now()->subDays(rand(1, 365));
                $pivot->save();
            }
        }

        // $this->createFakeCases($person);

        //////////////////////////////////////////////////////////////////////////
        if ($this->option('repopulate')) {
            Artisan::call('cf:populate_slices --just_households --overwrite --slice='.$slice->name);
        }

        //////////////////////////////////////////////////////////////////////////
    }

    public function createTable($where, $table_name)
    {
        $db_land = env('DB_VOTER_DATABASE');

        DB::statement('CREATE TABLE IF NOT EXISTS `'.$table_name.'` LIKE x__template_voters');

        $slice = new VoterSlice;
        $slice->sql = $where;
        $slice->name = $table_name;
        $slice->save();

        echo 'Created: '.$table_name."\n";

        return $slice;
    }

    public function createHouseholds($table_name)
    {
        $hh_table = $table_name.'_hh';

        DB::statement('CREATE TABLE IF NOT EXISTS `'.$hh_table.'` LIKE x__template_households');

        echo 'Created: '.$hh_table."\n";
    }

    public function Fake_findPersonOrImportVoter($id, $team_id)
    {
        $theperson = new Person;
        $theperson->team_id = $team_id;

        // $thevoter = VoterMaster::find($id);
        $thevoter = Voter::find($id);

        if (! $thevoter) {
            // If voter id not found, returns null
            return null;
        }

        $theperson->full_name = titleCase($thevoter->full_name);
        $theperson->full_name_middle = titleCase($thevoter->full_name_middle);
        $theperson->household_id = $thevoter->household_id;
        $theperson->mass_gis_id = $thevoter->mass_gis_id;
        $theperson->full_address = $thevoter->full_address;

        $theperson->voter_id = $thevoter->id;
        $theperson->first_name = titleCase($thevoter->first_name);
        $theperson->middle_name = titleCase($thevoter->middle_name);
        $theperson->last_name = titleCase($thevoter->last_name);

        $theperson->address_number = ucwords(strtolower($thevoter->address_number));
        $theperson->address_fraction = ucwords(strtolower($thevoter->address_fraction));
        $theperson->address_street = ucwords(strtolower($thevoter->address_street));
        $theperson->address_city = ucwords(strtolower($thevoter->address_city));
        $theperson->address_state = strtoupper($thevoter->address_state);
        $theperson->address_apt = ucwords(strtolower($thevoter->address_apt));
        $theperson->address_zip = $thevoter->address_zip;

        if (abs((int) $thevoter->address_lat) > 0) {
            $theperson->address_lat = $thevoter->address_lat;
        }
        if (abs((int) $thevoter->address_long) > 0) {
            $theperson->address_long = $thevoter->address_long;
        }

        $theperson->mailing_info = $thevoter->mailing_info;
        $theperson->business_info = $thevoter->business_info;

        $emails = $thevoter->emails;
        if ($emails) {
            if (count($emails) > 0) {
                $theperson->primary_email = $emails[0];
                if (count($emails) > 1) {
                    $other_emails = [];
                    foreach (array_slice($emails, 1) as $value) {
                        $other_emails[] = [$value, null];
                    }
                    $theperson->other_emails = $other_emails;
                }
            }
        }
        $theperson->primary_phone = $thevoter->cell_phone;
        if ($thevoter->home_phone) {
            $theperson->other_phones = [[$thevoter->home_phone, 'Home']];
        }

        $theperson->gender = $thevoter->gender;
        $theperson->party = $thevoter->party;
        $theperson->dob = $thevoter->dob;

        // Political districts
        $theperson->governor_district = $thevoter->governor_district;
        $theperson->congress_district = $thevoter->congress_district;
        $theperson->senate_district = $thevoter->senate_district;
        $theperson->house_district = $thevoter->house_district;

        $theperson->county_code = $thevoter->county_code;
        $theperson->ward = $thevoter->ward;
        $theperson->precinct = $thevoter->precinct;
        $theperson->city_code = $thevoter->city_code;

        $theperson->old_cc_id = $thevoter->voterID;

        $theperson->save();

        return $theperson;
    }
}
