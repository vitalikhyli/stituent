<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('imports')->create('imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('municipality_id')->nullable();

            $table->string('file')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->text('column_map')->nullable();
            $table->string('table_name')->nullable();

            $table->unsignedInteger('file_count')->nullable();
            $table->dateTime('started_at')->nullable();

            $table->unsignedInteger('imported_count')->nullable();
            $table->dateTime('imported_at')->nullable();

            $table->unsignedInteger('verified_count')->nullable();
            $table->dateTime('verified_at')->nullable();

            $table->unsignedInteger('new_count')->nullable();
            $table->unsignedInteger('updated_count')->nullable();
            $table->dateTime('completed_at')->nullable();

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
        Schema::connection('imports')->dropIfExists('imports');
    }
}
