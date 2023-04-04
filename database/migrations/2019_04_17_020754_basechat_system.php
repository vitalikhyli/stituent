<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BaseChatSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ======================================> BaseCh@

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('room_id')->index();
            $table->string('file')->nullable();
            $table->boolean('important')->default(false);
            $table->text('message');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('chat_room_access', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type'); // team or user (maybe 'group' in future?)
            $table->unsignedInteger('team_id')->index()->nullable();
            $table->unsignedInteger('user_id')->index()->nullable();
            $table->unsignedInteger('room_id')->index()->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();

            $table->string('name');
            $table->string('slug');
            $table->boolean('external')->index();
            // Used internally to mark one-to-one communication
            $table->boolean('direct')->index();
            // For easy reference, updates whenever message is added to room
            $table->dateTime('last_message_at')->nullable();
            $table->unsignedInteger('member_count')->nullable();

            $table->unsignedInteger('created_by')->index();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('chat_user_memory', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            // array with all rooms user has access to,
            // and last time viewed
            // used to make lookup faster, updated every time rooms changed
            $table->text('recent_rooms')->nullable();
            $table->text('unread_messages')->nullable();
            // So we can load the same room when a new page is loaded
            $table->unsignedInteger('current_room_id')->nullable();
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
        //
    }
}
