<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;
use App\SpecialPage;

class SeedSpecialPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:special_pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the initial special pages for campaign and office';

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

        $link = '/campaign/special/household-members';
        $sp = SpecialPage::where('live_link', $link)->first();
        if (!$sp) {
            $sp = new SpecialPage;
        }
        $sp->live_link = $link;
        $sp->user_id = '257';
        $sp->team_id = '1';
        $sp->app = 'campaign';
        $sp->name = "Households of ID'd Voters";
        $sp->description = "A page to show everyone else in each household where there is an ID'd voter. This allows users to see other potential voters who may vote similarly as the ones they Id'd.";
        $sp->anonymous = true;
        $sp->stars = [];
        $sp->save();
    }
}
