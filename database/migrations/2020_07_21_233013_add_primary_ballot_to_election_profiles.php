<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrimaryBallotToElectionProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->table('MA_election_profiles', function (Blueprint $table) {
            $table->string('primary_ballot_2016')->after('recently_moved_here')->index()->nullable();
            $table->string('primary_ballot_2018')->after('primary_ballot_2016')->index()->nullable();
            $table->string('primary_ballot_2020')->after('primary_ballot_2018')->index()->nullable();
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
            $table->dropColumn('primary_ballot_2016');
            $table->dropColumn('primary_ballot_2018');
            $table->dropColumn('primary_ballot_2020');
        });
    }
}
