<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();

            $table->string('team_name')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->string('user_name')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->string('device_id')->nullable();
            $table->string('api_key')->nullable();
            $table->text('device_info')->nullable();
            $table->string('pin')->nullable();

            $table->string('team_state')->nullable();
            $table->string('team_table')->nullable();

            $table->dateTime('live_at')->nullable();
            
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
        Schema::dropIfExists('devices');
    }
}
