<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaticDoorsCountToCampaignLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_lists', function (Blueprint $table) {
            $table->unsignedInteger('static_count_doors')->nullable()->after('static_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_lists', function (Blueprint $table) {
            $table->dropColumn('static_count_doors');
        });
    }
}
