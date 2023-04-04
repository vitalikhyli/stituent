<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TeamworkSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Config::get('teamwork.users_table'), function (Blueprint $table) {
            $table->integer('current_team_id')->unsigned()->after('id')->nullable();
        });

        Schema::create(\Config::get('teamwork.teams_table'), function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('account_id')->index();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('data_folder_id'); //MA, CT
            $table->string('db_slice')->nullable();

            $table->string('name');
            $table->string('short_name')->nullable();

            $table->boolean('active')->default(true);
            $table->dateTime('activated_at')->nullable();

            $table->string('app_type')->default('official');
            $table->string('district_name')->nullable();
            $table->string('district_type')->nullable(); // H, S, etc
            $table->unsignedInteger('district_id')->nullable();

            $table->boolean('admin')->default(0);
            $table->boolean('pilot')->default(0);

            $table->unsignedInteger('constituents_count')->nullable();
            $table->unsignedInteger('households_count')->nullable();

            $table->string('facebook_url')->nullable();

            $table->string('logo_img')->nullable();
            $table->unsignedInteger('logo_width')->nullable();
            $table->unsignedInteger('logo_height')->nullable();
            $table->string('logo_orient', 9)->nullable();

            $table->unsignedInteger('old_cc_id')->nullable()->index();

            $table->timestamps();
        });

        Schema::create(\Config::get('teamwork.team_user_table'), function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->timestamps();
        });

        Schema::create(\Config::get('teamwork.team_invites_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('team_id')->unsigned();
            $table->enum('type', ['invite', 'request']);
            $table->string('email');
            $table->string('accept_token');
            $table->string('deny_token');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('team_id')->index();

            $table->string('title')->nullable();

            $table->boolean('developer')->default(false);
            $table->boolean('admin')->default(false);
            $table->boolean('campaign')->default(false);
            $table->boolean('chat_external')->default(false);
            $table->boolean('chat')->default(true);
            $table->boolean('reports')->default(true);
            $table->boolean('metrics')->default(true);
            $table->boolean('constituents')->default(true);
            $table->boolean('creategroups')->default(false);

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
        Schema::table(\Config::get('teamwork.users_table'), function (Blueprint $table) {
            $table->dropColumn('current_team_id');
        });

        Schema::drop(\Config::get('teamwork.team_user_table'));
        Schema::drop(\Config::get('teamwork.team_invites_table'));
        Schema::drop(\Config::get('teamwork.teams_table'));
    }
}
