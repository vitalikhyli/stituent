<?php

namespace App\Http\Middleware;

use Closure;
use Schema;
use Auth;

class SetTeamConstituentTable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tablevoters = $request->user()->team->db_slice;

        //dd($tablevoters);

        if (!$tablevoters) {

            // Use empty Voter table
            $request->session()->put('team_table', 'x__template_voters');
            $request->session()->put('team_state', Auth::user()->team->data_folder_id);

        } elseif (Schema::hasTable($tablevoters)) {
            
            $request->session()->put('team_table', $tablevoters);

            $team_state = substr($tablevoters, 2, 2);
            $request->session()->put('team_state', $team_state);

        } else {

            // $request->session()->put('team_table', 'x_voters_0001');

        }

        return $next($request);
    }
}
