<?php

namespace App\Console\Commands\Admin\UploadToMaster;

use App\Import;
use App\VoterImport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VerifyMunicipality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:verify_municipality {--import_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $import_id = $this->option('import_id');
        if (! $import_id) {
            echo "Needs import_id like cf:verify_municipality --import_id=\n";

            return;
        }
        $import = Import::find($import_id);

        session(['import_table' => $import->table_name]);
        $voters_query = VoterImport::query();
        $count = 0;
        $duds = 0;
        $voters_query->chunk(1000, function ($voters) use (&$duds, &$count, $import) {

            foreach ($voters as $voter) {
                if ($count % 17 == 0) {
                    $import->verified_count = $count;
                    $import->save();
                }
                if (! $this->verifyOne($voter)) {
                    $duds++;
                }
                $count++;
            }

        });

        echo "Duds: ".$duds."\n";
        
        $import->verified_count = $count;
        $import->verified_at = Carbon::now();
        $import->save();
    }

    public function verifyOne($voter)
    {
        if (strlen($voter->id) != 15) {
            return false;
        }

        return true;
    }
}
