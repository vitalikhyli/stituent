<?php

namespace App\Http\Controllers\Admin;

use App\Candidate;
use App\CandidateMarketing;
use App\Http\Controllers\Controller;
use App\Models\Admin\AdminHistoryItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Ensure candidates have a marketing extensions

        $existing_marketing_exts = CandidateMarketing::all()->pluck('candidate_id');
        foreach (Candidate::WhereNotIn('id', $existing_marketing_exts)->get() as $candidate) {
            $marketing = new CandidateMarketing($candidate->id);
            $marketing->save();
        }

        return view('admin.dashboard.main');
    }

}
