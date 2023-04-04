<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Notice;
use Auth;
use Illuminate\Http\Request;

class NoticesController extends Controller
{
    public function index()
    {
        $notices = Notice::orderBy('publish_at', 'desc');

        if (isset($_GET['app_type']) && ($_GET['app_type'])) {
            $notices = $notices->where('app_type', $_GET['app_type']);
        }

        $notices = $notices->get();

        return view('admin.notices.index', compact('notices'));
    }

    public function approve($id)
    {
        $notice = Notice::find($id);
        $notice->approved = true;
        $notice->save();

        return redirect()->back();
    }

    public function unapprove($id)
    {
        $notice = Notice::find($id);
        $notice->approved = false;
        $notice->save();

        return redirect()->back();
    }

    public function archive($id)
    {
        $notice = Notice::find($id);
        $notice->archived_at = now();
        $notice->save();

        return redirect()->back();
    }

    public function unarchive($id)
    {
        $notice = Notice::find($id);
        $notice->archived_at = null;
        $notice->save();

        return redirect()->back();
    }

    public function new()
    {
        $notice = new Notice;
        $notice->user_id = Auth::user()->id;
        $notice->publish_at = now();
        $notice->bg_color = 'bg-blue-lightest';

        // if (isset($_GET['app_type']) && ($_GET['app_type'])) {
        //     $notices->app_type = $_GET['app_type'];
        // }

        $notice->save();

        return redirect()->back();
    }

    public function edit($id)
    {
        $notice = Notice::find($id);

        return view('admin.notices.edit', compact('notice'));
    }

    public function update(Request $request, $id, $close = null)
    {
        $notice = Notice::find($id);
        $notice->app_type = request('app_type');
        $notice->publish_at = request('publish_at');
        $notice->headline = request('headline');
        $notice->body = request('body');
        $notice->bg_color = request('bg_color');
        $notice->approved = (request('approved')) ? true : false;
        if (request('archived_at')) {
            $notice->archived_at = (! $notice->archived_at) ? now() : $notice->archived_at;
        } else {
            $notice->archived_at = null;
        }
        $notice->save();

        if ($close) {
            return redirect('/admin/notices');
        }
        if (! $close) {
            return redirect('/admin/notices/'.$id.'/edit');
        }
    }
}
