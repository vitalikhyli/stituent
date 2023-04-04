<?php

namespace Database\Seeders;

use App\County;
use App\Municipality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeographySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cc_counties = DB::connection('cc_remote')
                         ->table('cms_county')
                         ->get();

        foreach ($cc_counties as $cc_county) {
            $county = County::find($cc_county->county_keyID);

            if (! $county) {
                $county = new County;
                $county->id = $cc_county->county_keyID;
                $county->state = $cc_county->county_state;
                $county->name = $cc_county->county_name;
                $county->code = $cc_county->county_code;
                $county->save();
            }
        }
        $cc_municipalities = DB::connection('cc_remote')
                               ->table('cms_city_town')
                               ->get();

        foreach ($cc_municipalities as $cc_municipality) {
            $municipality = Municipality::find($cc_municipality->keyID);

            if (! $municipality) {
                $municipality = new Municipality;
                $municipality->id = $cc_municipality->keyID;
                $municipality->state = $cc_municipality->city_state;
                $municipality->name = $cc_municipality->city_name;
                $municipality->code = $cc_municipality->city_code;
                $municipality->county_id = $cc_municipality->city_county_code;
                $municipality->save();
            }
        }
    }
}
