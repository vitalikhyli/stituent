<?php

namespace App\Http\Controllers;

use App\Category;
use App\Group;
use App\GroupFile;
use App\GroupPerson;
use App\Http\Controllers\Controller;
use App\Person;
use App\Traits\ConstituentQueryTrait;
use App\Traits\ExportTrait;
use App\User;
use App\Voter;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    use ConstituentQueryTrait;
    use ExportTrait;

    public function updateAllPeopleCounts()
    {
        $groups = Group::where('team_id', Auth::user()->team->id)->get();
        foreach ($groups as $thegroup) {
            $thegroup->updatePeopleCounts();
        }
    }

    public function mergeAsk($app_type, $group_id)
    {
        // $this->authorize('new', Group::class);
        $group = Group::find($group_id);
        $categories = Auth::user()->team->categories;

        // Only show categories that have groups in them
        $categories = $categories->reject(function ($item) use ($group_id) {
            if (! Group::where('category_id', $item['id'])
                      ->whereNull('archived_at')
                      ->where('id', '<>', $group_id)
                      ->first()) {
                return $item;
            }
        });

        return view('shared-features.groups.merge', compact('group', 'categories'));
    }

    public function mergeConfirm(Request $request, $app_type)
    {
        // dd(request()->input());

        // if (!Auth::user()->permissions->developer) return redirect()->back();

        // if (request('which_primary') == 1) {
        //     $primary          = Group::find(request('group_id_1'));   // 1
        //     $secondary        = Group::find(request('group_id_2'));   // 2
        // }

        // if (request('which_primary') == 2) {
        // $primary          = Group::find(request('group_id_2'));   // 2
        // $secondary        = Group::find(request('group_id_1'));   // 1
        // }

        $primary = Group::find(request('group_id_1'));
        $secondary = Group::find(request('group_id_2'));

        //dd($primary, $secondary);
        $this->authorize('basic', $primary);
        $this->authorize('basic', $secondary);

        $rules = [];
        $rules['combine_notes'] = (request('combine_notes')) ? true : false;
        $rules['primary_only'] = (request('primary_only')) ? true : false;
        $rules['use_both'] = (request('use_both')) ? true : false;
        $rules['archive_secondary'] = (request('archive_secondary')) ? true : false;

        $people_primary = $primary->people;
        $people_secondary = $secondary->people;

        // COMBINE GROUPS

        $combined = $people_primary->merge($people_secondary);

        $combined->map(function ($model) use ($people_primary, $people_secondary, $rules) {

            // CREATE SINGLE ARRAY

            $model_primary = $people_primary->find($model->id);
            if ($model_primary) {
                $model['1_notes'] = $model_primary->pivot->notes;
                $model['1_title'] = $model_primary->pivot->title;
                $model['1_position'] = $model_primary->pivot->position;
            }

            $model_secondary = $people_secondary->find($model->id);
            if ($model_secondary) {
                $model['2_notes'] = $model_secondary->pivot->notes;
                $model['2_title'] = $model_secondary->pivot->title;
                $model['2_position'] = $model_secondary->pivot->position;
            }

            // PRESERVE / DISCARD PIVOT DATA LOGIC

            if ($rules['use_both']) {
                if ($rules['combine_notes']) {
                    $notes = trim($model['1_notes']."\n".$model['2_notes']);
                } else {
                    $notes = ($model['1_notes']) ? $model['1_notes'] : $model['2_notes'];
                }

                $position = ($model['1_position']) ? $model['1_position'] : $model['2_position'];
                $title = ($model['1_title']) ? $model['1_title'] : $model['2_title'];
            } elseif ($rules['primary_only']) {
                $notes = ($model['1_notes']) ? $model['1_notes'] : null;
                $position = ($model['1_position']) ? $model['1_position'] : null;
                $title = ($model['1_title']) ? $model['1_title'] : null;

            // if ($rules['archive_secondary']) {
                //     //
                // }
            } else {
                $notes = null;
                $position = null;
                $title = null;
            }

            $model['pivot_notes'] = $notes;
            $model['pivot_title'] = $title;
            $model['pivot_position'] = $position;

            return $model;
        });

        // NEW GROUP

        if (request(['create_new'][0]) == 1) {
            $combined_group = new Group;
            $combined_group->team_id = Auth::user()->team->id;
            $combined_group->category_id = request('save_category_id');
            $combined_group->name = request('save_name');
            $combined_group->save();
        } else {
            $combined_group = $primary;
            $combined_group->people()->detach();
        }

        // SYNC DATA

        $ids = $combined->pluck('id')->toArray();
        $sync = [];
        foreach ($ids as $id) {
            $sync[$id] = [
                            'team_id' => Auth::user()->team->id,
                            'notes' => $combined->find($id)->pivot_notes,
                            'title' => $combined->find($id)->pivot_title,
                            'position' => $combined->find($id)->pivot_position,
                        ];
        }

        // dd($sync);

        $combined_group->people()->sync($sync);

        $combined_group->updatePeopleCounts();

        // DELETE ORIGINALS

        if (!request('keep_originals')) {
            $secondary->delete();
        }
        if ((request(['create_new'][0]) == 1) && (! request('keep_originals'))) {
            $primary->delete();
        }

        return redirect('/'.Auth::user()->team->app_type.'/groups');
    }

    public function export($app_type, $group_id)
    {
        $group = Group::find($group_id);

        $this->authorize('basic', $group);

        $people = Person::select(DB::raw('people.id as id'),
                                        'full_name',
                                        'primary_email',
                                        'address_city',
                                        'full_address',
                                        'group_person.notes AS group_notes',
                                        'group_person.position AS group_position',
                                        'group_person.created_at AS added_at',
                                        'users.name AS user_name')
                        ->join('group_person', 'people.id', 'person_id')
                        ->leftJoin('users', 'group_person.created_by', 'users.id')
                        ->where('group_id', $group_id)
                        ->get();

        return $this->createCSV($people);
    }

    public function instanceNotes($app_type, $group_id, $person_id)
    {
        $instance = GroupPerson::where('team_id', Auth::user()->team->id)
                               ->where('group_id', $group_id)
                               ->where('person_id', $person_id)
                               ->first();

        if ($instance) {
            return nl2br($instance->notes);
        }
    }

    public function searchPeople($app_type, $group_id, $v = null)
    {
        // dd($v);
        $v = trim($v);
        $mode_all = 1;
        $search_value = $v;

        if ($v == null || strlen($v) <= 1) {
            return null;
        } elseif (strlen($v) > 1) {
            $people = $this->getPeopleFromName($v);
        }

        $group = Group::find($group_id);

        //Remove people already selected
        $attached_people = DB::table('group_person')
                             ->where('group_id', $group_id)
                             ->get()
                             ->pluck('person_id')
                             ->toArray();

        $people = $people->whereNotIn('id', $attached_people);

        return view('shared-features.groups.list-people', compact('people',
                                                        'mode_all',
                                                        'search_value'));
    }

    public function syncPeople(Request $request, $app_type, $group_id)
    {
        $group = Group::find($group_id);

        $this->authorize('basic', $group);

        $people = request('linked');
        $people = collect($people)->unique();

        foreach ($people as $orig_person_id) {
            $instance = GroupPerson::where('group_id', $group_id)
                                   ->where('person_id', $orig_person_id)
                                   ->first();

            if (! $instance) {
                $instance = new GroupPerson;
            }

            $person = findPersonOrImportVoter($orig_person_id, Auth::user()->team->id);

            $instance->person_id = $person->id;
            $instance->group_id = $group_id;
            $instance->team_id = Auth::user()->team->id;
            $instance->position = request('position_'.$orig_person_id);
            $instance->title = request('title_'.$orig_person_id);
            $instance->notes = request('notes_'.$orig_person_id);
            $instance->save();
            // $instance->pivotSignature();
        }

        return redirect(Auth::user()->team->app_type.'/groups/'.$group_id);
    }

    public function linkPerson($app_type, $group_id, $person_id)
    {
        $group = Group::find($group_id);

        $this->authorize('basic', $group);
        // $this->authorize('basic', $person);

        $instance = GroupPerson::where('group_id', $group_id)
                               ->where('person_id', $person_id)
                               ->first();

        if (! $instance) {
            $full_name = PersonOrVoterField('full_name', $person_id, Auth::user()->team->id);

            return view('shared-features.groups.one-linked-person-form', compact('person_id', 'full_name', 'group'));
        }
    }

    public function saveInstance(Request $request, $app_type, $id, $close = null)
    {
        $person = findPersonOrImportVoter($id, Auth::user()->team->id);
        $group = null;
        $this->authorize('basic', $person);

        $add_pivot = [];
        $remove_pivot = [];

        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 6) == 'group_') {
                $pivot_data = [];

                $id = substr($key, 6);
                $group = Group::find($id);
                $this->authorize('basic', $group);

                if (isset($request['position_'.$id])) {
                    $pivot_data['position'] = $request['position_'.$id];
                }

                if (isset($request['title_'.$id])) {
                    $pivot_data['title'] = $request['title_'.$id];
                }

                if (isset($request['notes_'.$id])) {
                    $pivot_data['notes'] = $request['notes_'.$id];
                }

                $pivot_data['team_id'] = Auth::user()->team->id;
                $add_pivot[$id] = $pivot_data;
            }

            if (substr($key, 0, 15) == 'existing_group_') {
                $pivot_data = [];

                $id = substr($key, 15);
                $group = Group::find($id);
                $this->authorize('basic', $group);

                if (! isset($request['keep_group_'.$id])) {
                    $remove_pivot[] = $id;
                } else {
                    if (isset($request['position_'.$id])) {
                        $pivot_data['position'] = $request['position_'.$id];
                    }

                    if (isset($request['title_'.$id])) {
                        $pivot_data['title'] = $request['title_'.$id];
                    }

                    if (isset($request['notes_'.$id])) {
                        $pivot_data['notes'] = $request['notes_'.$id];
                    }

                    $pivot_data['team_id'] = Auth::user()->team->id;
                    $add_pivot[$id] = $pivot_data;
                }
            }
        }

        

        if ($add_pivot) {

            // $signatures_to_do = [];
            // foreach ($add_pivot as $group_id => $pivot_data) {
            //     $instance = GroupPerson::where('person_id', $person->id)
            //                            ->where('group_id', $group_id)
            //                            ->first();
            //     if (!$instance) $signatures_to_do[$person->id] = $group_id;
            // }

            $person->groups()->syncWithoutDetaching($add_pivot);

            // foreach($signatures_to_do as $person_id => $group_id) {
            //     $instance = GroupPerson::where('person_id', $person->id)
            //                            ->where('group_id', $group_id)
            //                            ->first();
            //     if ($instance) $instance->pivotSignature('create');
            // }
        }

        //People Counts because sync, etc doesn't always trigger model observer
        foreach ($add_pivot as $group_id => $pivot_data) {
            $group = Group::withTrashed()->find($group_id);
            if ($group) {
                $group->updatePeopleCounts();
            }
        }

        // Detaching does not use SoftDeletes, so do it this way:
        foreach ($remove_pivot as $group_id) {
            GroupPerson::where('group_id', $group_id)
                       ->where('person_id', $person->id)
                       ->delete();

            //People Counts because sync, etc doesn't always trigger model observer
            $group = Group::withTrashed()->find($group_id);
            if ($group) {
                $group->updatePeopleCounts();
            }
        }

        if ($close) {
            return redirect(Auth::user()->team->app_type.'/constituents/'.$person->id);
        } elseif ($group) {
            return redirect(Auth::user()->team->app_type.'/constituents/'.$person->id
                .'/category/'.$group->cat->id.'/new');
        } else {
            return redirect(Auth::user()->team->app_type.'/constituents/'.$person->id);
        }
    }

    public function newInstance($app_type, $id, $category_id)
    {
        if (IDisPerson($id)) {
            $person = Person::find($id);

            $this->authorize('basic', $person);

            $category = Category::find($category_id);

            $existing_ids = GroupPerson::join('groups', 'group_person.group_id', 'groups.id')
                                       ->join('categories', 'groups.category_id', 'categories.id')
                                       ->where('groups.category_id', $category_id)
                                       ->where('group_person.person_id', $id)
                                       ->whereNull('groups.deleted_at')
                                       ->orderBy('groups.name')
                                       ->pluck('group_id')
                                       ->toArray();

            $existing_ids_ordered = implode(',', $existing_ids);

            $groups_available = Group::where('category_id', $category_id)
                                     ->where('team_id', Auth::user()->team->id)
                                     ->whereNotIn('id', $existing_ids)
                                     ->whereNull('archived_at')
                                     ->orderBy('name')
                                     ->get();

            $group_pivots_existing = GroupPerson::where('team_id', Auth::user()->team->id)
                                          ->whereIn('group_id', $existing_ids)
                                          ->where('person_id', $person->id);
            if ($existing_ids_ordered) {
                $group_pivots_existing = $group_pivots_existing->orderByRaw(DB::raw("FIELD(group_id, $existing_ids_ordered)"));
            }
            $group_pivots_existing = $group_pivots_existing->get();
            $group_pivots_existing_archived = $group_pivots_existing;

            // SEPARATE NON-ARCHIVED
            $group_pivots_existing = $group_pivots_existing->filter(function ($item, $key) {
                $is_archived = Group::find($item['group_id'])->archived_at;

                return ! $is_archived;
            });

            //SEPARATE ARCHIVED (SO ARCHIVED CAN STILL BE EDITED)
            $group_pivots_existing_archived = $group_pivots_existing_archived->filter(function ($item, $key) {
                $is_archived = Group::find($item['group_id'])->archived_at;

                return $is_archived;
            });

            // Sort all groups
            $groups_available = $groups_available->sortBy('name');
            $group_pivots_existing = $group_pivots_existing->sortBy('name');
            $group_pivots_existing_archived = $group_pivots_existing_archived->sortBy('name');

            return view('shared-features.groups.instance_new', compact('person', 'groups_available', 'group_pivots_existing', 'group_pivots_existing_archived', 'category'));
        }

        if (IDisVoter($id)) {
            $person = Voter::find($id);

            $category = Category::find($category_id);

            $groups_available = Group::where('category_id', $category_id)
                                   ->where('team_id', Auth::user()->team->id)
                                   ->whereNull('archived_at')
                                   ->get();

            $mode_external = true;

            // Sort all groups
            $groups_available = $groups_available->sortBy('name');

            return view('shared-features.groups.instance_new', compact('person', 'mode_external', 'groups_available', 'category'));
        }
    }

    public function deleteInstance($app_type, $id)
    {
        $instance = GroupPerson::find($id);
        $person = Person::find($instance->person_id);

        $this->authorize('basic', $person);

        $instance->delete();

        return redirect(Auth::user()->team->app_type.'/constituents/'.$person->id);
    }

    public function showInstance($app_type, $id)
    {
        $instance = GroupPerson::find($id);
        $person = Person::find($instance->person_id);
        $group = Group::find($instance->group_id);

        $this->authorize('basic', $person);
        $this->authorize('basic', $group);

        return view('shared-features.groups.instance', compact('person', 'instance', 'group'));
    }

    public function updateInstance(Request $request, $app_type, $id, $close = null)
    {
        $instance = GroupPerson::find($id);
        $person = Person::find($instance->person_id);

        $this->authorize('basic', $person);

        $instance->position = (request('position')) ? request('position') : null;
        $instance->title = (request('title')) ? request('title') : null;
        $instance->notes = (request('notes')) ? request('notes') : null;

        $instance->save();
        // $instance->pivotSignature();

        if ($close) {
            return redirect(Auth::user()->team->app_type.'/constituents/'.$person->id);
        } else {
            return redirect(Auth::user()->team->app_type.'/groups/instance/'.$instance->id);
        }
    }

    //////////////////////////[  BULK ADD GROUPS AJAX ]//////////////////////////

    public function bulkGroupsAdd($app_type, $group_id, $person_id, $team_id)
    {
        $person = findPersonOrImportVoter($person_id, $team_id);
        $group = Group::find($group_id);

        $this->authorize('basic', $person);
        $this->authorize('basic', $group);

        // Check if it has been previously deleted
        $pivot = GroupPerson::where('group_id', $group->id)
                            ->where('person_id', $person->id)
                            ->first();


        $pivot = new GroupPerson;

        $pivot->person_id = $person->id;
        $pivot->group_id = $group_id;
        $pivot->team_id = $team_id;

        $pivot->save();
        // $instance->pivotSignature();
        

        return '<button data-action="remove" data-group_id="'.$group->id.'" data-person_id="'.$person->id.'" class="toggle-group bg-blue rounded-lg text-white px-2 py-1 text-sm">'.substr($group->name, 0, 15).'</button>';
    }

    public function bulkGroupsRemove($app_type, $group_id, $person_id, $team_id)
    {
        $person = Person::find($person_id);
        $group = Group::find($group_id);

        $this->authorize('basic', $person);
        $this->authorize('basic', $group);

        GroupPerson::where('group_id', $group->id)
                   ->where('person_id', $person->id)
                   ->first()
                   ->delete();

        return '<button data-action="add" data-group_id="'.$group->id.'" data-person_id="'.$person->id.'" class="toggle-group bg-grey rounded-lg text-white px-2 py-1 text-sm">'.substr($group->name, 0, 15).'</button>';
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function searchInstance(Request $request, $app_type, $id)
    {
        $v = request('search_v');

        return $this->show($app_type, $id, $support = null, $search_v = $v)
                    ->with('search_v', $search_v);
    }

    public function showPosition($app_type, $id, $support)
    {
        return $this->show($app_type, $id, $support);
    }

    public function saveNote(Request $request, $app_type, $group_id)
    {
        $group = Group::find($group_id);
        $this->authorize('basic', $group);

        $person = Person::find(request('modal_person_id'));
        $this->authorize('basic', $person);

        $pivot = GroupPerson::where('person_id', $person->id)
                            ->where('group_id', $group->id)
                            ->first();

        if ($pivot) {
            $pivot->notes = request('modal_note_content');
            $pivot->save();
            // $instance->pivotSignature();
        }

        return redirect('/'.Auth::user()->team->app_type.'/groups/'.$group->id);
    }

    public function show($app_type, $id, $support = null, $search_v = null)
    {
        $group = Group::find($id);

        $this->authorize('basic', $group);

        return view('shared-features.groups.show-livewire', compact('group'));

        // $people = Person::select(DB::raw('people.id as id'),
        //                                 'full_name',
        //                                 'address_city',
        //                                 'full_address',
        //                                 'primary_email',
        //                                 'group_person.notes AS group_notes',
        //                                 'group_person.position AS group_position',
        //                                 'group_person.title AS group_title',
        //                                 'group_person.created_at as user_when',
        //                                 'group_person.created_by as user_who'
        //                                 )
        //                 ->join('group_person', 'people.id', 'person_id')
        //                 ->where('group_id', $id); // Soft Deletes

        // if ($support) {
        //     $people = $people->where('group_person.position', $support);
        // }

        // if ($search_v) {
        //     $people = $people->where(function ($w) use ($search_v) {
        //         $w->orWhere('group_person.notes', 'like', '%'.$search_v.'%');
        //         $w->orWhere('full_name', 'like', '%'.$search_v.'%');
        //     });
        // }

        // $people->orderBy('last_name');

        // $people_total = $people->count();

        // $people = $people->get();

        // // Not ideal, but put user name in this way:
        // $people = $people->each(function ($item) {
        //     if ($item['user_who']) {
        //         $item['user_who'] = User::find($item['user_who'])->short_name;
        //     }
        // });

        // return view('shared-features.groups.show', compact('group',
        //                                                  'people',
        //                                                  'people_total'
        //                                                 ));
    }

    public function update(Request $request, $app_type, $id, $close = null)
    {
        $group = Group::find($id);

        $this->authorize('basic', $group);

        $group->name = request('name');
        $group->notes = request('notes');
        $group->category_id = request('category_id');

        $group->save();

        $group->updatePeopleCounts();

        if ($close) {
            return redirect(Auth::user()->team->app_type.'/groups');
        } else {
            return redirect(Auth::user()->team->app_type.'/groups/'.$id.'/edit');
        }
    }

    public function edit($app_type, $id)
    {
        $group = Group::find($id);

        $this->authorize('basic', $group);

        $people = Person::select(DB::raw('people.id as id'),
                                        'full_name',
                                        'address_city',
                                        'full_address')
                        ->join('group_person', 'people.id', 'person_id')
                        ->where('group_id', $id)
                        ->orderBy('last_name');

        $people_total = $people->count();

        $categories = Category::where('team_id', Auth::user()->team->id)
                                ->orderBy('name')
                                ->get();

        return view('shared-features.groups.edit', compact('group', 'people_total', 'categories'));
    }

    public function index($app_type)
    {
        return $this->getIndex();
    }

    public function indexArchived($app_type)
    {
        return $this->getIndex($archived = true);
    }

    public function getIndex($archived = null)
    {

        // If people count process has never run
        if (Group::where('team_id', Auth::user()->team->id)->sum('people_count') == 0) {
            $this->updateAllPeopleCounts();
        }

        if ($archived) {

            //ONLY SHOW CATEGORIES WITH GROUPS
            $categories = Category::whereHas('groups', function ($q) {
                $q->whereNotNull('archived_at');
            })
                                  ->withCount(['groups' => function ($q) {
                                      $q->whereNotNull('archived_at');
                                  }])
                                  ->with(['groups' => function ($q) {
                                      $q->whereNotNull('archived_at');
                                      $q->orderBy('name');
                                  }]);
        } else {

            // SHOW ALL CATEGORIES EVEN IF THERE ARE NO GROUPS YET
            $categories = Category::withCount(['groups' => function ($q) {
                $q->whereNull('archived_at');
            }])
                                  ->with(['groups' => function ($q) {
                                      $q->whereNull('archived_at');
                                      $q->orderBy('name');
                                  }]);
        }

        $categories = $categories->where('team_id', Auth::user()->team->id)
                                 ->orderBy('name')
                                 ->get();

        $current_total = Group::where('team_id', Auth::user()->team->id)
                               ->whereNull('archived_at')
                               ->count();

        $archived_total = Group::where('team_id', Auth::user()->team->id)
                               ->whereNotNull('archived_at')
                               ->count();

        return view('shared-features.groups.index', compact('categories', 'current_total', 'archived_total'));
    }

    public function new(Request $request, $app_type)
    {
        //$this->authorize('new', Group::class);

        $group = new Group;
        $group->name = request('name');
        $group->category_id = request('category_id');
        $group->team_id = Auth::user()->team->id;
        $group->save();

        return redirect(Auth::user()->team->app_type.'/groups');
    }

    public function delete($app_type, $id)
    {
        $group = Group::find($id);

        //$this->authorize('delete', $group);

        $group->delete();

        return redirect(Auth::user()->team->app_type.'/groups');
    }

    public function archive($app_type, $id, $reverse = null)
    {
        $group = Group::find($id);
        //$this->authorize('archive', $group);
        if (! $reverse) {
            $group->archived_at = date('Y-m-d H:i:s');
        }
        if ($reverse) {
            $group->archived_at = null;
        }
        $group->save();

        return redirect()->back();
    }

    public function convertToLegislation($app_type, $id, $reverse = null)
    {
        $group = Group::find($id);
        //$this->authorize('archive', $group);
        $legcat = Auth::user()->team->categories()->where('name', 'Legislation')->first();
        if ($legcat) {
            $group->category_id = $legcat->id;
            $group->save();
        }

        return redirect()->back();
    }

    public function groupRemove($app_type, $person_id, $pivot_id)
    {
        $instance = GroupPerson::find($pivot_id);
        $group = Group::find($instance->group_id);
        $person = Person::find($person_id);

        $this->authorize('basic', $group);
        $this->authorize('basic', $person);

        $instance->delete();

        return back();
    }
}
