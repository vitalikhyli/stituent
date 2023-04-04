<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('list_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('participant_id')->nullable();
            $table->string('type')->nullable();
            $table->string('uuid')->nullable();
            $table->string('reserved')->nullable();
            $table->unsignedInteger('stats_total')->nullable();
            $table->unsignedInteger('stats_1')->nullable();
            $table->unsignedInteger('stats_2')->nullable();
            $table->unsignedInteger('stats_3')->nullable();
            $table->unsignedInteger('stats_4')->nullable();
            $table->unsignedInteger('stats_5')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->dateTime('emailed_at')->nullable();
            $table->unsignedInteger('clicks_count')->nullable();
            $table->unsignedInteger('created_by')->nullable();
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
        Schema::dropIfExists('list_user');
    }
}
