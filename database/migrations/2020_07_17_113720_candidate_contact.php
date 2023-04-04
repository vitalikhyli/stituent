<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CandidateContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('candidate_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->string('type')->nullable();
            $table->string('sequence')->nullable();
            $table->text('notes')->nullable();

            $table->string('mailable')->nullable();
            $table->text('to_emails')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();

            $table->dateTime('last_clicked_at')->nullable();
            $table->text('clicks')->nullable();
            $table->dateTime('responded_at')->nullable();

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
        Schema::dropIfExists('candidate_contacts');
    }
}
