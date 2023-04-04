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

class RestoreAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:restore {--login=} {--live}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores one account, brings over all old data from CC.';

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

        $start = $this->seederStart();


        //==============================================================================>

        $valid_campaign = '';
        if ($this->option('login')) {
            $login = $this->option('login');
            $cc_user = CCUser::where('login', $login)->first();
            $valid_campaign = $cc_user->campaignID;
        } else {
            echo "Gotta have login, like php artisan cf:restore --login=jimmyjones\n";
        }
        //dd($valid_campaign_ids);

        if (!$valid_campaign) {
            echo "No Campaign found for ".$login."\n";
            return;
        }
        $team = Team::where('app_type', 'office')
                     ->where('old_cc_id', $valid_campaign)
                     ->first();

        if (!$team) {
            echo "No Team found for ".$valid_campaign."\n";
            return;
        }

        if (! $this->confirm('Do you wish to continue with '.$team->name.'?')) {
            return;
        }

        // SET VOTER TABLE FOR LOOKUPS
        session(['team_table' => $team->db_slice]);


        $options = [];
        $options['--campaign'] = $team->old_cc_id;
        if ($this->option('live')) {
            session(['live' => true]);
        }

        $this->call('st:seed_people', $options);
        $this->call('st:seed_groups', $options);
        $this->call('st:seed_cases', $options);
        $this->call('st:seed_contacts', $options);
        $this->call('st:seed_bulk', $options);

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

}
