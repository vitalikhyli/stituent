<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_entities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('entity_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('pattern_id')->nullable();
            $table->boolean('private')->default(false);
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('contact_id')->nullable();
            $table->string('step')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_user_goals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->unsignedInteger('year')->nullable();
            $table->unsignedInteger('quarter')->nullable();
            $table->unsignedInteger('month')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_patterns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('default_type')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_steps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('pattern_id')->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('the_order')->nullable();
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
        Schema::dropIfExists('sales_entities');
        Schema::dropIfExists('sales_contacts');
        Schema::dropIfExists('sales_teams');
        Schema::dropIfExists('sales_user_goals');
        Schema::dropIfExists('sales_patterns');
        Schema::dropIfExists('sales_steps');
    }
}
