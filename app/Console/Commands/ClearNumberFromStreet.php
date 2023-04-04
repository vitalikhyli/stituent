<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearNumberFromStreet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:clear_number_from_street {--slice=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'removes leading numbers from street name, by city or slice';

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
        if ($this->option('slice')) {
        }
    }
}
