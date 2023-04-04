<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\VoterMaster;
use Carbon\Carbon;

class ElectionFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:election_from_file {--file=} {--live}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds all tabs and processes elections from each into voter records.';

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
        if (!$this->option('file')) {
            dd("Command needs FILE: php artisan cf:election_from_file --file=quincy-elections-2018.xlsx");
        }

        if (!file_exists(storage_path('app/elections'))) {
            mkdir(storage_path('app/elections'));
        }

        $extension = pathinfo($this->option('file'), PATHINFO_EXTENSION);

        $delimiter = ',';
        if ($extension == 'xlsx') {
            $num_sheets_to_check = 10;
        } else {
            $num_sheets_to_check = 1;
            $delimiter = '|';
        }

        $file_path = storage_path('app/elections/'.$this->option('file'));
        

        for ($i=1; $i<$num_sheets_to_check; $i++) {
            $rows = [];

            if ($extension == 'xlsx') {
                $rows = (new FastExcel)->sheet($i)->import($file_path);
                echo "\n\Sheet $i Records: ".$rows->count()."\n";
            } else {
                $file = fopen($file_path, 'r');
                // Read the first row (headers)
                $headers = fgetcsv($file, 0, $delimiter);
                //dd($headers);
                // Read each subsequent row
                while ($row = fgetcsv($file, 0, $delimiter)) {
                    // Create an array using the headers as keys and the row values as values
                    $rows[] = array_combine($headers, $row);
                }
               
            }

            
            // $voter_ids = [];
            // foreach ($rows as $row) {
            //     $id     = 'MA_'.$row['Voter ID Number '];
            //     $voter_ids[] = $id;
            // }
            // $voters = VoterMaster::whereIn('id', $voter_ids)->get()->keyBy('id');
            // dd($voters);
            $currcount = 0;
            foreach ($rows as $row) {
                try {
                    $id     = trim($row['Voter ID Number ']);
                    $date   = $row['Election Date '];
                    if (!is_object($date)) {
                        $date = Carbon::parse($date);
                    }
                    $type   = trim($row['Type of Election ']);

                    $code = '0000';
                    if (isset($row['City/ Town Code '])) {
                        $code   = str_pad(trim($row['City/ Town Code ']), 4, '0', STR_PAD_LEFT);
                    }
                    if (isset($row['City/ Town Code Assigned Number'])) {
                        $code   = str_pad(trim($row['City/ Town Code Assigned Number']), 4, '0', STR_PAD_LEFT);
                    }

                    

                    $reg_party = '0';
                    $voted_party = '0';
                    if (isset($row['Party Affiliation '])) {
                        $reg_party = trim($row['Party Affiliation ']);
                    }
                    if (isset($row['Party Voted '])) {
                        $voted_party = trim($row['Party Voted ']);
                    }

                    if (!$reg_party) {
                        if (isset($row['Record Seq. #'])) {
                            if (!is_numeric($row['Record Seq. #'])) {
                                $reg_party = trim($row['Record Seq. #']);
                            }
                        }
                    }
                    $voted_info = $code.'-'.$reg_party."-".$voted_party;

                    $voter = VoterMaster::find('MA_'.$id);
                    if (!$voter) {
                        echo "\t\tSkipped MA_$id\r";
                        continue;
                    }
                    $abbr = $this->getElectionTypeAbbreviation($type);

                    $str = 'MA-'.$date->format('Y-m-d').'-'.$abbr;


                    if ($abbr == 'L0000' || $abbr == 'LTM00') {
                        $str .= '-'.$code;
                    } else {
                        $str .= '-0000';
                    }

                    //echo "\t\t$str => $voted_info\n";

                    $elections = $voter->elections;
                    if (!is_array($elections)) {
                        $elections = [];
                    }
                    $elections[$str] = $voted_info;

                    // if (count($voter->elections) > 0) {
                    //     $voter->elections = $elections;
                    //     dd($voter);
                    // }

                    $voter->elections = $elections;

                    if (!$this->option('live')) {
                        dd($voter);
                    }

                    $voter->save();
                    
                    echo "\t\tCompleted: ".($currcount++)."/".count($rows)."\r";

                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
            }
        }

        return Command::SUCCESS;
    }

    public function getElectionTypeAbbreviation($long)
    {
        switch(strtoupper($long)) {
            case 'MUNICIPAL':
                return 'L0000';
            case 'LOCAL ELECTION':
                return 'L0000';
            case 'TOWN MEETING':
                return 'LTM00';
            case 'GENERAL':
                return 'STATE';
            case 'STATE ELECTION':
                return 'STATE';
            case 'PRESIDENTIAL PRIMARY':
                return 'PP000';
            case 'PRIMARY':
                return 'SP000';
            case 'STATE PRIMARY':
                return 'SP000';
            case 'SPECIAL':
                return 'SS000';
            case 'LEGISLATIVE SPECIAL':
                return 'LS000';
        }
        return '00000';
    }
}

/*
    "MA-2006-11-07-STATE-0000":"0274-U-0"


    ROW
    ------------------------------------------------
    "Party Affiliation " => "D "
    "Voter ID Number " => "07NLA2289000"
    "Last Name " => "NOEL"
    "First Name " => "LAURA"
    "Middle Name " => "TERESA"
    "Residential Address - Street Number" => 625
    "Residential Address - Street Suffix " => ""
    "Residential Address - Street Name " => "THOMAS BURGIN PKY"
    "Residential Address - Apartment Number " => 445
    "Residential Address - Zip Code " => 2169
    "Type of Election " => "STATE ELECTION"
    "Election Date " => DateTime @1541480400 {#2286
    date: 2018-11-06 00:00:00.0 America/New_York (-05:00)
    }
    "City/ Town Name " => "QUINCY"
    "City/ Town Indicator " => "C"
    "City/ Town Code Assigned Number" => 243
    "Voter Title " => ""
    "Ward Number " => 4
    "Precinct Number " => 5
    "Voter Status r" => "A"
    "Mailing Address - Street Number/Name " => ""
    "Mailing Address - Apartment Number " => ""
    "Mailing Address - City/Town " => ""
    "Mailing Address - State " => ""
    "Mailing Address - Zip Code" => ""
    ]
*/
