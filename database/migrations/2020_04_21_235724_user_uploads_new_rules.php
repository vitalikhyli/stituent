<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserUploadsNewRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_uploads', function (Blueprint $table) {
            $table->string('new_rules')->after('new_if_unmatched')->nullable();
            $table->dropColumn('new_if_unmatched');
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
            $table->boolean('new_if_unmatched')->after('new_rules')->default(0);
            $table->dropColumn('new_rules');
        });
    }
}
