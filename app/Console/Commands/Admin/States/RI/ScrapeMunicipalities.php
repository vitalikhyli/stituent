<?php

namespace App\Console\Commands\Admin\States\RI;

use Illuminate\Console\Command;

use App\Traits\ScrapingTrait;

use App\Municipality;
use App\County;

class ScrapeMunicipalities extends Command
{
    use ScrapingTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:ri_cities';

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
     * @return int
     */

    public $state = 'RI';

    public function handle()
    {
        $path = $this->getOrCreateStorageDir('/app/RI-district-webpages');

        $url            = 'https://en.wikipedia.org/wiki/List_of_municipalities_in_Rhode_Island';
        $start_block    = 'and largest city';
        $end_block      = 'List of census-designated places in Rhode Island';
        $column_num     = ['name'   => 0,
                          'county'  => 2];

        $path_file = $path.'/municipalities.htm';

        $html = $this->downloadOrUseExistingThenGetContents($url, $path_file, $redownload = null);

        $html = $this->reduceHTML($html, $start_block, $end_block);

        $rows = $this->getElementsByTag($html, '<tr>', '</tr>', $include_bookends = true);

        $cities = [];

        foreach ($rows as $therow) {

            $cells = $this->getElementsByTag($therow, '<td', '</td>', $include_bookends = true);

            if (!$cells) {
                continue;
            }

            $i = 1;
            $cities[] = [
                         'name'     => trim(strip_tags($cells[$column_num['name']])),
                         'county'   => trim(strip_tags($cells[$column_num['county']]))
                        ];
        }

        echo str_repeat('-', 70)."\n";
        
        $counties = collect($cities)->pluck('county')->unique()->sort();

        $h = 1;
        foreach($counties as $county_name) {
            $model = County::where('state', $this->state)->where('name', $county_name)->first();

            if (!$model) {
                $model = new County;
            }
            $model->state = $this->state;
            $model->name = $county_name;
            $model->code = $h++;
            $model->save();

            $this->info(($h - 1)."\t".$model->name);
        }

        echo str_repeat('-', 70)."\n";

        $i = 1;
        foreach($cities as $city) {
            $model = Municipality::where('state', $this->state)
                                 ->where('name', $city['name'])
                                 ->first();

            if (!$model) {
                $model = new Municipality;
            }

            $model->state = $this->state;

            $county = County::where('state', $this->state)->where('name', $city['county'])->first();
            if ($county) {
                $model->county_id = $county->id;
            }

            $model->name = $city['name'];
            $model->code = $i++;

            $model->save();

            $this->info(($i - 1)."\t".$model->name);
        }

    }
}
