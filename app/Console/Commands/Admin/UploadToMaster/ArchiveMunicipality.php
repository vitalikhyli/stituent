<?php

namespace App\Console\Commands\Admin\UploadToMaster;

use Illuminate\Console\Command;
use App\VoterMaster;
use App\Import;
use Carbon\Carbon;

class ArchiveMunicipality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:archive_municipality {--import_id=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'After complete import and insert, archives anything not in new file.';

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
     * @return int
     */
    public function handle()
    {
        $import_id = $this->option('import_id');
        if (! $import_id) {
            echo "Needs import_id like cf:insert_municipality --import_id=\n";

            return;
        }
        $date = $this->option('date');
        if (! $date) {
            echo "Needs date like cf:archive_municipality --import_id=49 date=yesterday\n";

            return;
        }
        $import = Import::find($import_id);
        $date = date('Y-m-d', strtotime($date));
        $to_archive = VoterMaster::where('city_code', $import->municipality_id)
                                 ->where('updated_at', '<', $date)
                                 ->whereNull('archived_at');
        if ($this->confirm('Are you sure you want to archive '.$to_archive->count().' voters, using date '.$date.'?')) {

            $affected = $to_archive->update(['archived_at' => Carbon::now()]);
            $this->info("$affected rows updated.");
        }


    }
}
