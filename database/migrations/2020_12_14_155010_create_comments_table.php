<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('idea')->default(false);
            $table->boolean('endorsement')->default(false);
            $table->string('subject')->nullable();
            $table->text('notes')->nullable();
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->string('state')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->text('closing_notes')->nullable();
            $table->unsignedInteger('closed_by')->nullable();
            $table->unsignedInteger('up')->default(0);
            $table->text('up_users')->nullable();
            $table->unsignedInteger('down')->default(0);
            $table->text('down_users')->nullable();
            $table->float('score')->default(0);
            $table->unsignedInteger('votes')->default(0);
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
        Schema::dropIfExists('comments');
    }
}
