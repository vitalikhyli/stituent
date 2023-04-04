<?php

namespace App\Console\Commands\OneTime;

use App\District;
use Illuminate\Console\Command;

class PopulateDistricts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:populate_districts';

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
        $filepath = database_path().'/seeds/voterfiles/DistrictListCombined.csv';
        $file = fopen($filepath, 'r');

        $count = 0;
        while ($row = fgetcsv($file)) {
            $count++;
            if ($count == 1) {
                continue;
            }
            $code = trim($row[0]);
            $type = trim($row[1]);
            $name = str_replace('DISTRICT', '', $row[2]);
            $name = trim(ucwords(strtolower($name)));
            $sort = trim($row[3]);
            $district = District::where('code', $code)
                                ->where('type', $type)
                                ->first();
            if (! $district) {
                $district = new District;
                $district->type = $type;
                $district->code = $code;
            }
            $district->name = $name;
            $district->sort = $sort;
            $district->save();
        }
    }
}
