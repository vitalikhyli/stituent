<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('nickname')->after('first_name')->nullable();
            $table->string('pronouns')->after('gender')->nullable();
            $table->text('social_media')->after('massemail_neversend')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('nickname');
            $table->dropColumn('pronouns');
            $table->dropColumn('social_media');
        });
    }
}
