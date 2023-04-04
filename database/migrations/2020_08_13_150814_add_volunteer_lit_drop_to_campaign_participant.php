<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVolunteerLitDropToCampaignParticipant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_participant', function (Blueprint $table) {
            $table->boolean('volunteer_lit_drop')->after('volunteer_caravan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_participant', function (Blueprint $table) {
            $table->dropColumn('volunteer_lit_drop');
        });
    }
}
