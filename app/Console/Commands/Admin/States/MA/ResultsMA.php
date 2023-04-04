<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

Use App\Models\ImportedMAResult;
use App\Municipality;

use DB;
use Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

class ResultsMA extends NationalMaster
{
    protected $signature = 'cf:ma_results           {--one_precinct}';
    protected $description = 'Command description';

    public $state                       = 'MA';
    public $results_table               = 'i_ma_results_import';
    public $results_date                = '2018-11-06';
    public $results_election            = 'Governor';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // https://electionstats.state.ma.us/elections/view/131501/

        if ($this->option('one_precinct')) {

            $results = DB::connection('voters')
                          ->table($this->results_table)
                          ->selectRaw('city, count(*) as precincts')
                          ->groupBy('city')
                          ->having('precincts', 1)
                          ->get();

            $list = [];
            foreach($results as $town) {
                // echo $town->precincts."\t".$town->city."\n";
                $list[] = $this->correctCityName($town->city);
            }

            dd(implode(',', $list));
        }

        $this->truncateIfExists($this->results_table);
        $file_path = $this->selectFilePath($this->storage_subdir, $what_for = 'RESULTS');
        $this->uploadResultsFile($file_path);

        echo "\n";
    }

    public function forEachRow($switch, $row, $row_num)
    {
        switch ($switch) {
            case 'results':
                return $this->importResultsRow($row, $row_num);
                break;
        }
    }

    public function importResultsRow($row, $rownum)
    {
        $csv = $this->englishColumnNames($row, $this->firstrow);

        if ($csv['City/Town'] == 'TOTALS') return;

        // 0 => "City/Town"
        // 1 => "Ward"
        // 2 => "Pct"
        // 3 => "Baker and Polito"
        // 4 => "Gonzalez and Palfrey"
        // 5 => "All Others"
        // 6 => "Blanks"
        // 7 => "Total Votes Cast"

        $result = new ImportedMAResult;

        $result->city            = $csv['City/Town'];
        $result->ward            = $csv['Ward'];
        $result->precinct        = $csv['Pct'];
        $result->gop             = str_replace(',', '', $csv['Baker and Polito']);
        $result->dem             = str_replace(',', '', $csv['Gonzalez and Palfrey']);
        $result->other           = str_replace(',', '', $csv['All Others']);
        $result->blank           = str_replace(',', '', $csv['Blanks']);
        $result->total           = str_replace(',', '', $csv['Total Votes Cast']);

        $result->original_import = $csv;
        $result->date            = $this->results_date;
        $result->election        = $this->results_election;

        $cf = Municipality::where('name', $this->correctCityName($result->city))
                          ->where('state', $this->state)
                          ->first();
        $result->cf_code         = ($cf) ? $cf->code : null;

        $result->save();

        return $result;  
    }

    public function correctCityName($city)
    {
        $city = str_replace('N.', 'North', $city);
        $city = str_replace('E.', 'East', $city);
        $city = str_replace('W.', 'West', $city);
        $city = str_replace('S.', 'South', $city);

        return $city;
    }

    public function uploadResultsFile($file_path)
    {
        echo "\n";
        $this->info('Uploading Results...');

        Schema::connection('voters')->dropIfExists($this->results_table);
        Schema::connection('voters')->create($this->results_table, function (Blueprint $table) {

            $table->increments('id');
            $table->string('cf_code')->nullable();

            $table->string('election')->nullable();
            $table->date('date')->nullable();

            $table->string('city')->nullable();
            $table->string('ward')->nullable();
            $table->string('precinct')->nullable();

            $table->unsignedInteger('gop')->nullable();
            $table->unsignedInteger('dem')->nullable();
            $table->unsignedInteger('other')->nullable();
            $table->unsignedInteger('blank')->nullable();
            $table->unsignedInteger('total')->nullable();

            $table->text('original_import')->nullable();

            $table->timestamps();
        });

        $this->expected_num_rows  = $this->expectedNumRows($file_path);
        $this->delimiter            = $this->detectDelimiter($file_path);
        $this->firstrow             = $this->getFirstRow($file_path);
        $this->start_time           = Carbon::now();

        $log                        = $this->createErrorLog($name = $this->results_table);

        $this->openHandleAndGoThrough($file_path,
                                      $switch = 'results',
                                      $log);

    }

}
