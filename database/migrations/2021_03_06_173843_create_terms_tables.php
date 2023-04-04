<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->dateTime('effective_at')->nullable();
            $table->boolean('publish')->default(false);
            $table->boolean('needs_acceptance')->default(true);
            $table->timestamps();
        });

        Schema::create('term_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('term_id');
            $table->unsignedInteger('user_id');
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->text('user_teams')->nullable();
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('account_name')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->datetime('notified_at')->nullable();
            $table->datetime('notified_by_email_at')->nullable();
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
        Schema::dropIfExists('terms');
        Schema::dropIfExists('term_user');
    }
}
