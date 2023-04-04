<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->string('unique_id')->nullable();
            $table->string('name')->nullable();
            $table->string('button')->nullable();
            $table->boolean('active')->default(true);
            $table->text('options')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('web_signups', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('web_form_id')->nullable();

            $table->unsignedInteger('participant_id')->nullable();
            $table->unsignedInteger('voter_id')->nullable();

            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('note')->nullable();

            $table->text('data')->nullable();
            $table->text('meta')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('web_forms');
        Schema::dropIfExists('web_signups');
    }
}
