<?php

namespace App\Console\Commands\Admin;

use Carbon\Carbon;
use Illuminate\Console\Command;

class GetHouseholdAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:get_household_addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes a CSV of people, groups them by household and puts family name.';

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
        $file = fopen(storage_path().'/app/csvs/nahant.csv', 'r');

        $addresses = [];
        $count = 0;
        while ($row = fgetcsv($file)) {
            $count++;
            //dd($row);
            if ($count == 1) {
                continue;
            }
            //dd($row);
            $address = $row[9];

            $lastname = $row[4];
            $addresses[$address]['families'][$lastname][] = $row;
            
            $addresses[$address]['city'] = $row[10];
            $addresses[$address]['zip'] = $row[11];
            if (isset($addresses[$address]['count'])) {
                $addresses[$address]['count']++;
            } else {
                $addresses[$address]['count'] = 1;
            }

            if ($count > 20) {
                //break;
            }
        }
        //dd($addresses);

        $output = fopen(storage_path().'/app/csvs/Nahant_Households.csv', 'w');
        foreach ($addresses as $address => $addressinfo) {
            $row = [];

            $bestname = '';
            $families = [];
            if (isset($addressinfo['families'])) {
                $families = $addressinfo['families'];
            }
            //dd($families);

            if (count($families) == 1) {
                $peoplecount = count($families[array_key_first($families)]);
                if ($peoplecount == 1) {
                    //dd($families[array_key_first($families)][0][0]);
                    $person = $families[array_key_first($families)][0];
                    $bestname = $person[2].' '.$person[4];
                }
                if ($peoplecount > 1) {
                    $bestname = array_key_first($families).' Household';
                }
            }
            if (count($families) > 1) {
                $bestname = 'Resident';
                // $max = 0;
                // foreach ($families as $family => $people) {
                //     if (count($people) > $max) {
                //         $bestname = $family." Household";
                //         $max = count($people);
                //     }
                // }
            }
            if (count($families) < 1) {
                // No currently registered voters
                $bestname = 'Resident';
            }
            $row['best_name'] = $bestname;

            $row['address'] = $address;
            $row['city'] = $addressinfo['city'];
            $row['zip'] = $addressinfo['zip'];

            $allresidents = [];
            foreach ($families as $family => $people) {
                foreach ($people as $person) {
                    $allresidents[] = trim($person[2].' '.$person[4]).' ('.$person[5].', Reg: '.Carbon::parse($person[7])->format('Y').')';
                }
            }
            $row['all_residents'] = implode(', ', $allresidents);

            //dd($row);

            fputcsv($output, $row);
        }
        //dd($addresses);
    }
}
