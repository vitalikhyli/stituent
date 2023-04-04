<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryUnslothLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_unsloth_logs', function (Blueprint $table) {
            $table->id();

            $table->string('hash')->nullable();
            $table->boolean('auth')->nullable();
            $table->float('time')->nullable();
            $table->text('explain')->nullable();
            $table->string('explain_type')->nullable();
            $table->text('sql')->nullable();
            $table->text('sql_full')->nullable();
            $table->text('tables')->nullable();
            $table->text('bindings')->nullable();
            $table->date('date')->nullable();

            $table->index('hash');
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
        Schema::dropIfExists('query_unsloth_logs');
    }
}
