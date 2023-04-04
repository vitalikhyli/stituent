<?php

namespace App\Providers;

use App\GroupPerson;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

// use DB;
// use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        GroupPerson::observe(\App\Observers\GroupPersonObserver::class);
        Paginator::useBootstrap();

        // $auth = Auth::check();

        // DB::listen(function($query) use ($auth) {

        //     $on = true;

        //     if (!$on) return;

        //     $ex = '(?:from `|into `|join `)(.+?)(?:`)';
        //     preg_match_all("/".$ex."/", $query->sql, $tables);
        //     $tables = $tables[1];

        //     if (!in_array('query_unsloth_logs', $tables) &&
        //         !in_array('query_unsloth_history', $tables) &&
        //         !str_starts_with($query->sql, 'explain') &&
        //         !strpos($query->sql, '`password` = ')) {

        //         try {

        //             try {

        //                 $sql_full = $query->sql;
        //                 $bindings = $query->bindings;

        //                 while(strpos($sql_full, '?')) {
        //                     // Remove password somehow so it is not stored
        //                     $sql_full = str_replace('?', reset($bindings), $sql_full);
        //                     $bindings = array_shift($bindings);
        //                 }

        //             } catch  (\Exception $e)  {

        //                 $sql_full = null;
        //             }


        //             try {

        //                 $explain = DB::select("explain ".$sql_full);
        //                 $explain_type = $explain[0]->type;

        //             } catch  (\Exception $e)  {

        //                 $explain = [];
        //                 $explain_type = null;

        //             }

        //             DB::table('query_unsloth_logs')->insert([
        //                 'hash' => substr(md5($query->sql),0,6),
        //                 'date' => \Carbon\Carbon::today()->toDateString(),
        //                 'auth' => $auth,
        //                 'time' => $query->time,
        //                 'explain' => json_encode($explain),
        //                 'explain_type' => $explain_type,
        //                 'tables' => json_encode($tables),
        //                 'sql' => $query->sql,
        //                 'sql_full' => $sql_full,
        //                 'bindings' => json_encode($query->bindings),
        //                 'created_at' => \Carbon\Carbon::now()
        //             ]);

        //         } catch (\Exception $e) {

        //             //

        //         }

        //     }

        // });

    }
}
