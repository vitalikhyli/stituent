<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UploadsMatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_uploads', function (Blueprint $table) {
            $table->unsignedInteger('matched_count')->after('count')->default(0);
        });

        Schema::table('user_uploads_data', function (Blueprint $table) {
            $table->unsignedInteger('line')->after('team_id')->nullable();
            $table->dateTime('matched_at')->after('hash')->nullable();
            $table->unsignedInteger('person_id')->after('voter_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_uploads', function (Blueprint $table) {
            $table->dropColumn('matched_count');
        });
        Schema::table('user_uploads_data', function (Blueprint $table) {
            $table->dropColumn('line');
            $table->dropColumn('matched_at');
            $table->dropColumn('person_id');
        });
    }
}
