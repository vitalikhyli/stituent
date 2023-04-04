<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnarchivedCountToVoterSlices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voter_slices', function (Blueprint $table) {
            $table->unsignedInteger('unarchived_count')->after('voters_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voter_slices', function (Blueprint $table) {
            $table->dropColumn('unarchived_count');
        });
    }
}
