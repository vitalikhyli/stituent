<?php

namespace App\Console\Commands\CC;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class PullInUpdatedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:pull_in_updated_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connects to Live CC server to get only new data.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $ints;

    public function __construct()
    {
        $this->ints = $this->getIntsArray();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->updateTableData('cms_voters', 'update_date');

        return;

        $this->updateTableData('call_logs', 'updated_at');
        $this->updateTableData('cms_bulkemail_template', 'bulkemail_template_update_date');
        $this->updateTableData('cms_bulkemail_tracker', 'cms_bulkemail_tracker_date');
        $this->updateTableData('cms_campaigns', 'update_date');
        $this->updateTableData('cms_contact_issue_docs', 'update_date');
        $this->updateTableData('cms_contact_issues', 'update_date');
        $this->updateTableData('cms_donations', 'cms_donations_update_date');
        $this->updateTableData('cms_event_assignment', 'cms_event_assignment_update_date');
        $this->updateTableData('cms_events', 'cms_events_update_date');
        $this->updateTableData('cms_export_tracker', 'cms_export_tracker_date');
        $this->updateTableData('cms_group_assignment', 'update_date');
        $this->updateTableData('cms_issue_assignment', 'update_date');
        $this->updateTableData('cms_issue_categories', 'update_date');
        $this->updateTableData('cms_issue_category_docs', 'update_date');
        $this->updateTableData('cms_job_queue', 'job_start_time');
        $this->updateTableData('cms_users', 'update_date');
        $this->updateTableData('cms_voter_bulkemail', 'cms_voter_bulkemail_date');
        $this->updateTableData('cms_voter_contact', 'update_date');
        $this->updateTableData('cms_voter_export_tracker', 'cms_voter_export_tracker_date');
        $this->updateTableData('cms_voter_groups', 'update_date');
        $this->updateTableData('cms_voter_issues', 'update_date');
        $this->updateTableData('cms_voter_notes', 'update_date');
        $this->updateTableData('cms_voter_private', 'update_date');
        $this->updateTableData('cms_voter_volunteer', 'update_date');
    }

    public function updateTableData($table, $update_date_column)
    {
        $total = DB::connection('cc_remote')
                   ->table($table)
                   ->where($update_date_column, '>', Carbon::now()->subWeeks(2))
                   ->count();
        $this->addToLog("Total for $table: $total\n");

        $count = 0;

        DB::connection('cc_remote')
          ->table($table)
          ->where($update_date_column, '>', Carbon::now()->subWeeks(2))
          ->orderBy($update_date_column)
          ->chunk(1000, function ($updated_rows) use ($table, &$count, $total) {
              $this->addToLog("$count/$total on $table\n");

              $rows_to_insert = [];
              foreach ($updated_rows as $row) {
                  $count++;
                  $rowcollection = collect($row);
                  $id_column = '';
                  $id_val = '';
                  foreach ($rowcollection as $key => $val) {
                      $id_column = $key;
                      $id_val = $val;
                      break;
                  }
                  foreach ($rowcollection as $key => $val) {
                      if ($val == '0000-00-00' || $val == '0000-00-00 00:00:00') {
                          $val = null;
                      }
                      if (! $val) {
                          if (isset($this->ints[$table][$key])) {
                              $val = $this->ints[$table][$key];
                          }
                      }
                      $rowcollection[$key] = $val;
                  }
                  if (! $id_column) {
                      return;
                  }
                  if (env('APP_ENV') == 'local') {
                      if ($table == 'cms_voter_groups') {
                          unset($rowcollection['deleted_at']);
                      }
                      if ($table == 'cms_voter_notes') {
                          continue;
                      }
                      if ($table == 'cms_voter_private') {
                          continue;
                      }
                      if ($table == 'cms_voter_volunteer') {
                          continue;
                      }
                  }
                  $exists = DB::connection('cc_local')
                            ->table($table)
                            ->where($id_column, $id_val)
                            ->exists();
                  if ($exists) {
                      DB::connection('cc_local')
                      ->table($table)
                      ->where($id_column, $id_val)
                      ->update($rowcollection->toArray());
                  } else {
                      $rows_to_insert[] = $rowcollection->toArray();
                  }
              }
              if (count($rows_to_insert) > 0) {
                  $this->addToLog('Inserting '.count($rows_to_insert)." rows.\n");
                  DB::connection('cc_local')
                      ->table($table)
                      ->insert($rows_to_insert);
              }
          });
    }

    public function getIntsArray()
    {
        return [
            'cms_bulkemail_tracker' => [
                    'cms_bulkemail_tracker_failed' => 0,
                    'cms_bulkemail_tracker_read' => 0,
                    'cms_bulkemail_tracker_optouts' => 0,
                    'cms_bulkemail_tracker_excluded' => 0,
                    'cms_bulkemail_tracker_noemail' => 0,
                ],
            'cms_contact_issues' => [
                    'priority' => 0,
                    'userID' => 0,
                ],
            'cms_users' => [
                    'admin' => 0,
                    'campaign_admin' => 0,
                    'can_delete' => 0,
                    'show_campaign_data' => 0,

                ],
            'cms_campaigns' => [
                    'campaign_county' => 0,
                ],
            'cms_voter_bulkemail' => [
                    'cms_voter_bulkemail_read_date' => '1899-12-31 00:00:00',
                ],
            'cms_voters' => [
                    'deceased_date' => '1899-12-31',
                    'voters_campaignID' => 0,
                    'voters_ethnicity' => 0,
                    'custom_district' => 0,
                    'mcrc16' => 0,
                    'archive_date' => '1899-12-31',
                    'alt_gov_district' => 0,
                    'alt_senate_district' => 0,
                    'alt_house_district' => 0,
                    'alt_congress_district' => 0,
                    'gov_district' => 0,
                    'house_district' => 0,
                    'senate_district' => 0,
                    'congress_district' => 0,
                    'city_code' => 0,
                    'county_code' => 0,
                ],
            'cms_voter_private' => [
                    'noemail_date' => '1899-12-31',
                ],
            'cms_voter_volunteer' => [
                    'ranking' => 0,
                ],
        ];
    }

    public function addToLog($str)
    {
        echo $str;
        file_put_contents(storage_path().'/sqldumps/log.txt', date('Y-m-d-hia').': '.$str, FILE_APPEND);
    }
}
