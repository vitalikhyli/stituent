<?php

namespace App\Console\Commands\Seeding;

use App\Account;
use App\BulkEmail;
use App\Category;
use App\CommunityBenefit;
use App\Contact;
use App\County;
use App\Entity;
use App\Group;
use App\GroupPerson;
use App\Models\CC\CCUser;
use App\Municipality;
use App\Partnership;
use App\PartnershipType;
use App\Permission;
use App\Person;
use App\Relationship;
use App\Team;
use App\TeamUser;
use App\User;
use App\VoterSlice;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class All extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_all {--fresh} {--login=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs Seeder for everything, brings over all data from CC.';

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

    //if (env('LOCAL_MACHINE') != 'Slothe')             return;
        if (! $this->confirm('Do you wish to continue?')) {
            return;
        }

        $start = $this->seederStart();

        if ($this->option('fresh')) {
            echo 'DISABLED FRESH FOR SAFETY';

            return;
            // $this->call('cf:migration', ['--fresh' => 1]);
        }

        $this->call('cf:add_gis'); //really a migration, changes people table
        $this->call('st:seed_geography');
        $this->call('st:seed_accounts');
        $this->call('cf:billygoat');

        //==============================================================================>

        //FLUENCY1 = ADMIN, ETC
        $fluency = User::whereIn('username', ['fluency1', 'lmorrison'])->get();
        foreach ($fluency as $the_fluency) {
            DB::statement('update permissions set admin=1, developer=1, creategroups=1 where user_id='.$the_fluency->id);
        }

        // UNIVERSITY TEAM
        $fluency = User::where('username', 'fluency1')->first();
        $f_team = Team::find($fluency->team_id);
        $u_team = Team::where('app_type', 'u')->first();

        $fluency_u = TeamUser::where('user_id', $fluency->id)
                             ->where('team_id', $u_team->id)
                             ->first();

        //==============================================================================>

        if (env('LOCAL_MACHINE') == 'Slothe') {
            $this->cycleThroughAnonymize($model = Account::all(),
                                         $fields = ['name', 'email', 'contact_name'],
                                         $exceptions = ['name' => ['All Campaigns', 'Northeastern University']]);

            $this->cycleThroughAnonymize($model = Team::all(),
                                         $fields = ['name', 'short_name'],
                                         $exceptions = ['name' => ['All Campaigns', 'Northeastern University']]);

            $this->cycleThroughAnonymize($model = User::all(),
                                         $fields = ['name', 'username', 'email'],
                                         $exceptions = ['current_team_id' => [$u_team->id, $f_team->id]]);
        }

        //==============================================================================>

        // EVERYONE CAN CREATE GROUPS
        DB::statement('update permissions set creategroups=1');

        if (! $fluency_u) {
            $fluency_u = new TeamUser;
            $fluency_u->user_id = $fluency->id;
            $fluency_u->team_id = $u_team->id;
            $fluency_u->save();
        }

        $fluency_permission = Permission::where('user_id', $fluency->id)
                                        ->where('team_id', $u_team->id)
                                        ->first();

        if (! $fluency_permission) {
            $fluency_permission = new Permission;
            $fluency_permission->user_id = $fluency->id;
            $fluency_permission->team_id = $u_team->id;
            $fluency_permission->admin = true;
            $fluency_permission->developer = true;
            $fluency_permission->save();
        }

        //==============================================================================>

        $this->call('st:seed_nu');
        $this->call('st:seed_slices');

        $valid_campaign_ids = [];
        if ($this->option('login')) {
            $login = $this->option('login');
            $cc_user = CCUser::where('login', $login)->first();
            $valid_campaign_ids = [$cc_user->campaignID];
        } else {
            $billy_accounts = Account::whereNotNull('billygoat_id')->pluck('id');
            $valid_campaign_ids = Team::where('app_type', 'office')
                                      ->whereIn('account_id', $billy_accounts)
                                      ->orWhere('old_cc_id', 1)
                                      ->orWhere('old_cc_id', 199) //Welch, Lauren
                                      ->pluck('old_cc_id')
                                      ->unique();
        }
        //dd($valid_campaign_ids);

        $teams = Team::where('app_type', 'office')
                     ->whereIn('old_cc_id', $valid_campaign_ids)
                     ->get();

        foreach ($teams as $team) {
            $options = [];
            $options['--campaign'] = $team->old_cc_id;

            $this->call('st:seed_people', $options);

            if (env('LOCAL_MACHINE') == 'Slothe') {
                $this->cycleThroughAnonymize(Person::all(),
                                            ['full_name', 'full_name_middle', 'primary_email',
                                             'first_name', 'last_name', ]);
            }

            $this->call('st:seed_groups', $options);
            $this->call('st:seed_cases', $options);
            $this->call('st:seed_contacts', $options);
            $this->call('st:seed_files', $options);
            $this->call('st:seed_bulk', $options);

            $team->refreshCount();
        }

        $this->call('cf:populate_districts');
        $this->call('cf:scrape_districts');

        if (env('LOCAL_MACHINE') == 'Slothe') {
            $this->call('cf:populate_slices');
        }

        $this->seederFinish($start);
    }

    public function seederStart()
    {
        return \Carbon\Carbon::now();
    }

    public function seederFinish($start)
    {
        $end = \Carbon\Carbon::now();
        $duration = $end->diffInSeconds($start);
        $duration = round($duration / 60, 2);
        echo str_repeat('-', 76)."\r\n";
        $grand_total = 'Seeding took a grand total of '.$duration.' minutes.';
        $spacer = (76 - strlen($grand_total)) / 2;
        echo str_repeat(' ', $spacer).$grand_total."\r\n";
        echo str_repeat('-', 76)."\r\n";
    }

    public function cycleThroughAnonymize($model, $fields, $exceptions = null)
    {
        $display_field = $fields[0];

        $consonants = ['q', 'w', 'r', 't', 'p', 's', 'd', 'f', 'g', 'h',
                       'j', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'k',
                       'Q', 'W', 'R', 'T', 'P', 'S', 'D', 'F', 'G', 'H',
                       'J', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', 'K', ];

        $use_consonants = ['w', 'r', 't', 'p', 's', 'd', 'f', 'g', 'h',
                           'j', 'l', 'z', 'c', 'v', 'b', 'n', 'm', 'k', ];

        foreach ($model as $key => $row) {
            $do_not_save = false;

            if (isset($exceptions['current_team_id'])) {
                if (in_array($row->current_team_id, $exceptions['current_team_id'])) {
                    $do_not_save = true;
                }
            }

            foreach ($fields as $thefield) {

                //$row->$thefield = Str::random(strlen($row->$thefield));

                // $row->$thefield = ucfirst(str_ireplace($consonants, $consonants[array_rand($consonants)], $row->$thefield));

                $anonymized = null;
                $previous = null;

                for ($i = 0; $i < strlen($row->$thefield); $i++) {
                    $letter = substr($row->$thefield, $i, 1);

                    if (in_array($letter, $consonants)) {
                        if (! in_array($previous, $consonants)) {

                            // Ignore two consonants in a row

                            $anonymized .= $use_consonants[array_rand($use_consonants)];
                        }
                    } else {
                        $anonymized .= $letter; // It's a vowel
                    }

                    $previous = $letter;
                }

                if ($exceptions) {
                    if (isset($exceptions[$thefield])) {
                        if (in_array($row->$thefield, $exceptions[$thefield])) {
                            $do_not_save = true;
                        }
                    }
                }

                $row->$thefield = ucwords($anonymized);
            }

            if (! $do_not_save) {
                $row->save();
                echo 'Anonymizing: '.$key.' '.$display_field.' '.$row->$display_field."\r\n";
            } else {
                echo '**** Skipping '.$row->$thefield."\r\n";
            }
        }
    }
}
