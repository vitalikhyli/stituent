<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Voter;
use App\VoterSlice;
use App\VoterSliceCount;

use App\Municipality;
use App\County;
use App\District;

use Schema;
use Carbon\Carbon;


class CountSlices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:count_slices {--state=} {--slice=} {--from=}';

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
    public function handle()
    {

        $start = Carbon::now();

        if ($this->option('state')) {
            $slices = VoterSlice::where('name', 'like', 'x_'.$this->option('state').'%')->get();
        } elseif (!$this->option('slice')) {
            $slices = VoterSlice::all();
        } else {
            $slices = VoterSlice::where('name', $this->option('slice'))->get();
        }

        if ($this->option('from')) {
          $slices = VoterSlice::where('name', '>=', $this->option('from'))
                              ->orderBy('name')
                              ->get();
        }

        foreach ($slices as $slice) {

            if (!Schema::hasTable($slice->name)) {
                $this->info('**** Table '.$slice->name.' does not exist');
                continue;
            }

            $this->info('Counting up '.$slice->name);

            $record = $slice->countRecord;

            if (!$record) {
                $record = new VoterSliceCount;
                $record->slice_id = $slice->id;
                $record->save();
                echo 'Created count record'."\n";
            }

            $record->slice              = $this->generateArray($slice, 'slice');
            $record->save();

            $record->municipalities     = $this->generateArray($slice, 'municipalities');
            $record->save();

            $record->counties           = $this->generateArray($slice, 'counties');
            $record->save();

            $record->congress_districts = $this->generateArray($slice, 'congress_districts');
            $record->save();

            $record->house_districts    = $this->generateArray($slice, 'house_districts');
            $record->save();
            
            $record->senate_districts   = $this->generateArray($slice, 'senate_districts');
            $record->save();
        }

        $this->info('**** Command took '.Carbon::parse($start)->diffInMinutes().' minutes ****');

    }

    public function generateArray($slice, $type)
    {

        session()->put('team_table', $slice->name);

        $array = [
                  'voters'          => [],
                  'democrats'       => [],
                  'republicans'     => [],
                  'unenrolled'      => [],
                  'men'             => [],
                  'women'           => []
                 ];

        //////////////////////////////////////////////////////////////////

        if ($type == 'slice') {

            $query = Voter::whereNull('archived_at');
            $array['voters']       = (clone $query)->count();

            $array['democrats']    = (clone $query)->where('party', 'D')->count();
            $array['republicans']  = (clone $query)->where('party', 'R')->count();
            $array['unenrolled']   = (clone $query)->where('party', 'U')->count();
            $array['men']          = (clone $query)->where('gender', 'M')->count();
            $array['women']        = (clone $query)->where('gender', 'F')->count();

            echo 'State'."                         \r";

            return $array;
        }

        //////////////////////////////////////////////////////////////////

        if ($type == 'municipalities') {
            $jurisdictions = Municipality::where('state', $slice->state)->get();
            $code_field = 'city_code';
        }

        if ($type == 'counties') {
            $jurisdictions = County::where('state', $slice->state)->get();
            $code_field = 'county_code';
        }

        if ($type == 'congress_districts') {
            $jurisdictions = District::where('state', $slice->state)->where('type', 'F')->get();
            $code_field = 'congress_district';
        }

        if ($type == 'house_districts') {
            $jurisdictions = District::where('state', $slice->state)->where('type', 'H')->get();
            $code_field = 'house_district';
        }

        if ($type == 'senate_districts') {
            $jurisdictions = District::where('state', $slice->state)->where('type', 'S')->get();
            $code_field = 'senate_district';
        }

        //////////////////////////////////////////////////////////////////

        foreach($jurisdictions as $place) {

            $query = Voter::where($code_field, $place->code);
            $array['voters'][$place->code]       = (clone $query)->count();

            $array['democrats'][$place->code]    = (clone $query)->where('party', 'D')->count();
            $array['republicans'][$place->code]  = (clone $query)->where('party', 'R')->count();
            $array['unenrolled'][$place->code]   = (clone $query)->where('party', 'U')->count();
            $array['men'][$place->code]          = (clone $query)->where('gender', 'M')->count();
            $array['women'][$place->code]        = (clone $query)->where('gender', 'F')->count();

            echo $place->name."                         \r";

        }

        return $array;        

    }
}
