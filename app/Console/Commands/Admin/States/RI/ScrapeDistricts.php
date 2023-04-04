<?php

namespace App\Console\Commands\Admin\States\RI;

use App\District;
use App\Traits\ScrapingTrait;
use Illuminate\Console\Command;

class ScrapeDistricts extends Command
{
    use ScrapingTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:ri_districts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrapes info from Wikipedia and puts it into District model.';

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
        $scope = $this->anticipate('Which districts to get? [house, senate, congress, all]', ['house', 'senate', 'congress', 'all']);

        if ($scope == 'all') {
            $legislators_a = $this->scrapePage('house');
            $legislators_b = $this->scrapePage('senate');
            $legislators_c = $this->scrapePage('congress');
            $legislators = array_merge($legislators_a, $legislators_b, $legislators_c);
        }

        if ($scope == 'house') {
            $legislators = $this->scrapePage('house');
        }

        if ($scope == 'senate') {
            $legislators = $this->scrapePage('senate');
        }

        if ($scope == 'congress') {
            $legislators = $this->scrapePage('congress');
        }

        $this->bannerMessage('Scraping Done');

        $link = $this->confirm('Add this data to District model?');

        if (!$link) dd('Process stopped.');

        foreach($legislators as $legislator) {

          $district = District::where('state', 'RI')
                              ->where('type', $legislator['type'])
                              ->where('code', $legislator['district'])
                              ->first();
          if (!$district) {
            $district = new District;
            $this->info('Adding '.$legislator['type']."\t".$legislator['district']."\t".$legislator['name']);
          }

          $district->state                       = 'RI';
          $district->code                        = trim($legislator['district']);
          $district->name                        = trim($legislator['district_name']);
          $district->type                        = trim($legislator['type']);
          $district->elected_official_name       = trim($legislator['name']);
          $district->elected_official_party      = trim($legislator['party']);
          $district->elected_official_residence  = trim($legislator['residence']);

          $district->save();

        }

        $this->bannerMessage('Update Done');

    }

    public function scrapePage($page)
    {
        $legislators = [];

        $path = $this->getOrCreateStorageDir('/app/RI-district-webpages');

        if ($page == 'house') {
            $type = 'H';
            $url = 'https://en.wikipedia.org/wiki/Rhode_Island_House_of_Representatives';
            $start_block = 'This list is of members elected';
            $end_block = 'Past composition of the';
            $column_num = ['district'   => 0,
                           'name'        => 1,
                           'party'       => 2,
                           'residence'   => 3,
                           // 'started'     => 6,
                           ];
        }

        if ($page == 'senate') {
            $type = 'S';
            $url = 'https://en.wikipedia.org/wiki/Rhode_Island_Senate';
            $start_block = 'Edit section: Members of the Rhode Island Senate';
            $end_block = 'Key Senate Staff';
            $column_num = ['district'   => 0,
                           'name'       => 1,
                           'party'       => 2,
                           'residence'   => 3,
                          // 'started'     => 5,
                          ];
        }

        if ($page == 'congress') {

            // Small enough to hard code

            $legislators[]   = ['type'          => 'F',
                                'district'      => 1,
                                'district_name' => 'First',
                                'name'          => 'David Cicilline',
                                'party'         => 'D',
                                'residence'     => 'Providence'];

            $legislators[]   = ['type'          => 'F',
                                'district'      => 2,
                                'district_name' => 'Second',
                                'name'          => 'James Langevin',
                                'party'         => 'D',
                                'residence'     => 'Warwick'];

            return $legislators;
        }

        $path_file = $path.'/'.$page.'.htm';

        $html = $this->downloadOrUseExistingThenGetContents($url, $path_file, $redownload = null);

        $html = $this->reduceHTML($html, $start_block, $end_block);

        $rows = $this->getElementsByTag($html, '<tr>', '</tr>', $include_bookends = true);

        foreach ($rows as $therow) {
            $cells = $this->getElementsByTag($therow, '<td', '</td>', $include_bookends = true);

            if (! $cells) {
                continue;
            }
            
            $legislators[] = ['type'          => $type,
                              'district'      => trim(strip_tags($cells[$column_num['district']])),
                              'district_name' => ucfirst($page).' District '.trim(strip_tags($cells[$column_num['district']])),
                              'name'          => trim(preg_replace('/'.'&#91.*&#93'.'/', '',
                                                    strip_tags($cells[$column_num['name']])
                                               )),
                              'party'         => strip_tags($cells[$column_num['party']]),
                              'residence'     => trim(strip_tags($cells[$column_num['residence']])),

                             ];
        }

        return $legislators;
    }

    public function bannerMessage($message)
    {
        echo str_repeat('-', 60)."\r\n";
        echo $message."\r\n";
        echo str_repeat('-', 60)."\r\n";
    }
}
