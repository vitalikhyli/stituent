<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkSentEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_sent_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('bulk_email_id')->index();
            $table->unsignedInteger('queue_id')->index();
            $table->string('name')->index()->nullable();
            $table->string('email')->index();
            $table->string('subject')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->text('error')->nullable();
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
        Schema::dropIfExists('bulk_sent_emails');
    }
}
