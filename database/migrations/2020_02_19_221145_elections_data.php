<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ElectionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->dropIfExists('elections');

        Schema::connection('voters')->dropIfExists('election_summaries');

        Schema::connection('voters')->create('elections', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('state');
            $table->date('date');
            $table->unsignedInteger('voters_count')->nullable();
            $table->boolean('presidential')->default(false);
            $table->boolean('statewide')->default(false);
            $table->boolean('local')->default(false);
            $table->unsignedInteger('city_code')->nullable();
            $table->boolean('general')->default(false);
            $table->boolean('primary')->default(false);
            $table->boolean('special')->default(false);
            $table->boolean('town_meeting')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // Schema::connection('voters')->create('election_summaries', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->string('state');
        //     $table->unsignedInteger('year');

        //     // Totals (Usually 1 or 0)
        //     $table->unsignedInteger('statewide_any')->nullable();
        //     $table->unsignedInteger('statewide_general')->nullable();
        //     $table->unsignedInteger('statewide_primary')->nullable();
        //     $table->unsignedInteger('statewide_special_general')->nullable();
        //     $table->unsignedInteger('statewide_special_special')->nullable();
        //     $table->unsignedInteger('statewide_presidential_general')->nullable();
        //     $table->unsignedInteger('statewide_presidential_primary')->nullable();

        //     // Totals by town in JSON
        //     $table->text('local_any')->nullable();
        //     $table->text('local_general')->nullable();
        //     $table->text('local_preliminary')->nullable();
        //     $table->text('local_special_general')->nullable();
        //     $table->text('local_special_preliminary')->nullable();
        //     $table->text('local_town_meeting')->nullable();

        //     $table->timestamps();
        // });

        Schema::connection('voters')->create('election_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('voter_id')->index();

            $current_year = 20; //Last two digits of the current year

            foreach ([$current_year - 2,
                     $current_year - 4,
                     $current_year - 6,
                     $current_year - 8, ] as $range) {
                $range = $range.$current_year.'_';

                // Make these tiny ints for two digits

                $table->tinyInteger($range.'any')->nullable();
                $table->tinyInteger($range.'state_any')->nullable();
                $table->tinyInteger($range.'state_general')->nullable();
                $table->tinyInteger($range.'state_general_gub')->nullable();
                $table->tinyInteger($range.'state_primary')->nullable();
                $table->tinyInteger($range.'state_primary_gub')->nullable();
                $table->tinyInteger($range.'state_special_general')->nullable();
                $table->tinyInteger($range.'state_special_primary')->nullable();
                $table->tinyInteger($range.'state_presidential_primary')->nullable();
                $table->tinyInteger($range.'local_any')->nullable();
                $table->tinyInteger($range.'local_general')->nullable();
                $table->tinyInteger($range.'local_preliminary')->nullable();
                $table->tinyInteger($range.'local_town_meeting')->nullable();

                $table->tinyInteger($range.'local_special_general')->nullable(); //LS exists

                // NOT CURRENTLY USED
                // $table->tinyInteger($range.'local_special_preliminary')->nullable();
            }
            $table->timestamps();
        });

        Schema::connection('voters')->create('election_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('voter_id')->index();

            //////// SUPER VOTER
            $table->boolean('stalwart_local')->nullable();
            $table->boolean('stalwart_state')->nullable();

            //////// RELIABLE
            $table->boolean('reliable_local')->nullable();          // >= 3/4 consec local
            $table->boolean('reliable_state')->nullable();          // >= 3/4 consec local

            //////// MEDIUM
            $table->boolean('somewhat_local')->nullable();
            $table->boolean('somewhat_state')->nullable();

            $table->string('previous_parties', 3)->nullable();
            $table->boolean('recently_registered')->nullable();     // In last 365 days
            $table->boolean('recently_moved_here')->nullable();     // In last 365 days

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('voters')->dropIfExists('elections');
        // Schema::connection('voters')->dropIfExists('election_summaries');
        Schema::connection('voters')->dropIfExists('election_ranges');
        Schema::connection('voters')->dropIfExists('election_profiles');
    }
}
