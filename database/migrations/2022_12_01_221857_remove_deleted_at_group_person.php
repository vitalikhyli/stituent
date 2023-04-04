<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\GroupPerson;

class RemoveDeletedAtGroupPerson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo 'Before: '.GroupPerson::count()."\n";
        //dd();
        GroupPerson::whereNotNull('deleted_at')->delete();
        Schema::table('group_person', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        echo 'After: '.GroupPerson::count()."\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dateTime('deleted_at')->nullable();
    }
}
