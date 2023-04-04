<?php

namespace App;

use App\Voter;
use App\VoterSlice;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $connection = 'main';

    public function voterCount($count_record, $index = null)
    {
        if ($index) return $count_record->municipalities[$index][$this->code];
        return $count_record->municipalities['voters'][$this->code];
    }

    public function voters()
    {
        return $this->hasMany(Voter::class, 'city_code', 'code');
    }

    public function getPrecinctsAttribute()
    {
      $precincts = [];
      $precincts_raw = Voter::where('city_code', $this->code)->groupBy('precinct')->orderBy('precinct')->pluck('precinct');
      foreach ($precincts_raw as $precinct) {
        if ($precinct) {
          $precincts[] = $precinct;
        }
      }
      return $precincts;

    }

    public function getWardsAttribute()
    {
        //  Format:
        //  [
        // 		1 => [1,2],
        // 		2 => [1,2,3],
        // 	]
        $wps = Voter::whereNull('archived_at')
                      ->select('ward', 'precinct')
                      ->where('city_code', $this->id)
                      ->groupBy('ward', 'precinct')
                      ->get();
        $wards = [];
        foreach ($wps as $wp) {
            $ward = $wp->ward;
            if (! $wp->ward) {
                $ward = 0;
            }
            if ($wp->precinct) {
              $wards[$ward][$wp->precinct] = 1;
              ksort($wards[$ward]);
            }
        }
        ksort($wards);
        //dd($wards);
        return $wards;
    }

    public function getStreetsAttribute()
    {
        $streets = Voter::whereNull('archived_at')
                      ->select('address_street')
                      ->where('city_code', $this->id)
                      ->groupBy('address_street')
                      ->orderBy('address_street')
                      ->pluck('address_street');

        return $streets;
    }

    public function streetsByWardsPrecincts($wp_arr)
    {

      if (count($wp_arr) < 1) {
        return $this->streets;
      }
      $has_wards = false;
      $this_city = [];
      $precincts = [];
      foreach ($wp_arr as $city_ward_precinct => $cwp_selected) {
        $cwp_arr = explode('_', $city_ward_precinct);
        if (count($cwp_arr) == 3) {
          $c = $cwp_arr[0];
          $w = $cwp_arr[1];
          $p = $cwp_arr[2];
          
          if ($c == $this->id && $cwp_selected) {
            $precincts[] = $p;
            $this_city[] = "'".$w."_".$p."'";
            if ($w == 0) {
              $this_city[] = "'_".$p."'";
            }
            if ($w > 0) {
              $has_wards = true;
            }
          }
        }
      }
      if (count($this_city) < 1) {
        return $this->streets;
      }
      //dd(implode($this_city, ','));
      //$raw_where_in = "Laz";
      if ($has_wards) {
        $raw_where_in = "CONCAT(`ward`, '_', `precinct`) IN (".implode($this_city, ',').")";
      } else {
        $raw_where_in = "`precinct` IN (".implode($precincts, ',').")";
      }
      //dd($this_city, $raw_where_in);
        $streets = Voter::whereNull('archived_at')
                      ->select('address_street')
                      ->where('city_code', $this->id)
                      ->whereRaw($raw_where_in)
                      ->groupBy('address_street')
                      ->orderBy('address_street')
                      ->pluck('address_street');

        return $streets;
    }
}
