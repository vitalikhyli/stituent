<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYobToVoters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->table('x_voters_MA_master', function (Blueprint $table) {
            $table->unsignedInteger('yob')->nullable()->after('dob');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('voters')->table('x_voters_MA_master', function (Blueprint $table) {
            $table->dropColumn('yob');
        });
    }
}
