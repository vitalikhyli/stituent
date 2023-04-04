<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Tag;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index()
    {
        $tags = Tag::where('team_id', Auth::user()->team->id)
                   ->withCount(['participants'])
                   ->orderBy('name');

        $tags_count = $tags->count();
        $tags = $tags->get();

        // Highlight recently-created tags in the list
        $tags->each(function ($item) {
            if (Carbon::now()->diffInSeconds($item['created_at']) <= 10) {
                return $item['is_new'] = true;
            }
        });

        return view('campaign.tags.index', compact('tags', 'tags_count'));
    }

    public function edit($id)
    {

        $tag = Tag::find($id);
        $this->authorize('basic', $tag);

        return view('campaign.tags.edit', compact('tag'));
    }

    public function show($id)
    {
        $tag = Tag::find($id);
        $this->authorize('basic', $tag);


        return view('campaign.tags.show', compact('tag'));
    }

    public function delete($tag_id)
    {
        $tag = Tag::find($tag_id);
        $this->authorize('basic', $tag);

        $tagged_people = $tag->participants->pluck('id')->toArray();
        foreach ($tagged_people as $participant_id) {
            DB::table('participant_tag')->where('participant_id', $participant_id)
                                        ->where('tag_id', $tag_id)
                                        ->delete();
        }

        $tag->delete();

        return redirect(Auth::user()->team->app_type.'/tags');
    }

    public function update(Request $request, $tag_id, $close = null)
    {
        $tag = Tag::find($tag_id);
        $this->authorize('basic', $tag);

        $name = request('name');
        $in_use = Tag::thisTeam()->where('name', $name)
                                   ->where('id', '!=', $tag->id) //Only check other tags
                                   ->first();

        $errors = [];
        if (! $name) {
            $errors = ['name'=>'Tag name cannot be blank.'];
        }
        if (strlen($name) > 100) {
            $errors = ['name'=>'Tag cannot be more than 100 letters.'];
        }
        if ($in_use) {
            $errors = ['name'=>'Tag already exists'];
        }

        if ($errors) {
            return redirect(Auth::user()->team->app_type.'/tags/'.$tag->id.'/edit')->withErrors($errors)->withInput();
        } else {
            $tag->team_id = Auth::user()->team->id;
            $tag->user_id = Auth::user()->id;
            $tag->name = $name;
            $tag->save();

            if (! $close) {
                return redirect(Auth::user()->team->app_type.'/tags/'.$tag->id.'/edit');
            } else {
                return redirect(Auth::user()->team->app_type.'/tags');
            }
        }
    }

    public function store(Request $request)
    {
        $name = request('name');
        $tag = Tag::thisTeam()->where('name', $name)->first();

        $errors = [];
        if (! $name) {
            $errors = ['name'=>'Tag name cannot be blank.'];
        }
        if (strlen($name) > 100) {
            $errors = ['name'=>'Tag cannot be more than 100 letters.'];
        }
        if ($tag) {
            $errors = ['name'=>'Tag already exists'];
        }

        if ($errors) {
            return redirect(Auth::user()->team->app_type.'/tags')->withErrors($errors)->withInput();
        } else {
            $tag = new Tag;
            $tag->team_id = Auth::user()->team->id;
            $tag->user_id = Auth::user()->id;
            $tag->name = $name;
            $tag->save();

            return redirect(Auth::user()->team->app_type.'/tags');
        }
    }
}
