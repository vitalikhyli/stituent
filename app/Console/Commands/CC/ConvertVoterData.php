<?php

namespace App\Console\Commands\CC;

use App\Models\CC\CCVoter;
use App\VoterMaster;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class ConvertVoterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:convert_voter_data {--fresh} {--max=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Puts data from CC cms_voters and cms_election_data into FluencyBase format.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $street_types;
    protected $log_file;

    public function __construct()
    {
        parent::__construct();
        $this->street_types = ['ST' => 1,
                               'DR' => 1,
                               'LN' => 1,
                               'AVE' => 1,
                               'TRL' => 1,
                               'RD' => 1,
                               'HTS' => 1,
                               'TPKE' => 1,
                               'TER' => 1,
                               'PATH' => 1,
                               'CIR' => 1,
                               'VLG' => 1,
                               'EXT' => 1,
                               'WAY' => 1,
                               'ROW' => 1,
                           ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! file_exists(storage_path().'/sqldumps')) {
            mkdir(storage_path().'/sqldumps');
        }

        // $voter_table = 'x_voters_0001';
        $voter_table = 'x_voters_MA_master';

        $this->log_file = storage_path().'/sqldumps/log.txt';

        //$indexes = collect(DB::select("SHOW INDEXES FROM x_voters_0001"))->pluck('Key_name');
        //dd($indexes);

        DB::connection('main')->disableQueryLog();
        //DB::statement('ALTER TABLE x_voters_0001 DISABLE KEYS;');

        //session()->put('team_table', $voter_table);

        $completed = 0;
        $last_imported = 0;
        if ($this->option('fresh')) {
            $this->addToLog("Truncating Voter Table $voter_table\n");
            VoterMaster::truncate();
        } else {
            // Figure out where left off
            $last = VoterMaster::take(1)->orderBy('import_order', 'desc')->first();
            if ($last) {
                $last_imported = $last->import_order;
                $completed = CCVoter::where('voterID', '<', $last_imported)->count();
            }
        }

        $this->addToLog("Counting CC Voter Table\n");

        if ($this->option('max')) {
            $total = $this->option('max');
        } else {
            $total = CCVoter::count() - $completed;
        }
        //dd($count);
        $count = 0;

        $start = time();

        $this->addToLog("Starting Conversion on $total rows...\n");

        $max = min($this->option('max'), 10000000);

        $ccvotersquery = CCVoter::where('voterID', '>', $last_imported)
                                ->with('elections');

        $chunksize = 5000;
        if ($this->option('max') && $this->option('max') < 5000) {
            $chunksize = $this->option('max');
        }
        $ccvotersquery->chunk($chunksize, function ($voters) use (&$count, $total, $start, $max) {
            $now = time();
            if ($count > 0) {
                $speed_per_record = ($now - $start) / $count;
                $secondsleft = $speed_per_record * ($total - $count);
                $timeleft = $secondsleft / 60;
                $hoursleft = $timeleft / 60;
                $this->addToLog("$count/$total - Hours Left: $hoursleft\n");
            }

            //dd();

            foreach ($voters as $voter) {
                if ($this->option('max')) {
                    if ($this->option('max') <= $count) {
                        $this->addToLog("Reached max of $max\n");

                        return false;
                    }
                }

                $count++;

                $voter->convertAddOrUpdate();
                //dd($voter, $newvoter);
            }
        });

        // DB::statement('ALTER TABLE x_voters_MA_master ENABLE KEYS;');
    }

    public function addToLog($str)
    {
        echo $str;
        file_put_contents($this->log_file, date('Y-m-d-hia').': '.$str, FILE_APPEND);
    }
}
