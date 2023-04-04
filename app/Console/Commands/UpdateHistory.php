<?php

namespace App\Console\Commands;

use App\Account;
use App\HistoryItem;
use App\Models\Admin\AdminHistoryItem;
use App\Person;
use App\Team;
use App\User;
use App\WorkCase;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:update_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update daily history table';

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
        $teams = Team::all();
        foreach ($teams as $theteam) {
            $item = new HistoryItem;

            $item->team_id = $theteam->id;

            $item->num_people = Person::where('team_id', $theteam->id)
                                      ->count();

            $item->num_cases_open = WorkCase::where('team_id', $theteam->id)
                                            ->where('resolved', 0)
                                            ->count();

            $item->num_cases_resolved = WorkCase::where('team_id', $theteam->id)
                                                ->where('resolved', 1)
                                                ->count();

            $item->num_cases_new = WorkCase::where('team_id', $theteam->id)
                                           ->whereDate('created_at', '>', Carbon::now()->subDays(1))
                                           ->count();

            $item->num_contacts_new = WorkCase::where('team_id', $theteam->id)
                                           ->whereDate('created_at', '>', Carbon::now()->subDays(1))
                                           ->count();

            // $num_emails = 0;
            // $num_phones = 0;
            // foreach (Person::where('team_id', $theteam->id)->get() as $theperson) {
            //     $num_emails += count(json_decode($theperson->email));
            //     $num_phones += count(json_decode($theperson->phone));
            // }
            // $item->num_emails = $num_emails;
            // $item->num_phones = $num_phones;
            $item->save();
        }

        $item = new AdminHistoryItem;
        $item->num_accounts = Account::where('active', 1)->count();
        $item->num_users = User::count();
        $item->save();

        echo 'History Updated for Admin and '.$teams->count()." teams\r\n";
    }
}
