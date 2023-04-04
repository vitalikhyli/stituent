<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddBirthdayMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:birthday_migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the birthday field to every voter table and people';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $state = 'MA';

        if (! Schema::connection('voters')->hasColumn('x_voters_MA_master', 'birthday')) {
            echo date('Y-m-d h:i:s')." adding x_voters_MA_master birthday\n";
            Schema::connection('voters')->table('x_voters_MA_master', function (Blueprint $table) {
                $table->string('birthday', 5)->after('yob')->nullable();
            });
        }

        echo date('Y-m-d h:i:s')." adding people birthday\n";
        if (! Schema::hasColumn('people', 'birthday')) {
            Schema::table('people', function (Blueprint $table) {
                $table->string('birthday', 5)->after('yob')->index()->nullable();
            });
        }

        //dd("people done");




        $table_query = DB::select("SHOW TABLES LIKE 'x_".$state."_%'");
        foreach ($table_query as $tableresults) {
            foreach ($tableresults as $key => $tablename) {
                if (Str::contains($tablename, '_hh')) {
                    continue;
                }
                if (! Schema::hasColumn($tablename, 'yob')) {
                    echo date('Y-m-d h:i:s')." adding ".$tablename." yob\n";
                    Schema::table($tablename, function (Blueprint $table) {
                        $table->string('yob', 4)->after('dob')->nullable();
                    });
                }
                if (! Schema::hasColumn($tablename, 'birthday')) {
                    echo date('Y-m-d h:i:s')." adding ".$tablename." birthday\n";
                    Schema::table($tablename, function (Blueprint $table) {
                        $table->string('birthday', 5)->after('yob')->nullable();
                    });
                } else {
                    echo date('Y-m-d h:i:s')." SKIPPING ".$tablename." birthday\n";
                }
            }
        }
    }

}
