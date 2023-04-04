<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameElectionProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->rename('election_profiles', 'MA_election_profiles');
        Schema::connection('voters')->rename('election_ranges', 'MA_election_ranges');
        Schema::connection('voters')->rename('elections', 'MA_elections');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('voters')->rename('MA_election_profiles', 'election_profiles');
        Schema::connection('voters')->rename('MA_election_ranges', 'election_ranges');
        Schema::connection('voters')->rename('MA_elections', 'elections');
    }
}
