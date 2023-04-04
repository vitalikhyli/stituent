<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReservedExpiresAtToListUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_user', function (Blueprint $table) {
            $table->text('reserved_expires_at')->nullable()->after('reserved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_user', function (Blueprint $table) {
            $table->dropColumn('reserved_expires_at');
        });
    }
}
