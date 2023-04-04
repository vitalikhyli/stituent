<?php

namespace App\Console\Commands\CC;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class DropAllIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cc:drop_all_indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops all indexes on the cms_voters table in the cc database.';

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
        echo "Starting.\n";

        dd($test);

        $test = DB::connection('cc_live')->statement('TRUNCATE cms_voters');

        Schema::connection('cc_live')->table('cms_voters', function ($table) {
            $table->date('create_date')->default(null)->change();
            $table->date('update_date')->default(null)->change();
            $table->date('deceased_date')->default(null)->change();
            $table->date('archive_date')->default(null)->change();
        });
        echo "Dates set to null default.\n";

        $test = DB::connection('cc_live')->statement('SELECT * from cms_voters LIMIT 5');
        //dd($test);
        echo "Dropping voter_\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX voter_code');
        echo "Dropping idx_ci\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_city_ward');
        echo "Dropping idx_cu\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_custom_district');
        echo "Dropping archiv\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX archive');
        echo "Dropping idx_gd\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_gd');
        echo "Dropping campai\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX campaignID');
        echo "Dropping idx_ho\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_home_phone');
        echo "Dropping full_l\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX full_lastname');
        echo "Dropping idx_ra\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_raddress');
        echo "Dropping idx_al\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_alt_congress');
        echo "Dropping idx_sd\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_sd');
        echo "Dropping idx_al\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_alt_senate');
        echo "Dropping idx_st\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_state_rcity');
        echo "Dropping idx_ca\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_camp_state_archive_names');
        echo "Dropping middle\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX middle');
        echo "Dropping idx_ci\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_city_state_congdist');
        echo "Dropping idx_co\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_county');
        echo "Dropping idx_em\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_email');
        echo "Dropping autoco\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX autocomplete');
        echo "Dropping idx_hd\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_hd');
        echo "Dropping full_f\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX full_firstname');
        echo "Dropping idx_la\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_last_name');
        echo "Dropping full_r\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX full_rcity');
        echo "Dropping idx_rs\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_rstname_archive');
        echo "Dropping idx_al\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_alt_house');
        echo "Dropping idx_st\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_state');
        echo "Dropping idx_ar\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_archive');
        echo "Dropping maddre\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX maddress');
        echo "Dropping idx_cd\n";
        DB::connection('cc_live')->statement('ALTER TABLE cms_voters DROP INDEX idx_cd');
    }
}
