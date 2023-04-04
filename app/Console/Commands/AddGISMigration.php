<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGISMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:add_gis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        /* TOOK TOO LONG
        echo date('Y-m-d h:i:s')." changing lat/long fields.\n";
        Schema::connection('voters')->table('x_voters_MA_master', function (Blueprint $table) {
            $table->decimal('address_lat', 10, 7)->nullable()->change();
            $table->decimal('address_long', 10, 7)->nullable()->change();
        });
        */

        if (! Schema::connection('voters')->hasTable('mass_gis')) {
            echo "Creating Mass_GIS table...\r\n";
            $this->create_GIS_Table();
        }

        echo date('Y-m-d h:i:s')." adding people gis_id\n";
        if (! Schema::hasColumn('people', 'mass_gis_id')) {
            Schema::table('people', function (Blueprint $table) {
                $table->unsignedInteger('mass_gis_id')->after('household_id')->index()->nullable();
            });
        }

        $indexes = [
            ['geographic_town_id', 'street_name', 'address_number'],
            ['geographic_town', 'street_name', 'address_number'],
            ['community_name', 'street_name', 'address_number'],
        ];
        foreach ($indexes as $index_arr) {
            $this->addIndexToTable('mass_gis', $index_arr);
        }
    }

    public function create_GIS_Table()
    {
        Schema::connection('voters')->create('mass_gis', function (Blueprint $table) {
            $table->decimal('address_long', 10, 7)->index();                //0 => "X"
            $table->decimal('address_lat', 10, 7)->index();                 //1 => "Y"
            $table->unsignedInteger('id')->index();                         //2 => "OBJECTID"
            $table->string('centroid_id');                                  //3 => "CENTROID_ID"
            $table->string('address_full_number');                          //4 => "FULL_NUMBER_STANDARDIZED"
            $table->string('address_number_prefix');                        //5 => "ADDRESS_NUMBER_PREFIX"
            $table->string('address_number');                               //6 => "ADDRESS_NUMBER"
            $table->string('address_number_suffix');                        //7 => "ADDRESS_NUMBER_SUFFIX"
            $table->string('address_number_2_prefix');                      //8 => "ADDRESS_NUMBER_2_PREFIX"
            $table->string('address_number_2');                             //9 => "ADDRESS_NUMBER_2"
            $table->string('address_number_2_suffix');                      //10 => "ADDRESS_NUMBER_2_SUFFIX"
            $table->string('street_name');                                  //11 => "STREET_NAME"
            $table->string('floor');                                        //12 => "FLOOR"
            $table->string('unit');                                         //13 => "UNIT"
            $table->string('master_address_id');                            //14 => "MASTER_ADDRESS_ID"
            $table->string('street_name_id');                               //15 => "STREET_NAME_ID"
            $table->string('rel_loc');                                      //16 => "REL_LOC"
            $table->string('site_id');                                      //17 => "SITE_ID"
            $table->string('building_name');                                //18 => "BUILDING_NAME"
            $table->unsignedInteger('geographic_town_id');                  //19 => "GEOGRAPHIC_TOWN_ID"
            $table->unsignedInteger('community_id');                        //20 => "COMMUNITY_ID"
            $table->string('community_name');                               //21 => "COMMUNITY_NAME"
            $table->string('geographic_town');                              //22 => "GEOGRAPHIC_TOWN"
            $table->string('postcode');                                     //23 => "POSTCODE"
            $table->string('pc_name');                                      //24 => "PC_NAME"
            $table->string('county');                                       //25 => "COUNTY"
            $table->string('state');                                        //26 => "STATE"
        });

        Schema::connection('voters')->table('x_voters_MA_master', function (Blueprint $table) {
            $table->unsignedInteger('mass_gis_id')->after('household_id')->index()->nullable();
        });
    }

    public function addIndexToTable($tablename, $index_arr)
    {
        //dd($index_arr);
        echo date('Y-m-d h:i:s', time())." - $tablename: ".implode(', ', $index_arr);
        Schema::connection('voters')->table($tablename, function (Blueprint $table) use ($index_arr, $tablename) {
            $index_name = 'idx_'.implode('_', $index_arr);
            $existing_indexes = collect(DB::connection('voters')->select('SHOW INDEXES FROM '.$tablename))
                                    ->pluck('Key_name');
            //dd($existing_indexes);
            if ($existing_indexes->contains($index_name)) {
                echo " => Exists, SKIPPING.\n";

                return;
            } else {
                echo "\n";
            }
            $table->index($index_arr, $index_name);
        });
    }
}
