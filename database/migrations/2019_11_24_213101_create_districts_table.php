<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->nullable();
            $table->unsignedInteger('code')->index()->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('sort')->nullable();

            $table->text('description')->nullable();
            $table->string('elected_official_name')->nullable();
            $table->string('elected_official_party')->nullable();
            $table->string('elected_official_residence')->nullable();
            $table->string('elected_official_started')->nullable();

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
        Schema::dropIfExists('districts');
    }
}
