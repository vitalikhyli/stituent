<?php

namespace App\Console\Commands\Admin\States\MA\Old;

use App\District;
use App\Traits\ScrapingTrait;
use Illuminate\Console\Command;

class MAScrapeDistricts extends Command
{
    use ScrapingTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:scrape_districts';

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
        $scope = $this->anticipate('Which to get? [house, senate, congress, all]', ['house', 'senate', 'congress', 'all']);

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

        print_r($legislators);

        $this->bannerMessage('Done!');

        $link = $this->confirm('Add this data to District model?');

        $district = District::all();
        $districts_array = [];

        foreach ($district as $thedistrict) {
            $key_district = trim(preg_replace("/[^A-Za-z0-9?!\s]/", '', $thedistrict->name));
            $key_district = str_replace('  ', ' ', $key_district);

            $districts_array[$thedistrict->type.' '.$key_district] = $thedistrict->id;
        }

        print_r($districts_array);

        $i = 0;
        if ($link) {
            foreach ($legislators as $legislator) {
                $find_district = trim(preg_replace("/[^A-Za-z0-9?!\s]/", '', $legislator['district']));
                $find_district = str_replace('  ', ' ', $find_district);
                $find_district = $legislator['type'].' '.$find_district;

                echo str_repeat('-', 60)."\r\n";
                echo 'Looking for: '.$find_district."\r\n";

                if (isset($districts_array[$find_district])) {
                    $model = District::find($districts_array[$find_district]);

                    if ($model) {
                        echo $i++.' Found String Match: '.$model->name.' (id = '.$model->id.')'."\r\n";
                        print_r($legislator);
                        $model->elected_official_name = trim($legislator['name']);
                        $model->elected_official_party = trim($legislator['party']);
                        $model->elected_official_residence = trim($legislator['residence']);
                        $model->elected_official_started = trim($legislator['started']);
                        $model->save();
                    }
                }
            }
        }
    }

    public function scrapePage($page)
    {
        $legislators = [];

        $path = $this->getOrCreateStorageDir('/app/MA-district-webpages');

        if ($page == 'house') {
            $type = 'H';
            $url = 'https://en.wikipedia.org/wiki/Massachusetts_House_of_Representatives';
            $start_block = 'The following is a complete list of Members';
            $end_block = 'Past composition of the';
            $column_num = ['district'   => 4,
                           'name'        => 0,
                           'party'       => 2,
                           'residence'   => 3,
                           'started'     => 6,
                           ];
        }
        if ($page == 'senate') {
            $type = 'S';
            $url = 'https://en.wikipedia.org/wiki/Massachusetts_Senate';
            $start_block = 'Current members of the Senate';
            $end_block = 'Past composition of the';
            $column_num = ['district'   => 3,
                           'name'       => 0,
                          'party'       => 2,
                          'residence'   => 4,
                          'started'     => 5,
                          ];
        }
        if ($page == 'congress') {
            $type = 'F';
            $url = 'https://en.wikipedia.org/wiki/Massachusetts%27s_congressional_districts';
            $start_block = 'List of members of the Massachusetts';
            $end_block = 'Enumeration trends';
            $column_num = ['district'   => 0,
                           'name'       => 0,
                           'party'      => 1,
                           'residence'  => 0,
                           'started'    => 3,
                          ];
        }

        $path_file = $path.'/'.$page.'.htm';

        $html = $this->downloadOrUseExistingThenGetContents($url, $path_file, $redownload = true);

        $html = $this->reduceHTML($html, $start_block, $end_block);

        $rows = $this->getElementsByTag($html, '<tr>', '</tr>', $include_bookends = true);

        foreach ($rows as $therow) {
            $cells = $this->getElementsByTag($therow, '<td', '</td>', $include_bookends = true);

            if (! $cells) {
                continue;
            }
            // print_r($cells);
            $legislators[] = ['type'        => $type,
                              'district'    => $this->englishOrdinal(
                                                str_replace('Worchester ', 'Worcester ', // YEAH
                                                str_replace('and ', '& ',
                                                           strip_tags($cells[$column_num['district']])
                                                           )
                                                )
                                                ),
                              'name'        => preg_replace('/'.'&#91.*&#93'.'/', '',
                                                    strip_tags($cells[$column_num['name']])
                                               ),
                              'party'       => strip_tags($cells[$column_num['party']]),
                              'residence'   => strip_tags($cells[$column_num['residence']]),
                              'started'     => substr(
                                                   preg_replace(
                                                        '/[^0-9]/',
                                                        '',
                                                        strip_tags($cells[$column_num['started']])
                                                   ),
                                                0, 4), // Four digit year
                             ];
        }

        // Special process for Congress
        if ($page == 'congress') {
            $legislators = $this->processCongress($legislators);
        }

        // print_r($legislators);
        return $legislators;
    }

    public function processCongress($legislators)
    {
        foreach ($legislators as $key => $legislator) {

            // Remove town from "Bill Keating (D-Bourne)"
            $legislators[$key]['name'] = trim(
                                            substr(
                                                $legislator['name'],
                                                0,
                                                strpos($legislator['name'], '(')
                                            )
                                        );

            // Remove name from "Bill Keating (D-Bourne)"
            $legislators[$key]['residence'] = trim(
                                                substr(
                                                    $legislator['name'],
                                                    strpos($legislator['name'], '(') + 3,
                                                    strlen($legislator['name']) - strpos($legislator['name'], '(') - 3 - 1 - 1
                                                )
                                            );

            // Get Year from "October 16, 2001 – present"
            $ex = '/'.'([1-2][0-9][0-9][0-9])'.'/';
            preg_match($ex, $legislators[$key]['started'], $matches);
            if (isset($matches[0])) {
                $legislators[$key]['started'] = $matches[0];
            }

            //Simple count
            $legislators[$key]['district'] = $this->englishOrdinal($key + 1);
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
