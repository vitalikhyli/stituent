<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_log_apps', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2084)->nullable();

            $table->unsignedInteger('user_id')->index()->nullable();
            $table->unsignedInteger('team_id')->index()->nullable();
            $table->string('name')->nullable();
            $table->string('team_name')->nullable();
            
            $table->string('type')->nullable();
            $table->text('debug')->nullable();
            $table->float('time', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_log_apps');
    }
}
