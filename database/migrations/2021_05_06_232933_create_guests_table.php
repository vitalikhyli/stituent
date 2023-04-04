<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('team_id')->nullable();
            $table->string('uuid')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('active')->default(true);
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('title')->nullable();
            $table->string('password')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->text('notified_at')->nullable();
            $table->dateTime('uuid_expires_at')->nullable();
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
        Schema::dropIfExists('guests');
    }
}
