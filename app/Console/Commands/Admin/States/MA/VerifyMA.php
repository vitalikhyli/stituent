<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

use App\Municipality;
use App\County;
use App\District;

use App\VoterMaster;
use App\Models\ImportedVoterMaster;

use DB;
use Carbon\Carbon;


class VerifyMA extends NationalMaster
{

    protected $signature    = 'cf:ma_verify         {--latest}
                                                    {--no_archived_at}';
    protected $description  = 'Command description';

    public $state           = 'MA';
    // public $sample_size;
    public $tolerance;
    public $new_master;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->option('latest')) {
            $this->new_master = $this->getMostRecentMaster($this->state);
        } else {
            $this->new_master = $this->selectPreviousMaster();
        }

        session(['table_while_importing_master' => $this->new_master]);
        session(['team_state' => $this->state]);

        $which = $this->choice('Which one, '.$this->condescendingNickname(), 
                                                 ['city' => 'City',
                                                  'county' => 'County',
                                                  'house' => 'House',
                                                  'senate' => 'Senate',
                                                  'congress' => 'Congress',
                                                  'nulls'   => 'Nulls for whole DB']);

        if ($which == 'nulls') {

            $this->showAllNulls();

        } else {

            // $this->sample_size    = $this->ask('Sample size?', 1000);
            $this->tolerance      = $this->ask('Tolerance % ?', 12);

            $this->start_time           = Carbon::now();

            if ($which == 'county')     $this->runSample('county_code');
            if ($which == 'city')       $this->runSample('city_code');
            if ($which == 'house')      $this->runSample('house_district');
            if ($which == 'senate')     $this->runSample('senate_district');
            if ($which == 'congress')   $this->runSample('congress_district');
            
            echo $this->progress($row = null)."\r";

            echo $this->basicLine();
            $this->info("Compared current with: $this->new_master");
            // $this->info("Sample size:           ".number_format($this->sample_size)." voters");
            //$this->info("Tolerance:             ".number_format($this->tolerance)."%");

        }

        echo "\n";

    }

    public function showAllNulls()
    {
        $this->start_time           = Carbon::now();

        $work = ['City'     => 'city_code',
                 'County'   => 'county_code',
                 'House'    => 'house_district',
                 'Senate'   => 'senate_district',
                 'Congress' => 'congress_district'];

        echo str_pad('TYPE', 15, " ")." NULLS\n-----------------------\n";
        foreach ($work as $label => $field) {
            echo str_pad($label, 15, " ")." "
                .number_format(
                    ImportedVoterMaster::where($field, '<', 1)->orwhereNull($field)->count()
                )."\n";
            // echo str_pad($label, 15, " ")." "
            //     .number_format(
            //         ImportedVoterMaster::whereNull($field)->count()
            //     )."\n";
        }

        echo "\n";
        echo $this->progress($row = null)."\r";

    }


    // public function getRandomCollection($model, $limit)
    // {
    //     echo "Counting $model...\n";

    //     $this->expected_num_rows = $limit;

    //     $max = $model::count();
    //     $untraversed = $max;
    //     $rows = [];

    //     $pointer = 0;
        

    //     // $this->info("Jump size: $jump (".round($max/$limit, 4)." x ".number_format($limit)." -> ".number_format($max).")");

    //     echo "Building random collection for $model: \n";

    //     for ($i=0; $i < $limit; $i++) {

    //         $jump   = ceil($untraversed/($limit - $i));

    //         $next = false;
    //         while ($next == false) {
    //             $pointer += rand(1, $jump);
    //             if ($pointer > $max) {
    //                 $pointer = 0;
    //                 $untraversed = 0;
    //                 $this->info('Boing');
    //             } else {
    //                 $next = true;
    //             }
    //         }
    //         $model = $model::skip($pointer)->take(1)->first();
    //         $rows[] = $model;

    //         $untraversed -= $jump;

    //         echo    $this->progress($i)."\r";

    //         // echo $untraversed."\t".$pointer."\t".$jump."\t".$model->full_name."\n";
    //     }

    //     return collect($rows);
    // }

    public function runSample($field)
    {
        //select city_code, COUNT(*) from x_voters_MA_master_1613694084 group by city_code order by city_code

        //$old_sub = VoterMaster::inRandomOrder()->take($this->sample_size);
        // $old = VoterMaster::select($field, DB::raw('count(*) as total'))
        //          ->whereNotNull($field)
        //          ->groupBy($field)
        //          ->get();

        //$new_sub = ImportedVoterMaster::inRandomOrder()->take($this->sample_size);
        // $new = ImportedVoterMaster::select($field, DB::raw('count(*) as total'))
        //          ->whereNotNull($field)
        //          ->groupBy($field)
        //          ->pluck('total', $field);


        // $old = $this->getRandomCollection('App\VoterMaster', $this->sample_size)
        //             ->groupBy($field)
        //             ->map(function ($item, $key) use ($field) {
        //                 return [$field => $key, 'total' => collect($item)->count()];
        //             })->sort();
                    
        // $new = $this->getRandomCollection('App\Models\ImportedVoterMaster', $this->sample_size)
        //             ->groupBy($field)
        //             ->map(function ($item, $key) {
        //                 return collect($item)->count();
        //             })->sort();

        if ($this->option('no_archived_at')) {
            $archived_at_clause = "WHERE archived_at IS NULL";
        } else {
            $archived_at_clause = null;
        }

        $old_sql = "SELECT $field, COUNT(*) as total
                    FROM x_voters_{$this->state}_master
                    {ARCHIVED_AT_CLAUSE}
                    GROUP BY $field 
                    ORDER BY $field";
        $old_sql = str_replace('{ARCHIVED_AT_CLAUSE}', $archived_at_clause, $old_sql);
        $old = DB::connection('voters')->select($old_sql);


        $new_sql = "SELECT $field, COUNT(*) as total 
                    FROM $this->new_master
                    {ARCHIVED_AT_CLAUSE}
                    GROUP BY $field 
                    ORDER BY $field";
        $new_sql = str_replace('{ARCHIVED_AT_CLAUSE}', $archived_at_clause, $new_sql);
        $new = DB::connection('voters')->select($new_sql);


        $new = collect($new)->pluck('total', $field);

        $combined = [];

        foreach($old as $obj) {

            $obj = (object) $obj;

            if (isset($new[$obj->$field])) {
                $total_new  = $new[$obj->$field];
                $the_diff   = $total_new - $obj->total;
            } else {
                $total_new = 0;
                $the_diff = $obj->total;
            }

            $combined[] = [$field       => $obj->$field,
                           'total'      => $obj->total,
                           'total_new'  => $total_new,
                           'diff'       => $the_diff
                          ];
        }

        //////////////////////////////////////////////////

        echo "\n";
        echo str_pad('WHERE', 15, " ")." ".
             "OLD"."\t".
             "NEW"."\t".
             "DIFF"."\t".
             "VARIANCE"."\n";

        $above_tolerance = 0;
        $variances = [];

        foreach($combined as $jurisdiction) {

            $jurisdiction = (object) $jurisdiction;

            $variance = round(abs($jurisdiction->diff) / $jurisdiction->total *100);

            if ($variance == 0) {
                $variance = "-";
            } else if ($variance > $this->tolerance) {
                $variance = $this->r1.number_format($variance).'%'.$this->color_reset;
                $above_tolerance++;
            } else {
                $variance = number_format($variance).'%';
            }

            $the_diff = ($jurisdiction->diff == 0) ? '' : $jurisdiction->diff;

            switch ($field) {
                case 'city_code':
                    $model = Municipality::where('code', $jurisdiction->$field)->where('state', $this->state)->first();
                    break;

                case 'county_code':
                    $model = County::where('code', $jurisdiction->$field)->where('state', $this->state)->first();
                    break;

                case 'house_district':
                    $model = District::where('type', 'H')->where('state', $this->state)->where('code', $jurisdiction->$field)->first();
                    break;

                case 'senate_district':
                    $model = District::where('type', 'S')->where('state', $this->state)->where('code', $jurisdiction->$field)->first();
                    break;

                case 'congress_district':
                    $model = District::where('type', 'F')->where('state', $this->state)->where('code', $jurisdiction->$field)->first();
                    break;
            }

            $the_place = ($model) ? $model->name : '* s'.$jurisdiction->$field;

            echo str_pad(substr($the_place, 0, 15), 15, " ")." ".
                 $jurisdiction->total."\t".
                 $jurisdiction->total_new."\t".
                 $the_diff."\t".
                 $variance."\n";

            $variances[] = $variance;
        }

        echo $this->basicLine();
        if ($above_tolerance > 0) {
            $this->error(" ----> # above tolerance: $above_tolerance ");
            echo "\n";
        }

        $this->info('Average variance: '.number_format(100 *array_sum($variances)/count($variances)).'%');

    }

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }
}
