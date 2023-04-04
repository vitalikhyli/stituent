<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Municipality;
use App\VoterMaster;
use Illuminate\Support\Str;

class CorrectStreetPrefix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:correct_street_prefix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds voters missing a street prefix (Lowell: 5 W 10th)';

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
        $municipalities = Municipality::where('state', 'MA')->orderBy('name')->get();
        foreach ($municipalities as $municipality) {
            echo "===============================> ".$municipality->name."\n";
            $voters = VoterMaster::where('city_code', $municipality->code)
                                 ->where('original_import', '<>', '[]')
                                 ->where('original_import', 'NOT LIKE', '%"44":null%')
                                 ->whereRaw("address_street NOT REGEXP '^(N|E|S|W) '")
                                 ->get();
            echo $voters->count()." voters.\n";
            $streets_fixed = [];
            foreach ($voters as $voter) {
                $original_import = $voter->original_import;
                if (!isset($original_import['2020-03-01_STATEWIDE_EMERGES'][44])) {
                    echo "Error: ".$voter->id."\n";
                    continue;
                }
                $prefix = $original_import['2020-03-01_STATEWIDE_EMERGES'][44];
                $old_address = $voter->address_street;
                $new_address = $prefix." ".$voter->address_street;
                $streets_fixed[$old_address.' => '.$new_address][] = $voter->address_number." $prefix ".$voter->address_street;

                $full_old_address = $voter->address_number." ".$voter->address_street;
                $full_new_address = $voter->address_number." $prefix ".$voter->address_street;
                
                if ($voter->mailing_info) {
                    $mailing_info = $voter->mailing_info;
                    if (isset($mailing_info['address'])) {
                        $mailing_address = $mailing_info['address'];
                        if (Str::contains($full_old_address, $mailing_address)) {
                            $mailing_info['address'] = $full_new_address;
                            $voter->mailing_info = $mailing_info;
                        }
                    }
                }

                $voter->address_street = $new_address;
                $voter->save();
                //dd($voter);
            }
            foreach ($streets_fixed as $street_name => $address_array) {
                echo "\t".count($address_array)."\t".$street_name."\n";
            }
        }
    }
}

/*

{"2020-03-01_STATEWIDE_EMERGES":
    {"import_order":"5464",
    "voter_id":"07RCY1077002",
    "2":null,
    "county_code":"017",
    "4":null,
    "5":null,
    "last_name":"ROBINSON",
    "first_name":"COREY",
    "middle_name":null,
    "9":null,
    "10":null,
    "name_title":null,
    "suffix_name":null,
    "registration_date":"10\/31\/2011",
    "yob":"00\/00\/1977",
    "15":null,
    "party":"Non-Partisan",
    "17":null,
    "gender":"M",
    "19":null,
    "20":null,
    "21":null,
    "voter_status":"1",
    "23":null,
    "24":null,
    "25":"5 W 10TH ST",
    "26":null,
    "mailing_address_number":"5",
    "mailing_address_street":"10TH",
    "mailing_address_street_type":"ST",
    "30":"W",
    "31":null,
    "mailing_address_city":"LOWELL",
    "mailing_address_state":"MA",
    "mailing_address_zip":"01850",
    "mailing_address_zip4":"2011",
    "mailing_address_apt_type":null,
    "mailing_address_apt":null,
    "38":null,
    "39":"5 W 10TH ST",
    "":null,
    "address_number":"5",
    "address_street":"10TH",
    "address_street_type":"ST",
    "44":"W",
    "45":null,
    "address_city":"LOWELL",
    "address_state":"MA",
    "address_zip":"01850",
    "address_zip4":"2011",
    "address_apt_type":null,
    "address_apt":null,
    "52":null,
    "53":null,
    "54":null,
    "55":null}}


{"2020-03-01_STATEWIDE_EMERGES":{"import_order":"1453","voter_id":"02BMY0164002","2":null,"county_code":"017","4":null,"5":null,"last_name":"BORGES","first_name":"MARY","middle_name":null,"9":null,"10":null,"name_title":null,"suffix_name":null,"registration_date":"03\/04\/2016","yob":"00\/00\/1964","15":null,"party":"Other","17":null,"gender":"F","19":null,"20":null,"21":null,"voter_status":"1","23":null,"24":null,"25":"339 W 6TH ST","26":null,"mailing_address_number":"339","mailing_address_street":"6TH","mailing_address_street_type":"ST","30":"W","31":null,"mailing_address_city":"LOWELL","mailing_address_state":"MA","mailing_address_zip":"01850","mailing_address_zip4":"1974","mailing_address_apt_type":null,"mailing_address_apt":null,"38":null,"39":"339 W 6TH ST","":null,"address_number":"339","address_street":"6TH","address_street_type":"ST","44":"W","45":null,"address_city":"LOWELL","address_state":"MA","address_zip":"01850","address_zip4":"1974","address_apt_type":null,"address_apt":null,"52":null,"53":null,"54":null,"55":null}}

{"2020-03-01_STATEWIDE_EMERGES":{"import_order":"2255","voter_id":"03DAX0171000","2":null,"county_code":"017","4":null,"5":null,"last_name":"DENNIS","first_name":"ALEX","middle_name":null,"9":null,"10":null,"name_title":null,"suffix_name":null,"registration_date":"10\/06\/2020","yob":"00\/00\/1971","15":null,"party":"Democratic","17":null,"gender":null,"19":null,"20":null,"21":null,"voter_status":"1","23":null,"24":null,"25":"390 E MERRIMACK ST APT 5","26":null,"mailing_address_number":"390","mailing_address_street":"MERRIMACK","mailing_address_street_type":"ST","30":"E","31":null,"mailing_address_city":"LOWELL","mailing_address_state":"MA","mailing_address_zip":"01852","mailing_address_zip4":"2437","mailing_address_apt_type":"APT","mailing_address_apt":"5","38":null,"39":"390 E MERRIMACK ST APT 5","":null,"address_number":"390","address_street":"MERRIMACK","address_street_type":"ST","44":"E","45":null,"address_city":"LOWELL","address_state":"MA","address_zip":"01852","address_zip4":"2437","address_apt_type":"APT","address_apt":"5","52":null,"53":null,"54":null,"55":null}}
*/
