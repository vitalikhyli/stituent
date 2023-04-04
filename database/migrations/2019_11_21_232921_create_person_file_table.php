<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_file', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('person_id')->index();
            $table->unsignedInteger('file_id')->index();
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
        Schema::dropIfExists('person_file');
    }
}
