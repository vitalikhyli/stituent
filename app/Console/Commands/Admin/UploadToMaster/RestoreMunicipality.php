<?php

namespace App\Console\Commands\Admin\UploadToMaster;

use App\Municipality;
use App\Voter;
use App\VoterMaster;
use Illuminate\Console\Command;

class RestoreMunicipality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:restore_municipality {--municipality_id=}';

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
        $municipality_id = $this->option('municipality_id');
        if (! $municipality_id) {
            echo "Needs import_id like cf:restore_municipality --municipality_id=\n";

            return;
        }
        $municipality = Municipality::find($municipality_id);
        if (! $municipality) {
            echo "Bad id\n";

            return;
        }
        session(['team_table' => 'x_MA_STATE']);

        echo 'Reverting '.$municipality->name."\n";
        echo 'Voter Master has '.VoterMaster::where('city_code', $municipality_id)->count()."\n";
        echo 'Voter Backup has '.Voter::where('city_code', $municipality_id)->count()."\n";

        $master_ids = VoterMaster::where('city_code', $municipality_id)->pluck('id');
        $outside_city = Voter::where('city_code', '!=', $municipality_id)
                             ->whereIn('id', $master_ids)
                             ->get();
        echo 'Outside City: '.$outside_city->count()."\n";

        if (! $this->confirm('Do you wish to delete these Voter master records permanently?')) {
            exit;
        }

        VoterMaster::where('city_code', $municipality_id)->forceDelete();

        $inside_city = Voter::where('city_code', $municipality_id)->get();

        $all = $inside_city->merge($outside_city);
        foreach ($all as $ind => $voter) {
            $vm = new VoterMaster;
            foreach ($voter->toArray() as $index => $val) {
                $vm->$index = $val;
            }
            try {
                $vm->save();
            } catch (\Exception $e) {
                echo 'Error. '.$vm->id."\n";
            }
            echo "\rUpdated ".($ind + 1);
        }
        echo "\n";

        echo 'Reverted '.$municipality->name."\n";
        echo 'Voter Master has '.VoterMaster::where('city_code', $municipality_id)->count()."\n";
        echo 'Voter Backup has '.Voter::where('city_code', $municipality_id)->count()."\n";
    }
}
