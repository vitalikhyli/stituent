<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStepToCandidateContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_contacts', function (Blueprint $table) {
            $table->unsignedInteger('step')->after('sequence')->nullable();
        });

        // For Fluency Team
            // $table->dateTime('new_notified_at')->nullable();
            // $table->dateTime('first_contacted_at')->nullable();
            // $table->dateTime('last_contacted_at')->nullable();
            // $table->boolean('do_not_contact')->default(false);
            // $table->boolean('ok_email_candidate')->default(true);
            // $table->boolean('ok_email_chair')->default(true);
            // $table->boolean('ok_email_treasurer')->default(true);
            // $table->unsignedInteger('loyalty_conflict_id')->nullable();
            // $table->text('notes')->nullable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_contacts', function (Blueprint $table) {
            $table->dropColumn('step');
        });
    }
}
