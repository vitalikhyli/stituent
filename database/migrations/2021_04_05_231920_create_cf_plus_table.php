<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfPlusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('voters')->create('CF_PLUS_FULL', function (Blueprint $table) {
            $table->id();
            $table->string('import');
            $table->string('SEQUENCE');
            $table->string('LALVOTERID');
            $table->string('Voters_StateVoterID');
            $table->string('voter_id')->index()->nullable();
            $table->text('full_data');
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
        Schema::connection('voters')->dropIfExists('CF_PLUS_FULL');
    }
}
