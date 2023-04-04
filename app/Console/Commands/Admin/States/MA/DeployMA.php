<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;


class DeployMA extends NationalMaster
{
    protected $signature                = 'cf:ma_deploy';
    protected $description              = 'What the fuck do you THINK it does?';
    public $state                       = 'MA';

   
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        echo "\n";
        // if (config('app.env') != 'local') dd('Cannot run in live yet.');
        $this->redLineMessage('What table do you want to make Live?');
        $this->info("(You will be asked to confirm.)");

        $new = $this->selectPreviousMaster($or_current = true);
        $old = $this->getMaster($this->state);

        if (!$new || !$old) {
            $this->error('Problem selecting master tables');
            dd('Exiting.');
        }

        $go = $this->confirm("Are you sure you want to rename:\n "
                             .$old." ---> ".$old."_archived_TIMESTAMP\n AND: \n "
                             .$new." ---> ".$old."?\t", false);

        if (!$go) dd('Exiting.');

        $response = $this->activateMaster($new);

        if (!$response) $this->error('Possible error.');

        $this->blueLineMessage('New Master Table Deployed!');
        echo "\n";

        // $populate   = $this->confirm('Run populate slices for '.$this->state.'?');
        // $count      = $this->confirm('Run count on jurisdictions for '.$this->state.'?');

        // if ($populate) {

        // }

        // if ($count)  {

        // }
    }

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }

}
