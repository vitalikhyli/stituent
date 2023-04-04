<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_pages', function (Blueprint $table) {
            $table->id();
            $table->string('app')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->boolean('anonymous')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('admin_comment')->nullable();
            $table->text('stars')->nullable();
            $table->string('live_link')->nullable();
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
        Schema::dropIfExists('special_pages');
    }
}
