<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaVotersProcessingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->create('MA_voters_processing', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('voter_id')->index();
            $table->dateTime('elections_processed_at')->nullable();
            $table->dateTime('zip_codes_processed_at')->nullable();
            $table->dateTime('full_name_processed_at')->nullable();
            $table->dateTime('geocoding_processed_at')->nullable();
            $table->dateTime('ward_prec_processed_at')->nullable();
            $table->dateTime('city_code_processed_at')->nullable();
            $table->dateTime('districts_processed_at')->nullable();
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
        Schema::connection('voters')->dropIfExists('ma_voters_processing');
    }
}
