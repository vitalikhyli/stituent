<?php

namespace App\Console\Commands\Admin\States\RI;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\VoterMaster;
use App\Municipality;

use App\Traits\Admin\FileProcessingTrait;


class ElectionsRI extends Command
{

    use FileProcessingTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:ri_elections {--file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add elections to Master Voter File';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     *
     * @return int
     */


    ////////////////////////////////////////////////////////////////////////
    //
    // BASIC VARIABLES
    //

    public $state;
    public $master_table;
    public $storage_subdir;

    public function __construct()
    {
        parent::__construct();

        $this->state            = 'RI';
        $this->master_table     = 'x_voters_'.$this->state.'_master';
        $this->elections_storage_subdir   = '/uploads-election-history';
        
    }

    // public function __destruct() {
    //     session()->forget('table_while_importing_master');
    // }

    public function handle()
    {
        session(['table_while_importing_master' => $this->master_table]);

        if ($this->option('file_path')) {
           $file_path = $this->option('file_path');

        } else {

            $file_path = $this->selectFilePath($this->elections_storage_subdir);
        }
        ////////////////////////////////////////////////////////////////////
        //
        // INTRO
        //

        $this->bannerMessage(["Import Election Data for ".$this->state]);

        $started = Carbon::now();
        $this->info('Starting process at '.$started->toDateTimeString());

        ////////////////////////////////////////////////////////////////////
        //
        // GET file CSV
        //

        $file = new \SplFileObject($file_path, 'r');

        $expected_num_rows = $this->expectedNumRows($file_path);
        $this->info('Expected number rows including header: '.$expected_num_rows);


        $delimiter = $this->detectDelimiter($file);

        if (!$delimiter) {
            dd('Error - could not figure out delimiter');
        } else {
            $this->info('Delimiter detected: --> '.$delimiter.' <--');
        }

        $firstrow = $file->fgetcsv($delimiter);
        $firstrow = collect($firstrow)->map(function ($item) {
                                                return trim($item);
                                            })
                                      ->toArray();

        if (!$firstrow) {
            dd('Error - could not get first row');
        } else {
            $this->info('Found '.count($firstrow).' column headers');
        }

        ////////////////////////////////////////////////////////////////////
        //
        // Create Error Log in Case Needed
        //

        $log_name = 'elections-'.$this->state.'-'.time().'.log';
        $log = new Logger($log_name);
        $log->pushHandler(new StreamHandler(storage_path().'/logs/master-errors/'.$log_name));
        $this->info('New Log Ready: /logs/master-errors/'.$log_name);


        ////////////////////////////////////////////////////////////////////
        //
        // Process Each Line
        //

        $this->info('Processing...');

        $handle = fopen($file_path, "r");

        $row_num = 0;
        $error_count = 0;

        while (($raw_string = fgets($handle)) !== false) {

            $row_num++;

            if ($row_num == 1) continue;

            $row = str_getcsv($raw_string);

            // try {

                $this->importRow($row, $row_num, $firstrow);

            // } catch (\Exception $e) {

            //     $log->error($e->getMessage());
            //     $error_count++;

            // }
            

        }

        fclose($handle);

        $total_rows = $row_num - 1;

        ////////////////////////////////////////////////////////////////////
        //
        // DONE
        //

        $finished = Carbon::now();

        $this->bannerMessage([
                                "Done.",
                                "Finished:\t".trim(str_replace('after', '', $finished->diffForHumans($started, ['parts' => 4])))
                             ]);
    }


    public function importRow($row, $row_num, $firstrow)
    {
        echo 'Row number: '.number_format($row_num)."\r";

        ////// Map English Column Names onto Row instead of Key #s

        $csv = [];
        foreach($row as $key => $value) {
            $csv[$firstrow[$key]] = $value;
        }

        
        $id = $this->state.'_'.$csv['VOTER ID'];

        ////// Get number of elections from columns

        $elections = collect($csv)->keys()->filter(function ($item) {
            if (substr($item, 0, 4) == 'DATE') return true;
        })->map(function ($item) {
            return substr($item, 5);
        });
        
        ////// Put elections into one array

        $history = [];

        foreach($elections as $num) {

            if  (!$csv['DATE '.$num]) continue;

            $city_code =  substr($csv['PRECINCT '.$num], 0, 2) ;
            if (substr($city_code, 0, 1) == 0)  {
                $city_code = substr($city_code, 1);
            }

            $history[] = [
                            'date'      => $csv['DATE '.$num],
                            'type1'     => $csv['ELECTION '.$num],
                            'type2'     => $csv['TYPE '.$num],          // R or M
                            'precinct'  => $csv['PRECINCT '.$num],
                            'city'      => $city_code,
                            'party'     => $csv['PARTY '.$num]
                         ];
        }

        //////// Process Election String

        foreach ($history as $key => $election) {
            // "MA-1999-03-08-L0000-0191":"0191-U-0"
            // "MA-2000-11-07-STATE-0000":"0191-U-0"
            // "MA-2004-11-02-STATE-0000":"0061-U-0"

            $date = Carbon::parse($election['date'])->toDateString();
            $party = ($election['party']) ? $election['party'] : 0;

            $municipality = Municipality::where('code', $election['city'])
                                        ->where('state', $this->state)
                                        ->first();
            $municipality_code = ($municipality) ? $municipality->code : null;
            $city = str_pad($municipality_code, 4, '0', STR_PAD_LEFT);

            $string = $this->state.'-'
                      .$date.'-'
                      .$this->lookupElectionType($election['type1'], $election['type2']).'-'
                      .'0000';

            $string2 = $city.'-'
                      .$party.'-'
                      .'0';

            $history[$key]['string'] = $string;
            $history[$key]['string2'] = $string2;
        }

        // dd(collect($history)->pluck('type1')->unique());

        // dd($history);

        //////// Add to Voter

        $voter = VoterMaster::find($id);

        if ($voter) {

            $current = $voter->elections;

            foreach($history as $election) {
                $current[$election['string']] = $election['string2'];
            }

            $voter->elections = $current;

            $voter->save();

        }

        //--------------------------------------
        // 0 => "VOTER ID"
        //--------------------------------------
        // 1 => "LAST NAME"
        // 2 => "FIRST NAME"
        // 3 => "MIDDLE NAME"
        // 4 => "SUFFIX"
        //--------------------------------------
        // 5 => "DATE 1"
        // 6 => "ELECTION 1"
        // 7 => "TYPE 1"
        // 8 => "PRECINCT 1"
        // 9 => "PARTY 1"
        // 10 => "DATE 2"
        // 11 => "ELECTION 2"
        // 12 => "TYPE 2"
        // 13 => "PRECINCT 2"
        // 14 => "PARTY 2"
        // 15 => "DATE 3"
        // 16 => "ELECTION 3"
        // 17 => "TYPE 3"
        // 18 => "PRECINCT 3"
        // 19 => "PARTY 3"
        // 20 => "DATE 4"
        // 21 => "ELECTION 4"
        // 22 => "TYPE 4"
        // 23 => "PRECINCT 4"
        // 24 => "PARTY 4"
        // 25 => "DATE 5"
        // 26 => "ELECTION 5"
        // 27 => "TYPE 5"
        // 28 => "PRECINCT 5"
        // 29 => "PARTY 5"
        // 30 => "DATE 6"
        // 31 => "ELECTION 6"
        // 32 => "TYPE 6"
        // 33 => "PRECINCT 6"
        // 34 => "PARTY 6"
        // 35 => "DATE 7"
        // 36 => "ELECTION 7"
        // 37 => "TYPE 7"
        // 38 => "PRECINCT 7"
        // 39 => "PARTY 7"
        // 40 => "DATE 8"
        // 41 => "ELECTION 8"
        // 42 => "TYPE 8"
        // 43 => "PRECINCT 8"
        // 44 => "PARTY 8"
        //--------------------------------------
        // 45 => "CURRENT PARTY"
        // 46 => "YEAR OF BIRTH"
        // 47 => "STREET NUMBER"
        // 48 => "SUFFIX A"
        // 49 => "SUFFIX B"
        // 50 => "STREET NAME"
        // 51 => "STREET NAME 2"
        // 52 => "UNIT"
        // 53 => "CITY"
        // 54 => "POSTAL CITY"
        // 55 => "STATE"
        // 56 => "ZIP CODE"
        // 57 => "ZIP CODE 4"
        // 58 => "PRECINCT"
        // 59 => "STATUS"
        //--------------------------------------

    }

    public function lookupElectionType($type1, $type2)
    {
        $lookup = [

            //----------------------------- From CSV Data File
            // PRESIDENTIAL ELECTION
            // PRESIDENTIAL PREFERENCE PRIMARY
            // STATEWIDE GENERAL ELECTION
            // STATEWIDE PRIMARY

            'STATEWIDE GENERAL ELECTION'        => 'STATE',
            'PRESIDENTIAL PREFERENCE PRIMARY'   => 'PP',
            'PRESIDENTIAL ELECTION'             => 'STATE',
            'STATEWIDE PRIMARY'                 => 'SP',

            //----------------------------- Massachusetts AddElectionsFromStateHistory Command
            // 'Local Election' => 'L',
            // 'Local Primary' => 'LP',
            // 'Special State' => 'SS',
            // 'Primary Election' => 'PE',
            // 'General Election' => 'G',
            // 'Local Special' => 'LS',
            // 'Special State Primary' => 'SSP',
            // 'Local Town Meeting' => 'LTM',
            // 'Local Rep Town Mtg' => 'LRTM',
        ];

        if (isset($lookup[$type1])) {
            $string = $lookup[$type1];
            $string = str_pad($string, 5, '0', STR_PAD_RIGHT);

            return $string;
        }

        return '00000';
    }

}
