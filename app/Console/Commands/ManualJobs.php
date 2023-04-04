<?php

namespace App\Console\Commands;

use App\Models\Admin\DataImport;
use App\Models\Admin\DataJob;
use Illuminate\Console\Command;

class ManualJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:onetime';

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
        $import = DataImport::find(1);
        (new DataJob)->add('enrich', $import->id);
        (new DataJob)->add('createHouseholds', $import->id);
        (new DataJob)->add('ready', $import->id);
        (new DataJob)->add('deploy', $import->id);
        (new DataJob)->add('deployHouseholds', $import->id);
    }
}
