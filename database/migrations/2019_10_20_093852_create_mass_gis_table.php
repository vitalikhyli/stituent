<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMassGISTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Schema::connection('voters')->create('mass_gis', function (Blueprint $table) {
        //     $table->decimal('address_long', 10, 7)->index();                //0 => "X"
        //     $table->decimal('address_lat', 10, 7)->index();                 //1 => "Y"
        //     $table->unsignedInteger('id')->index();                         //2 => "OBJECTID"
        //     $table->string('centroid_id');                                  //3 => "CENTROID_ID"
        //     $table->string('address_full_number');                          //4 => "FULL_NUMBER_STANDARDIZED"
        //     $table->string('address_number_prefix');                        //5 => "ADDRESS_NUMBER_PREFIX"
        //     $table->string('address_number');                               //6 => "ADDRESS_NUMBER"
        //     $table->string('address_number_suffix');                        //7 => "ADDRESS_NUMBER_SUFFIX"
        //     $table->string('address_number_2_prefix');                      //8 => "ADDRESS_NUMBER_2_PREFIX"
        //     $table->string('address_number_2');                             //9 => "ADDRESS_NUMBER_2"
        //     $table->string('address_number_2_suffix');                      //10 => "ADDRESS_NUMBER_2_SUFFIX"
        //     $table->string('street_name');                                  //11 => "STREET_NAME"
        //     $table->string('floor');                                        //12 => "FLOOR"
        //     $table->string('unit');                                         //13 => "UNIT"
        //     $table->string('master_address_id');                            //14 => "MASTER_ADDRESS_ID"
        //     $table->string('street_name_id');                               //15 => "STREET_NAME_ID"
        //     $table->string('rel_loc');                                      //16 => "REL_LOC"
        //     $table->string('site_id');                                      //17 => "SITE_ID"
        //     $table->string('building_name');                                //18 => "BUILDING_NAME"
        //     $table->unsignedInteger('geographic_town_id');                  //19 => "GEOGRAPHIC_TOWN_ID"
        //     $table->unsignedInteger('community_id');                        //20 => "COMMUNITY_ID"
        //     $table->string('community_name');                               //21 => "COMMUNITY_NAME"
        //     $table->string('geographic_town');                              //22 => "GEOGRAPHIC_TOWN"
        //     $table->string('postcode');                                     //23 => "POSTCODE"
        //     $table->string('pc_name');                                      //24 => "PC_NAME"
        //     $table->string('county');                                       //25 => "COUNTY"
        //     $table->string('state');                                        //26 => "STATE"
        // });

        // Schema::connection('voters')->table('x_voters_MA_master', function (Blueprint $table) {
        //     $table->unsignedInteger('mass_gis_id')->after('household_id')->index()->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        Schema::connection('voters')->dropIfExists('mass_gis');
        Schema::connection('voters')->table('x_voters_MA_master', function (Blueprint $table) {
            $table->dropColumn('mass_gis_id');
        });
        */
    }
}
