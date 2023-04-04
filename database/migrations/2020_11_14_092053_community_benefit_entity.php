<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CommunityBenefitEntity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_benefit_entity', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('community_benefit_id');
            $table->unsignedInteger('entity_id');
            $table->boolean('beneficiary')->default(false);
            $table->boolean('initiator')->default(false);
            $table->boolean('partner')->default(false);
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('community_benefit_entity');
    }
}
