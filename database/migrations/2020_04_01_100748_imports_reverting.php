<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImportsReverting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('imports')->table('imports', function (Blueprint $table) {
            $table->boolean('reverting')->after('changed_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('imports')->create('imports', function (Blueprint $table) {
            $table->dropColumn('reverting');
        });
    }
}
