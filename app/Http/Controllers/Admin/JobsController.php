<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataImport;
use App\Models\Admin\DataJob;
use Illuminate\Http\Request;
use Schema;

class JobsController extends Controller
{
    public function jobsIndex()
    {
        $imports = DataImport::orderBy('created_at', 'desc')
                             ->with('jobs')
                             ->get();

        return view('admin.import.jobs', compact('imports'));
    }

    public function rollback($job_id)
    {
        $job = DataJob::find($job_id);

        $jobs_this_and_after = DataJob::where('id', '>=', $job_id)
                                      ->where('data_import_id', $job->data_import_id)
                                      ->orderBy('id', 'desc')
                                      ->get();

        foreach ($jobs_this_and_after as $thejob) {
            $thejob->rollback();

            $v_import = DataImport::find($job->data_import_id);
            $v_import->isNotReady();

            if (($thejob->type == 'createHouseholds') ||
               ($thejob->type == 'createHouseholdsBySlice')) {
                if ($v_import->relatedHouseholds()) {
                    $v_import->relatedHouseholds()->rollback($destroy_table = true, $destroy_import = true);
                }
            } elseif ($thejob->type == 'import') {
                $v_import->rollback($destroy_table = true);
            } else {
                $v_import->rollback();
            }
        }

        session()->flash('startworker', 1);

        return redirect('/admin/data');
    }
}
