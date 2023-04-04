<?php

namespace Database\Seeders;

use App\BulkEmail;
use App\Models\CC\CCBulkEmail;
use App\Team;
use Illuminate\Database\Seeder;

class BulkEmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BulkEmail::truncate();
        $valid_campaign_ids = Team::pluck('old_cc_id')
                                  ->unique();

        $cc_bulk_query = CCBulkEmail::whereIn('cms_bulkemail_tracker_campaignID', $valid_campaign_ids)
                                    ->with('ccBulkEmailVoters');

        $cc_bulk_query->chunk(100, function ($cc_bulk_emails) {
            foreach ($cc_bulk_emails as $cc_bulkemail) {
                //dd($cc_bulkemail->ccBulkEmailVoters()->count());
                $bulk = new BulkEmail;
                //$bulk
            }
        });
    }
}
