<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CallLogViewModel extends Model
{
    // Based on: https://stitcher.io/blog/laravel-view-models

    public function __construct(User $user)
    {
        $this->contacts = $user->contacts()->orderBy('date', 'desc');

        $this->total = $this->contacts->count();

        $this->contacts_by_date = $user->contacts()->orderBy('date', 'desc')
                                                  ->take(100)
                                                  ->get()
                ->each(function ($item) {
                    $item['text'] = $item['subject'].' '.$item['notes'];
                })->each(function ($item) {
                    $string = $item['text'];
                    //$pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
                    $pattern = '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})/i';
                    preg_match_all($pattern, $string, $matches);
                    if ($matches[0]) {
                        $item['emails'] = $matches[0];
                        //dd($matches);
                    }
                })
                                                  ->groupBy('date_clean');

        $this->today = Carbon::today()->toDateString();

        $this->last_7_start = Carbon::today()->subDays(7)->toDateString();
        $this->last_7 = $user->contacts()->orderBy('date', 'desc')
                                                   ->whereDate('date', '>=', Carbon::today()->subDays(7))
                                                   ->whereDate('date', '<=', Carbon::today())
                                                   ->count();

        $this->last_30_start = Carbon::today()->subDays(30)->toDateString();
        $this->last_30 = $user->contacts()->orderBy('date', 'desc')
                                                   ->whereDate('date', '>=', Carbon::today()->subDays(30))
                                                   ->whereDate('date', '<=', Carbon::today())
                                                   ->count();

        $this->this_week_start = Carbon::now()->startOfWeek()->toDateString();
        $this->this_week_end = Carbon::now()->endOfWeek()->toDateString();
        $this->this_week = $user->contacts()->orderBy('date', 'desc')
                                                   ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                                   ->count();

        $this->this_month_start = Carbon::now()->startOfMonth()->toDateString();
        $this->this_month_end = Carbon::now()->endOfMonth()->toDateString();
        $this->this_month = $user->contacts()->orderBy('date', 'desc')
                                                   ->whereMonth('date', '=', Carbon::now()->month)
                                                   ->whereYear('date', '=', Carbon::now()->year)
                                                   ->count();
    }
}
