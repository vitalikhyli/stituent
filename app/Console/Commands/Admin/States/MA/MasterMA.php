<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

use Carbon\Carbon;


class MasterMA extends NationalMaster
{
    protected $signature                = 'cf:ma_master     {--file_path_voters=}
                                                            {--file_path_districts=}
                                                            {--file_path_district_voter=}
                                                            {--file_path_elections=}
                                                            {--file_path_election_voter=}';

    

    protected $description              = 'Command of commands';
    public $state                       = 'MA';
    public $import_key;

    public $script;
    public $ask_functions       = [];

    public function __construct()
    {
        parent::__construct();

    }
    public function setScript()
    {
        //----------------------------------------------------------------------------
        // THE SCRIPT

        $this->script = [
        'cf:ma_voters'     => [
                'options'   => [
                                'clear_districts', 
                                'clear_district_voter',
                               ],
                'checks'    => [
                                'App\Console\Commands\Admin\States\MA\VotersMA' 
                                    => 'checkDistricts',
                                'App\Console\Commands\Admin\States\MA\VotersMA' 
                                    => 'checkMunicipalities'
                               ],
                'inputs'    => [
                                'file_path_voters'
                                    => ($this->option('file_path_voters')) ?: '{ASK}',
                                'file_path_districts'
                                    => ($this->option('file_path_districts')) ?: '{ASK}',
                                'file_path_district_voter'
                                    => ($this->option('file_path_district_voter')) ?:'{ASK}'
                               ],
                ],

        'cf:ma_elections'  => [
                'options'   => [
                                'clear_elections', 
                                'clear_election_voter'
                               ],
                'checks'    => [],
                'inputs'    => [
                                'file_path_elections'
                                    => ($this->option('file_path_elections')) ?: '{ASK}',
                                'file_path_election_voter'
                                    => ($this->option('file_path_election_voter')) ?: '{ASK}',
                                'table_lookup_cities'   => '{MOST RECENT MASTER}',
                                'table_add_elections'   => '{MOST RECENT MASTER}'
                               ],
               ],
        // 'cf:ma_merge'      => [
        //                     'options'   => [
        //                                    ],
        //                     'checks'    => [],
        //                     'inputs'    => [
        //                                     'table_master_merge_into'   => '{MOST RECENT MASTER}'
        //                                    ],
        //                    ]

        ];
    }

    public function setInputFunctions()
    {

        $this->ask_functions = [

            'file_path_voters'          => ['name' => 'selectFilePath',
                                            'arguments' => [$this->storage_subdir,
                                                            'THE VOTER FILE']
                                            ],

            'file_path_districts'       => ['name' => 'selectFilePath',
                                            'arguments' => [$this->storage_subdir,
                                                            'DISTRICTS']
                                            ],

            'file_path_district_voter'  => ['name' => 'selectFilePath',
                                            'arguments' => [$this->storage_subdir,
                                                            'DISTRICTS-VOTERS PIVOT']
                                            ],


            'file_path_elections'       => ['name' => 'selectFilePath',
                                            'arguments' => [$this->elections_storage_subdir,
                                                            'ELECTIONS']
                                            ],

            'file_path_election_voter'  => ['name' => 'selectFilePath',
                                            'arguments' => [$this->elections_storage_subdir,
                                                            'ELECTIONS-VOTERS PIVOT']
                                            ],
        ];
    }

    public function handle()
    {
        $this->setScript();
        $this->setInputFunctions();

        echo "\n";
        $this->showCommunityFluencyWordMark();

        // if (config('app.env') != 'local') dd('Cannot run in live yet.');

        //----------------------------------------------------------------------------
        // ASK FOR ALL NEEDED INPUTS AND RUN CHECKS
        // ...So all up-front work is done before long processing begins

        foreach ($this->script as $command => $needs) {
            $this->blueLineMessage('Set Up: '.$command);

            foreach ($needs as $type => $args) {
                $this->info('Checking '.strtoupper($type).':');

                switch ($type) {

                    case 'inputs':  // Ask for these BEFORE calling the subcommands
                                    // Then pass them into the sub-commands
                                    
                        foreach($args as $input => $value) {

                            if  ($value == '{ASK}') {

                                $function  = $this->ask_functions[$input]['name'];
                                $arguments = $this->ask_functions[$input]['arguments'];    
                                $response = call_user_func_array(array($this, $function), 
                                                                 $arguments);
                                $this->script[$command][$type][$input] = $response;

                            }

                        }
                        break;

                    // case 'checks':  // Run these BEFORE calling the sub-commands
                    //                 // Then supress them in the sub-commands
                                    
                    //     foreach($args as $model => $function) {
                    //         $model = new $model;
                    //         call_user_func_array(array($model, $function), []);
                    //     }
                    //     break;

                    default:
                        break;
                }

                echo "\n".'   OK'."\n\n";
            }
        }

        //----------------------------------------------------------------------------
        // RUN EACH COMMAND IN THE SCRIPT

        $master_start_time = Carbon::now();

        foreach ($this->script as  $command => $needs) {
            
            $this->redLineMessage('Running: '.$command);
            $arguments = [];

            foreach ($needs as $type => $args) {

                switch ($type) {

                    case 'inputs':
                        foreach($args as $key => $value) {
                            $arguments['--'.$key] = $value;
                        }
                        break;

                    case 'options':
                        foreach($args as $key => $value) {
                            $arguments['--'.$value] = true;
                        }
                        break;

                    case 'checks':
                        foreach($args as $input => $value) {
                            $arguments['--'.$value] = 'done';
                        }
                        break;

                }
            }

            $this->call($command, $arguments);
        }

        //----------------------------------------------------------------------------
        // HOW LONG IT TOOK

        $seconds = Carbon::now()->diffInSeconds($master_start_time);

        if ($seconds >= 86400) {
            $days = floor($seconds/86400);
            $new_seconds = $seconds - $days * 86400;
            $elapsed = $days.'d '.gmdate('H:i:s', $new_seconds);
        } else  {
            $elapsed = gmdate('H:i:s', $seconds);
        }

        $this->blueLineMessage('Whole thing took '.$elapsed.', '.$this->condescendingNickname());

        //----------------------------------------------------------------------------
        // DEPLOY?

        $deploy = $this->confirm('Run DEPLOY Command', true);
        if ($deploy) $this->call('cf:ma_deploy');

    }

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }
}
