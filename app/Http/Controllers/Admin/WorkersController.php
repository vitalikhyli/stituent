<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataWorker;
use Artisan;
use Illuminate\Http\Request;

class WorkersController extends Controller
{
    public function startWorker()
    {
        Artisan::call('st:work');
    }

    public function stopWorker()
    {
        $active = DataWorker::whereNull('deleted_at')->get();

        foreach ($active as $theactive) {
            $theactive->markInterrupted();
        }
    }

    public function index()
    {
        $workers = DataWorker::withTrashed()
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('admin.workers.index', compact('workers'));
    }

    public function show($id)
    {
        $worker = DataWorker::withTrashed()->find($id);

        return view('admin.workers.details-modal', compact('worker'));
    }
}
