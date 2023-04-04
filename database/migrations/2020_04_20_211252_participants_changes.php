<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ParticipantsChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedInteger('upload_id')->after('notes')->nullable();

            $table->string('primary_email')->after('email')->nullable();
            $table->string('work_email')->after('primary_email')->nullable();
            $table->text('other_emails')->after('work_email')->nullable();
            $table->dropColumn('email');

            $table->string('primary_phone')->after('phone')->nullable();
            $table->string('work_phone')->after('primary_phone')->nullable();
            $table->text('other_phones')->after('work_phone')->nullable();
            $table->dropColumn('phone');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->unsignedInteger('upload_id')->after('old_voter_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('upload_id');

            $table->dropColumn('primary_email');
            $table->dropColumn('work_email');
            $table->dropColumn('other_emails');
            $table->string('email')->after('primary_email')->nullable();

            $table->dropColumn('primary_phone');
            $table->dropColumn('work_phone');
            $table->dropColumn('other_phones');
            $table->string('phone')->after('primary_phone')->nullable();
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('upload_id');
        });
    }
}
