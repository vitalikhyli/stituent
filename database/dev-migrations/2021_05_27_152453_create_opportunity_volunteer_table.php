<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpportunityVolunteerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunity_volunteer', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('opportunity_id');
            $table->unsignedInteger('volunteer_id');
            $table->dateTime('emailed_at')->nullable();
            $table->dateTime('followed_up_at')->nullable();
            $table->boolean('self_invited')->default(false);
            $table->boolean('participated')->default(false);
            $table->boolean('declined')->default(false);
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
        Schema::dropIfExists('opportunity_volunteer');
    }
}
