<?php

namespace App\Models\Admin;

use App\Candidate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CandidateStats extends Model
{
    public $date;
    public $month;
    public $year;
    public $summary;

    public function __construct($date)
    {
        $this->date = $date;
        $this->month = Carbon::parse($date)->format('n');
        $this->year = Carbon::parse($date)->format('Y');

        $candidates = Candidate::whereMonth('organized_at', $this->month)
                            ->whereYear('organized_at', $this->year)
                            ->get();

        $summary = ['month' => null,
                              'total' => null,
                              'dem' => null,
                              'gop' => null,
                              'ind' => null,
                              'men' => null,
                              'women' => null,
                             ];

        $summary['month'] = Carbon::now()->format('F');
        $summary['total'] = $candidates->count();
        $summary['men'] = $candidates->reject(function ($item) {
            if (! $item->voter) {
                return true;
            }

            return ($item->voter->gender == 'M') ? false : true;
        })->count();
        $summary['women'] = $candidates->reject(function ($item) {
            if (! $item->voter) {
                return true;
            }

            return ($item->voter->gender == 'F') ? false : true;
        })->count();
        $summary['dem'] = $candidates->reject(function ($item) {
            if (! $item->voter) {
                return true;
            }

            return ($item->voter->party == 'D') ? false : true;
        })->count();
        $summary['gop'] = $candidates->reject(function ($item) {
            if (! $item->voter) {
                return true;
            }

            return ($item->voter->party == 'R') ? false : true;
        })->count();
        $summary['ind'] = $candidates->reject(function ($item) {
            if (! $item->voter) {
                return true;
            }

            return ($item->voter->party == 'U') ? false : true;
        })->count();
        $summary['age'] = number_format(
                                        $candidates->each(function ($item) {
                                            if (! $item->voter) {
                                                return;
                                            }
                                            $item['age'] = $item->voter->age;
                                        })->average('age')
                                    );

        $this->summary = $summary;
    }
}
