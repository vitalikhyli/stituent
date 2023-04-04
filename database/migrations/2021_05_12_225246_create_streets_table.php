<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->create('x_MA_streets', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->unsignedInteger('city_code')->index()->nullable();
            $table->unsignedInteger('voter_count')->nullable();
            $table->unsignedInteger('house_count')->nullable();
            $table->unsignedInteger('min_num')->nullable();
            $table->unsignedInteger('max_num')->nullable();
            $table->decimal('lat_min', 10, 7)->nullable();
            $table->decimal('lat_max', 10, 7)->nullable();
            $table->decimal('long_min', 10, 7)->nullable();
            $table->decimal('long_max', 10, 7)->nullable();
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
        Schema::connection('voters')->dropIfExists('x_MA_streets');
    }
}
