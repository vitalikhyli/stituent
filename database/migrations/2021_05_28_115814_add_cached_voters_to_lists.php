<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCachedVotersToLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_lists', function (Blueprint $table) {
            $table->boolean('dynamic')->after('form')->default(false);
            $table->longText('cached_voters')->after('mail_data')->nullable();
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
            $table->dropColumn('dynamic');
            $table->dropColumn('cached_voters');
        });
    }
}
