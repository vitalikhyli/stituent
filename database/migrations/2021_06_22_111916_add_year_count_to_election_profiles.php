<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearCountToElectionProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->table('MA_election_profiles', function (Blueprint $table) {
            $table->text('year_count')->after('voter_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('voters')->table('MA_election_profiles', function (Blueprint $table) {
            $table->dropColumn('year_count');
        });
    }
}
