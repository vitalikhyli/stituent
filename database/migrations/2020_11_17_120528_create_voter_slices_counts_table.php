<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoterSlicesCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voter_slices_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('slice_id');
            $table->text('slice')->nullable();
            $table->text('municipalities')->nullable();
            $table->text('counties')->nullable();
            $table->text('congress_districts')->nullable();
            $table->text('house_districts')->nullable();
            $table->text('senate_districts')->nullable();
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
        Schema::dropIfExists('voter_slices_counts');
    }
}
