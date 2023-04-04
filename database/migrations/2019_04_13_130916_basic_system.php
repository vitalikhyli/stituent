<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BasicSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /*

        1. Run on DB first:
            ALTER DATABASE
                fluencybase
                CHARACTER SET = utf8mb4
                COLLATE = utf8mb4_unicode_ci

        2. Check that it worked:
            SELECT DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'fluencybase' LIMIT 1;


        */

        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->string('voter_id')->nullable()->index();
            $table->string('household_id')->nullable()->index();
            // $table->boolean('entity')->default(false);
            // == Keys ^^^

            // == Person / Enriched Data::
            $table->string('full_name')->index()->nullable();
            $table->string('full_name_middle')->nullable();
            $table->string('full_address')->index()->nullable();

            $table->string('primary_phone')->nullable();
            $table->text('other_phones')->nullable();

            $table->string('primary_email')->nullable();
            $table->string('work_email')->nullable();
            $table->text('other_emails')->nullable();
            $table->boolean('master_email_list')->default(false);
            $table->boolean('massemail_neversend')->default(false);

            $table->string('social_twitter')->nullable();
            $table->string('social_facebook')->nullable();

            $table->text('support')->nullable();
            $table->text('private')->nullable();
            $table->text('old_private')->nullable();

            // == Essentially (+initially) a copy of Voter File::
            $table->string('name_title')->nullable();           //Make "title_name ????"
            $table->string('first_name')->index()->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->index()->nullable();
            $table->string('suffix_name')->nullable();

            $table->string('address_number')->index()->nullable();
            $table->string('address_fraction')->nullable();
            $table->string('address_street')->index()->nullable();
            $table->string('address_apt')->nullable();
            $table->string('address_city')->index()->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_zip')->index()->nullable();

            $table->decimal('address_lat', 10, 7)->index()->nullable();
            $table->decimal('address_long', 10, 7)->index()->nullable();

            $table->text('mailing_info')->nullable();
            $table->text('business_info')->nullable();

            $table->string('gender')->index()->nullable();
            $table->string('party', 3)->index()->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('dob')->index()->nullable();
            $table->boolean('deceased')->index()->nullable();
            $table->date('deceased_date')->nullable();

            // ======================================================> Political districts
            $table->unsignedInteger('governor_district')->nullable()->index();  //Is this Guv Council?
            $table->unsignedInteger('congress_district')->nullable()->index();
            $table->unsignedInteger('senate_district')->nullable()->index();
            $table->unsignedInteger('house_district')->nullable()->index();

            $table->unsignedInteger('county_code')->nullable()->index();
            $table->string('ward')->nullable()->index();
            $table->string('precinct')->nullable()->index();
            $table->unsignedInteger('city_code')->index()->nullable();

            // == Metadata::
            $table->unsignedInteger('old_cc_id')->index()->nullable();
            $table->string('old_voter_code')->index()->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index()->nullable();

            $table->string('type')->index()->nullable();
            $table->string('name')->index()->nullable();
            $table->text('notes')->nullable();
            $table->string('full_address')->index()->nullable();
            $table->string('household_id')->nullable()->index();

            $table->json('contact_info')->nullable();

            // $table->json('emails')->nullable();
            // $table->json('phones')->nullable();

            $table->string('social_twitter')->nullable();
            $table->string('social_facebook')->nullable();
            $table->longText('private')->nullable();

            $table->string('address_raw')->nullable();
            $table->string('address_number')->index()->nullable();
            $table->string('address_fraction')->nullable();
            $table->string('address_street')->index()->nullable();
            $table->string('address_apt')->nullable();
            $table->string('address_city')->index()->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_zip')->index()->nullable();

            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('entity_person', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index()->nullable();
            $table->unsignedInteger('entity_id')->index();
            $table->unsignedInteger('person_id')->index();
            $table->string('relationship')->nullable();
            // == Metadata::
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('subject_id')->index();
            $table->unsignedInteger('object_id')->index();
            $table->string('subject_type')->nullable();
            $table->string('object_type')->nullable();
            $table->string('kind')->nullable();
            // == Metadata::
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('partnership_types', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();

            $table->string('name');
            // == Metadata::
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('partnerships', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('partnership_type_id')->index()->nullable();

            $table->unsignedInteger('partner_id')->index()->nullable();
            $table->string('partner_name')->nullable();
            $table->string('program')->nullable();
            $table->text('contacts')->nullable();

            $table->text('notes')->nullable();
            $table->text('data')->nullable();

            $table->unsignedInteger('department_id')->nullable(); //entities

            $table->date('year')->nullable();

            // == Metadata::
            $table->softDeletes();
            $table->timestamps();
        });

        // Entity --> Entity        ex: Partners                reversable
        // Person --> entity        ex: Director
        // Person --> Person        ex: Spouses                 reversable
        // PilotItem --> Entity     ex: Beneficiary
        // PilotItem --> Entity     ex: Partner on that item

        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('case_id')->index()->nullable(); //If any
            $table->unsignedInteger('parent_id')->index()->nullable(); //If reply/followup notes
            // == Keys ^^^
            $table->dateTime('date')->nullable();
            // $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('type')->index()->nullable();
            $table->string('source')->nullable();
            $table->text('suggested_people')->nullable();
            $table->text('suggested_entities')->nullable();
            $table->string('category')->nullable(); //Disturbance, etc.
            $table->string('subject')->nullable();
            $table->longText('notes')->nullable();

            $table->boolean('private')->default(false)->index();
            $table->boolean('followup')->default(false)->index();
            $table->date('followup_on')->nullable()->index();
            $table->boolean('followup_done')->default(false)->index();

            // == Metadata::
            $table->unsignedInteger('old_cc_id')->index()->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('contact_person', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();

            $table->unsignedInteger('person_id')->index()->nullable();
            $table->unsignedInteger('contact_id')->index()->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('contact_entity', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();

            $table->unsignedInteger('entity_id')->index()->nullable();
            $table->unsignedInteger('contact_id')->index()->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('priority')->nullable()->index();
            $table->boolean('private')->default(false)->index();
            $table->date('date')->nullable();
            $table->string('type')->nullable()->index();
            $table->string('status')->default('open');
            $table->string('subject')->nullable();
            $table->text('notes')->nullable();
            $table->text('closing_remarks')->nullable();

            // Meta
            $table->unsignedInteger('old_cc_id')->nullable()->index();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('case_person', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('person_id')->index();
            $table->unsignedInteger('case_id')->index();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('case_household', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->string('household_id')->index();
            $table->unsignedInteger('case_id')->index();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('case_file', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();

            $table->unsignedInteger('file_id')->index();
            $table->unsignedInteger('case_id')->index();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('group_file', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();

            $table->unsignedInteger('file_id')->index();
            $table->unsignedInteger('group_id')->index();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('category_id')->index();
            $table->boolean('campaign')->default(0);
            // == Keys ^^^
            $table->string('preset', 10)->nullable();
            $table->unsignedInteger('parent_id')->default(0);
            $table->string('name')->nullable();
            $table->text('notes')->nullable();

            $table->text('additional_info')->nullable();
            $table->dateTime('archived_at')->nullable();
            // == Metadata::
            $table->unsignedInteger('old_cc_id')->nullable()->index();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('parent_id')->index()->nullable();
            $table->unsignedInteger('depth')->default(0)->nullable();
            // == Keys ^^^
            $table->string('preset', 10)->nullable();
            $table->string('name')->nullable();
            // $table->json('data_template')->nullable();
            $table->boolean('can_edit')->default(true);
            $table->boolean('has_position')->default(true);
            $table->boolean('has_title')->default(false);
            $table->boolean('has_notes')->default(true);
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('group_person', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('group_id')->default(0);
            $table->unsignedInteger('person_id')->index();
            $table->string('position')->nullable();
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            // $table->text('data')->nullable(); //notes, etc
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('group_entity', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('group_id')->default(0);
            $table->unsignedInteger('entity_id')->index();
            $table->text('data')->nullable(); //notes, position, etc
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('directory_id')->index();

            $table->string('name');
            $table->string('description')->nullable();
            $table->string('path')->nullable();

            $table->unsignedInteger('old_cc_id')->nullable()->index();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('directories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('parent_id')->nullable()->index();

            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('depth')->default(0)->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('searches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();

            $table->string('name')->nullable();
            $table->text('form')->nullable();
            $table->text('fields')->nullable();

            $table->string('sql')->nullable(); //optional, for future possibilities
            $table->boolean('scope_voters')->default(1);
            $table->boolean('archived')->default(0);
            $table->boolean('bulk_email')->default(0);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        //////////////////////////////////////[  HISTORY  ]//////////////////////////////////////

        Schema::create('historyitems', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('num_people')->nullable();
            $table->unsignedInteger('num_cases_open')->nullable();
            $table->unsignedInteger('num_cases_resolved')->nullable();
            $table->unsignedInteger('num_cases_new')->nullable();
            $table->unsignedInteger('num_contacts_new')->nullable();
            $table->unsignedInteger('num_emails')->nullable();
            $table->unsignedInteger('num_phones')->nullable();
            $table->timestamps();
        });

        Schema::create('campaignhistoryitems', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('current_campaign_id')->index();
            $table->unsignedInteger('support_1')->nullable();
            $table->unsignedInteger('support_2')->nullable();
            $table->unsignedInteger('support_3')->nullable();
            $table->unsignedInteger('support_4')->nullable();
            $table->unsignedInteger('support_5')->nullable();
            $table->unsignedInteger('num_emails')->nullable();
            $table->unsignedInteger('num_phones')->nullable();
            $table->timestamps();
        });

        Schema::create('adminhistoryitems', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('num_accounts')->nullable();
            $table->unsignedInteger('num_users')->nullable();
            $table->timestamps();
        });

        //////////////////////////////////////[  POLITICAL ]//////////////////////////////////////

        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->string('name')->nullable();
            $table->date('election_day')->nullable();
            $table->boolean('current')->default(0);
            $table->timestamps();
        });

        Schema::create('universemembers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id')->index();
            $table->string('member_id')->index();
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person_id')->index();
            $table->unsignedInteger('team_id')->index();
            $table->date('date')->nullable();
            $table->float('amount')->default(0);
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->timestamps();
        });

        //////////////////////////////////////[  BULK MAIL ]//////////////////////////////////////

        // Schema::create('bulk_email_templates', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->unsignedInteger('team_id')->index();
        //     $table->unsignedInteger('user_id')->index();
        //     $table->string('name');
        //     $table->string('description');
        //     $table->text('template');
        //     $table->softDeletes();
        //     $table->timestamps();
        // });

        Schema::create('bulk_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('bulk_email_code_id')->nullable()->index();

            $table->string('name')->nullable();
            $table->unsignedInteger('search_id')->nullable()->index();

            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->text('content_plain')->nullable();
            $table->boolean('refresh_plain')->default(true);

            $table->string('sent_from')->nullable();
            $table->string('sent_from_email')->nullable();
            $table->date('send_date')->nullable();

            $table->boolean('exclude_prior')->default(false);
            $table->text('excluded')->nullable();

            // Tracking the Email
            $table->boolean('queued')->default(0);
            $table->text('no_email_address')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->unsignedInteger('expected_count')->nullable();
            $table->text('emails')->nullable();
            $table->unsignedInteger('sent_count')->nullable();
            $table->dateTime('completed_at')->nullable();

            // Sending results
            $table->text('failed')->nullable();
            $table->text('read')->nullable();

            $table->unsignedInteger('old_cc_id')->index()->nullable();
            $table->string('old_tracker_code')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('bulk_email_code', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->string('name')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
        });

        Schema::create('bulk_email_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bulk_email_id')->index();
            $table->unsignedInteger('team_id')->index();
            $table->string('email')->index();
            $table->unsignedInteger('person_id')->nullable()->index();
            $table->string('voter_id')->nullable()->index();
            $table->unsignedInteger('old_voter_id')->nullable()->index();
            $table->boolean('processing')->default(0);
            $table->datetime('processing_start')->nullable();
            $table->unsignedInteger('attempts')->nullable();
            $table->boolean('sent')->default(0);
            $table->boolean('test')->default(0);
            $table->timestamps();
        });

        Schema::create('people_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->index();

            $table->string('type')->nullable()->index();
            $table->string('name')->nullable();
            $table->text('form')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        ////////////////////////////////[  ??????  ]////////////////////////////////

        Schema::create('city_team', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('city_id')->index();
            $table->timestamps();
        });

        // ======================================> System-wide data
        Schema::create('team_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('description');
            $table->timestamps();
        });

        ////////////////////////////////[  DATA & TEMPLATES  ]////////////////////////////////

        Schema::create('data_workers', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('run')->default(true);
            $table->boolean('interrupted')->default(false);
            $table->text('jobs')->nullable();
            $table->longText('log')->nullable();
            $table->unsignedInteger('ping')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('data_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('data_import_id');
            $table->string('type')->nullable();
            $table->text('arguments')->nullable();
            $table->boolean('done')->default(0);
            $table->unsignedInteger('failed')->nullable();
            $table->unsignedInteger('remaining')->nullable();
            $table->bigInteger('start')->nullable();
            $table->bigInteger('end')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->unsignedInteger('rate')->nullable();
            $table->timestamps();
        });

        Schema::create('data_imports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();   // If copied from another
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('version')->nullable();
            $table->string('type')->nullable();                 // v, hh, e
            $table->string('slug')->nullable();
            $table->boolean('enriched')->default(0);
            $table->boolean('ready')->default(0);
            $table->boolean('deployed')->default(0);
            $table->boolean('archived')->default(0);
            $table->unsignedInteger('slice_of_id')->nullable();
            $table->string('slice_sql')->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->unsignedInteger('count_expected')->nullable();
            $table->unsignedInteger('count_pointer')->nullable();
            $table->string('name')->nullable();
            $table->text('notes')->nullable();
            $table->string('data_folder_id')->nullable();
            $table->boolean('election_include')->default(0);
            $table->string('file_path')->nullable();
            $table->string('file_hash')->nullable();
            $table->boolean('file_stored')->default(0);
            $table->unsignedInteger('file_pointer')->nullable();
            $table->json('header_columns')->nullable();
            $table->json('extra_columns')->nullable();
            $table->string('delimiter', 2)->nullable();
            $table->boolean('skip_first')->default(0);
            $table->timestamps();
        });

        Schema::create('data_folders', function (Blueprint $table) {
            $table->string('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('x__template_voters', function (Blueprint $table) {

            //Optional
            $table->unsignedInteger('import_order')->index()->nullable();
            $table->string('id')->primary();

            // ======================================================> "Enriched Columns"
            $table->string('full_name')->index()->nullable();
            $table->string('full_name_middle')->nullable();
            $table->string('household_id')->nullable()->index();
            $table->string('full_address')->nullable();
            $table->text('elections')->nullable();

            // ======================================================> Name
            $table->string('name_title')->nullable();               //Make "title_name ???"
            $table->string('first_name')->nullable()->index();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable()->index();
            $table->string('suffix_name')->nullable();

            // ======================================================> Address
            //
            $table->string('address_prefix')->nullable();       //Is this used?
            $table->string('address_number')->index()->nullable();
            $table->string('address_fraction')->nullable();
            $table->string('address_street')->nullable()->index();
            $table->string('address_street_type')->nullable();
            $table->string('address_post')->nullable();         //What is this?

            $table->string('address_apt_type')->nullable();     //What is this?
            $table->string('address_apt')->nullable();
            $table->string('address_city')->nullable()->index();
            $table->string('address_state')->nullable();
            $table->string('address_zip')->nullable()->index();

            $table->string('address_zip4')->nullable();

            $table->string('address_lat')->index()->nullable();
            $table->string('address_long')->index()->nullable();

            // ======================================================> Demographic Data
            $table->string('gender', 1)->nullable()->index();
            $table->string('party', 3)->nullable()->index();

            // Party # characters: Changed 1->3
            //See: https://en.wikipedia.org/wiki/Political_parties_and_political_designations_in_Massachusetts
            //And NY has 3-character ones: https://www.elections.ny.gov/NYSBOE/Forms/FOIL_VOTER_LIST_LAYOUT.pdf

            $table->date('dob')->nullable()->index(); // English column name?
            $table->date('registration_date')->nullable()->index();
            $table->string('voter_status', 1)->nullable()->index();

            $table->string('ethnicity')->index()->nullable();
            $table->boolean('head_household')->index()->nullable();      //What is this?

            // ======================================================> Political districts
            $table->string('state', 2)->nullable();     // Is address_state enough?
            $table->unsignedInteger('governor_district')->nullable()->index();  //Is this Guv Council?
            $table->unsignedInteger('congress_district')->nullable()->index();
            $table->unsignedInteger('senate_district')->nullable()->index();
            $table->unsignedInteger('house_district')->nullable()->index();

            $table->unsignedInteger('county_code')->nullable()->index();
            $table->unsignedInteger('city_code')->nullable()->index();
            $table->string('ward')->nullable()->index();
            $table->string('precinct')->nullable()->index();

            // ======================================================> Personal
            $table->string('spouse_name')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('home_phone')->nullable();
            $table->boolean('deceased')->default(false)->index();
            $table->date('deceased_date')->nullable();

            // ======================================================> Mailing Address
            $table->text('mailing_info')->nullable();       //Leaving as one column?
            // maddress_1
            // maddress_2
            // mcity
            // mstate
            // mzip
            // mzip4

            // ======================================================> Other Emails
            $table->text('emails')->nullable();     // Don't understand how these come from voterfile? Phones?
            // email
            // email2
            // email3

            // ======================================================> Business Info
            $table->text('business_info')->nullable();
            // mcrc16
            // occupation
            // work_phone
            // work_phone_ext
            // fax
            // bname
            // baddress_1
            // baddress_2
            // bcity
            // bstate
            // bzip
            // bzip4
            // bfax
            // bweb

            // ======================================================> Alternate Districts
            $table->text('alternate_districts')->nullable();
            // alt_senate_district
            // alt_house_district
            // alt_congress_district
            // alt_gov_district
            // custom_district

            // ======================================================> Admin
            $table->dateTime('archived_at')->nullable();
            $table->string('origin_method')->nullable(); // statewide, city, user, etc
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes(); // archived
            $table->timestamps();
        });

        Schema::create('counties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('state');
            $table->string('code');
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('municipalities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('state');
            $table->string('code');
            $table->string('name');
            $table->unsignedInteger('county_id');
            $table->timestamps();
        });

        //  TO DISCUSS? All tables called x_0000_0000    or  x_0000_0000_hh
        //  If deployed, simply add this: x_0000_0000_$  or  x_0000_0000_hh_$
        //  This way they are all easy to find
        //  i.e., dispense with "x_voters_" etc.

        Schema::create('x__template_households', function (Blueprint $table) {
            $table->string('id')->primary(); //->index()
            $table->string('household')->nullable(); //Full address string
            $table->json('residents')->nullable(); //Array of VoterIDs
            $table->integer('residents_count')->default(0);

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // $table->json('removed_residents')->nullable();
            // $table->json('added_residents')->nullable(); //Array of person_ids

            $table->timestamps();
        });

        /*
        if (!DB::connection('voters')->getSchemaBuilder()->hasTable('x_voters_MA_master')) {
            DB::statement('CREATE TABLE x_voters_MA_master LIKE x__template_voters');
            $to_db = env('DB_VOTER_DATABASE');
            DB::statement('RENAME TABLE x_voters_MA_master TO '.$to_db.'.x_voters_MA_master');
        }
        if (!DB::connection('voters')->getSchemaBuilder()->hasTable('x_households_MA_master')) {
            DB::statement('CREATE TABLE x_households_MA_master LIKE x__template_households');
            $to_db = env('DB_VOTER_DATABASE');
            DB::statement('RENAME TABLE x_households_MA_master TO '.$to_db.'.x_households_MA_master');
        }
        */

        if (! DB::connection('voters')->getSchemaBuilder()->hasTable('voter_slices')) {
            Schema::create('voter_slices', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('sql');
                $table->unsignedInteger('voters_count')->default(0);
                $table->unsignedInteger('hh_count')->default(0);
                $table->timestamps();
            });
        }

        // DB::statement('CREATE TABLE x_voters_0001 LIKE x__template_voters');
        // DB::statement('CREATE TABLE x_households_0001 LIKE x__template_households');

        //////////////////////////////[  USERS, TEAMS, ACCOUNTS  ]//////////////////////////////

        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('old_cc_id')->nullable()->index();
            $table->boolean('active')->default(1);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();

            $table->unsignedInteger('billygoat_id')->nullable()->index();
            $table->text('billygoat_data')->nullable();
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->after('name')->index()->nullable();
            $table->text('memory')->nullable();
            $table->string('language', 2)->default('en');
            $table->dateTime('last_login')->nullable();
            $table->string('title')->nullable();
            $table->json('breadcrumb')->nullable();
            $table->dateTime('accepted_terms')->nullable();
            $table->boolean('active')->default(true);
            $table->string('login_token')->nullable();
            $table->boolean('change_password')->default(false);
            $table->string('password')->nullable()->change();
        });

        //////////////////////////////[  PILOT  ]//////////////////////////////

        // Laz simplified PILOT version
        // Now Community Benefits with a PILOT boolean

        Schema::create('community_benefits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('fiscal_year');

            $table->string('program_name')->nullable();
            $table->text('program_description')->nullable();
            $table->integer('value')->nullable();
            $table->string('value_type')->nullable(); //Cash, In-Kind, etc
            $table->string('time_frame')->nullable(); // Ongoing, 1-time

            $table->boolean('pilot')->index()->nullable();

            $table->string('beneficiaries')->nullable();
            $table->string('initiators')->nullable();
            $table->string('partners')->nullable();
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        /*
        Schema::create('pilot_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('program_id')->nullable();
            $table->string('item')->nullable();
            $table->integer('value')->nullable();
            $table->string('value_type',7)->nullable(); //Cash, In-Kind, etc
            $table->date('date')->nullable();
            $table->longText('notes')->nullable();
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('pilot_programs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('pilot_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id');
            $table->date('date')->nullable();
            $table->string('name')->nullable();
            $table->string('headline')->nullable();
            $table->string('description')->nullable();
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Schema::create('pilot_beneficiaries', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->unsignedInteger('team_id');
        //     $table->string('name')->nullable();
        //     $table->string('notes')->nullable();
        //     // == Metadata::
        //     $table->unsignedInteger('created_by')->nullable();
        //     $table->unsignedInteger('updated_by')->nullable();
        //     $table->unsignedInteger('deleted_by')->nullable();
        //     $table->softDeletes();
        //     $table->timestamps();
        // });

        Schema::create('pilot_beneficiary_item', function (Blueprint $table) {
            $table->increments('id');
            // $table->unsignedInteger('beneficiary_id');
            $table->unsignedInteger('entity_id');
            $table->unsignedInteger('item_id');
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('pilot_item_partner', function (Blueprint $table) {
            $table->increments('id');
            // $table->unsignedInteger('beneficiary_id');
            $table->unsignedInteger('entity_id');
            $table->unsignedInteger('item_id');
            // == Metadata::
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('followups');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_person');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('issue_person');
        Schema::dropIfExists('files');
        Schema::dropIfExists('case_person');
        Schema::dropIfExists('case_file');

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('shortname');
            $table->dropColumn('logo_img');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('memory');
        });

        Schema::dropIfExists('team_1_voters');
        Schema::dropIfExists('team_1_households');
    }
}
