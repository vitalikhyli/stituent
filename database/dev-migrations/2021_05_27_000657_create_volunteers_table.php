<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('guests');

        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('participant_id')->nullable();
            $table->string('voter_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->text('types')->nullable();
            $table->boolean('active')->default(true);
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('title')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->text('notified_at')->nullable();
            $table->dateTime('uuid_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volunteers');
    }
}
