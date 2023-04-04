

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('user_id');
            $table->text('name')->nullable();
            $table->string('file')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->text('columns')->nullable();
            $table->text('column_matches')->nullable();
            $table->text('column_map')->nullable();
            $table->boolean('new_if_unmatched')->default(0);
            $table->text('hash')->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->unsignedInteger('imported_count')->default(0);
            $table->unsignedInteger('integrated_count')->default(0);
            $table->unsignedInteger('summary_voters_matched')->default(0);
            $table->unsignedInteger('summary_participants_matched')->default(0);
            $table->unsignedInteger('summary_new_participants')->default(0);
            $table->unsignedInteger('summary_new_emails')->default(0);
            $table->unsignedInteger('summary_new_phones')->default(0);
            $table->unsignedInteger('summary_new_tagged')->default(0);
            $table->timestamps();
        });

        Schema::create('user_uploads_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('upload_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->string('voter_id')->nullable();
            $table->unsignedInteger('participant_id')->nullable();
            $table->text('data')->nullable();
            $table->string('hash')->nullable();
            $table->dateTime('integrated_at')->nullable();
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
        Schema::dropIfExists('user_uploads');
        Schema::dropIfExists('user_uploads_data');
    }
}
