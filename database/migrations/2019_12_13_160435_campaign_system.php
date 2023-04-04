<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CampaignSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('name')->nullable();
            $table->date('due')->nullable();
            $table->boolean('done')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('questionnaire_id')->index();
            $table->unsignedInteger('assigned_to')->index();
            $table->unsignedInteger('the_order')->nullable();
            $table->string('question')->nullable();
            $table->text('description')->nullable();
            $table->text('answer')->nullable();
            $table->boolean('done')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('campaigns');
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('name')->nullable();
            $table->date('election_day')->nullable();
            $table->boolean('current')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('campaign_lists');
        Schema::create('campaign_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('name');
            $table->text('form');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('list_participant');
        Schema::create('list_participant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('voter_id')->nullable()->index();
            $table->unsignedInteger('list_id')->index();
            $table->unsignedInteger('participant_id')->index();
            $table->boolean('excluded')->default(false);
            $table->timestamps();
        });

        Schema::dropIfExists('tags');
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('participant_tag');
        Schema::create('participant_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('voter_id')->nullable()->index();

            $table->unsignedInteger('participant_id')->index();
            $table->unsignedInteger('tag_id')->index();
            $table->timestamps();
        });

        Schema::dropIfExists('participants');
        Schema::create('participants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();

            $table->string('voter_id')->nullable()->index();
            $table->unsignedInteger('crossteam_person_id')->nullable()->index();

            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('preferred_name')->nullable();

            $table->string('full_address')->nullable();
            $table->unsignedInteger('city_code')->index()->nullable();
            $table->string('address_number')->index()->nullable();
            $table->string('address_fraction')->nullable();
            $table->string('address_street')->index()->nullable();
            $table->string('address_apt')->nullable();
            $table->string('address_city')->index()->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_zip')->index()->nullable();

            $table->string('gender')->index()->nullable();
            $table->string('party', 3)->index()->nullable();
            $table->string('ward')->nullable()->index();
            $table->string('precinct')->nullable()->index();

            $table->unsignedInteger('congress_district')->nullable()->index();
            $table->unsignedInteger('senate_district')->nullable()->index();
            $table->unsignedInteger('house_district')->nullable()->index();

            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('campaign_participant');
        Schema::create('campaign_participant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('campaign_id')->nullable()->index();
            $table->unsignedInteger('participant_id')->nullable()->index();
            $table->string('voter_id')->nullable()->index();

            $table->unsignedInteger('support')->index()->nullable();
            $table->boolean('volunteer_lawnsign')->default(false);
            $table->boolean('volunteer_general')->default(false);
            $table->boolean('volunteer_door_knock')->default(false);
            $table->boolean('volunteer_phone_calls')->default(false);
            $table->boolean('volunteer_hold_signs')->default(false);
            $table->boolean('volunteer_office_work')->default(false);
            $table->boolean('volunteer_write_letters')->default(false);

            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('donations'); // Created Previously

        Schema::create('donations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('participant_id')->nullable()->index();
            $table->string('voter_id')->nullable()->index();

            $table->date('date')->nullable();
            $table->unsignedInteger('campaign_event_id')->nullable();
            $table->string('method')->nullable();
            $table->decimal('amount', 7, 2)->default(0);
            $table->decimal('fee', 5, 2)->default(0);

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();

            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();

            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('campaign_events');
        Schema::create('campaign_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();

            $table->date('date')->nullable();
            $table->string('time')->nullable();

            $table->string('name');
            $table->string('venue_name')->nullable();
            $table->string('venue_street')->nullable();
            $table->string('venue_city')->nullable();
            $table->string('venue_state')->nullable();
            $table->string('venue_zip')->nullable();

            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('campaign_event_invites');
        Schema::create('campaign_event_invites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('campaign_event_id')->nullable();
            $table->unsignedInteger('participant_id')->nullable()->index();
            $table->string('voter_id')->nullable()->index();

            $table->boolean('can_attend')->default(false);
            $table->boolean('comped')->default(false);
            $table->unsignedInteger('guests')->default(0);
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
        Schema::dropIfExists('questionnaires');
        Schema::dropIfExists('questions');

        Schema::dropIfExists('participants');
        Schema::dropIfExists('lists');
        Schema::dropIfExists('list_participant');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('participant_tag');
        Schema::dropIfExists('campaign_participant');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('campaign_events');
        Schema::dropIfExists('campaign_event_invites');
        Schema::dropIfExists('lists');
    }
}
