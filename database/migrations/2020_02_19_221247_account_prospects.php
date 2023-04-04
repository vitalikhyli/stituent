<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AccountProspects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('account_prospects');

        Schema::create('account_prospects', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Raw
            $table->string('candidate_name')->nullable();
            $table->string('candidate_address')->nullable();
            $table->string('committee_name')->nullable();
            $table->string('committee_address')->nullable();
            $table->string('treasurer_name')->nullable();
            $table->unsignedInteger('ocpf_id')->nullable();
            $table->string('party')->nullable();
            $table->string('office_district_sought')->nullable();
            $table->string('office_district_held')->nullable();
            $table->date('ocpf_organized_on')->nullable();
            $table->date('ocpf_closed_on')->nullable();
            $table->string('pdf_link')->nullable();

            // Computed:
            $table->string('voter_id')->nullable();
            $table->string('office_type')->nullable();
            $table->string('district_name')->nullable();
            $table->string('district_type')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->unsignedInteger('city_code')->nullable();
            $table->unsignedInteger('age')->nullable();

            // Fluency:
            $table->string('source')->nullable(); // Added Manually, Web, etc
            $table->date('letter_sent_on')->nullable();
            $table->date('fb_start_on')->nullable();
            $table->date('fb_stopped_on')->nullable();
            $table->date('phone_call_on')->nullable();
            $table->text('notes')->nullable();
            $table->date('election_on')->nullable();
            $table->string('campaign_status')->nullable();
            $table->boolean('won')->nullable();

            // Eventually became an account?
            $table->unsignedInteger('account_id')->nullable();

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
        Schema::dropIfExists('account_prospects');
    }
}
