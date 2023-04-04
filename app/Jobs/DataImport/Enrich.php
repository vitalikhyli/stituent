<?php

namespace App\Jobs\DataImport;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Enrich implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $voters;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($voters)
    {
        $this->voters = $voters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->voters as $thevoter) {

            // Households
            $thevoter->household_id = $thevoter->generateHouseholdId();
            $thevoter->full_address = $thevoter->generateFullAddress();

            // Full Names
            $thevoter->full_name = titleCase($thevoter->first_name.' '.$thevoter->last_name);
            $thevoter->full_name_middle = titleCase($thevoter->first_name.' '.$thevoter->middle_name.' '.$thevoter->last_name);

            //Election Data
            $thevoter->elections = json_encode([
                        'MA-2016-11-08-STATE0000000000-329-U-0' => '0001-R-0',
                        'MA-2014-11-08-STATE0000000000-329-U-0' => '0001-R-0',
                        'MA-2012-11-08-STATE0000000000-329-U-0' => '0001-U-0',
                    ]);

            // Finish
            $thevoter->job_enriched = 1;
            $thevoter->save();
        }
    }
}
