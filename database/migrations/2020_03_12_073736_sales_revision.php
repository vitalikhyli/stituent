<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SalesRevision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_entities', function (Blueprint $table) {
            $table->unsignedInteger('cf_id')->after('type')->nullable();
            $table->unsignedInteger('bg_id')->after('cf_id')->nullable();
            $table->boolean('client')->after('bg_id')->default(false);
            $table->date('next_check_in')->after('client')->nullable();
            $table->unsignedInteger('days_check_in')->after('next_check_in')->nullable();
        });

        Schema::table('sales_contacts', function (Blueprint $table) {
            $table->unsignedInteger('amount_secured')->after('step')->nullable();
            $table->boolean('check_in')->after('amount_secured')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_entities', function (Blueprint $table) {
            $table->dropColumn('cf_id');
            $table->dropColumn('bg_id');
            $table->dropColumn('client');
            $table->dropColumn('next_check_in');
            $table->dropColumn('days_check_in');
        });

        Schema::table('sales_contacts', function (Blueprint $table) {
            $table->dropColumn('amount_secured');
            $table->dropColumn('check_in');
        });
    }
}
