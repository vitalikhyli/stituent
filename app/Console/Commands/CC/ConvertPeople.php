<?php

namespace App\Console\Commands\CC;

use Illuminate\Console\Command;

class ConvertPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:convert_people';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Puts cms_voters rows created by users into the people table.';

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
        /*
        GET ALL CMS_VOTERS WITH NON STANDARD VOTER CODES
        -----------------------------------------
        select SUBSTR(voter_code, 1, 2) as sub, COUNT(*)
        from cms_voters where
        voter_code NOT LIKE "0%" AND
        voter_code NOT LIKE "10%" AND
        voter_code NOT LIKE "11%" AND
        voter_code NOT LIKE "12%"
        group by sub
        */
    }
}
