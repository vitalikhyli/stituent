<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\CampaignParticipant;
use Str;

class UpdateVolunteerOptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:update_volunteer_options';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keeps all volunteer options updated, changes Schema';

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
        // House party
        // Table
        // Lawn sign
        // Lit drop
        // Visibility pre-Election Day
        // Volunteer on Election Day
        // Write LTE
        // Phone calls
        // Door knock
        // Introduce GL to your neighborhood
        // Supporter list & sig ad
        // Ward Chair
        // Other
        // General
        // Here is what we want it to look like (add but NO re-ordering!)
        $volunteer_options = [
            "house_party",
            "table",
            "lawnsign",
            "general",
            "door_knock",
            "phone_calls",
            "hold_signs",
            "election_day",
            "office_work",
            "write_letters",
            "caravan",
            "lit_drop",
            "poll_watch",
            "ward_chair",
            "signatures",
            "website_list",
            "other",
        ];

        $current_volunteer_options = [];
        $attributes = CampaignParticipant::first()->getAttributes();
        foreach ($attributes as $attribute => $val) {
            if (Str::contains($attribute, 'volunteer')) {
                $current_volunteer_options[] = str_replace('volunteer_', '', $attribute);
            }
        }

        $differences = array_diff($volunteer_options, $current_volunteer_options);
        //dd($attributes, $current_volunteer_options, $differences);

        Schema::table('campaign_participant', function (Blueprint $table) use ($differences, $current_volunteer_options, $volunteer_options) {
            foreach ($differences as $key => $field) {

                $after = "support";
                if ($key > 0) {
                    $after = "volunteer_".$volunteer_options[$key-1];
                }
                $column = "volunteer_".$field;
                $table->boolean($column)->after($after)->default(false);
                echo "$column AFTER $after\n";
                
            }
        });
        
        $this->info('Remember to add to Participant->getVolunteerColumns()');
    }
}
