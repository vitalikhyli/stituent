<?php
/* EDITING OUT UNNECESSSARY CONTROLLER

namespace App\Http\Controllers\University;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Group;
use App\GroupPerson;
use App\Category;
use App\Person;

use Auth;

use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{

    public function saveInstance(Request $request, $id)
    {
        $person         = Person::find($id);
        $group          = Group::find(request('group'));

        $this->authorize('basic', $person);
        $this->authorize('basic', $group);

        $category = Category::find($group->category_id);

        $person->groups()->attach(request('group'), ['team_id' => Auth::user()->team->id,
                                                     'data' => json_encode($category->data_template)]);

        $instance = GroupPerson::where('person_id', $id)
                               ->where('group_id',$group->id)
                               ->first();

        return redirect('u/groups/instance/'.$instance->id);

    }

    public function newInstance($id, $category_id)
    {
        $person         = Person::find($id);

        $this->authorize('basic', $person);

        $existing_groups = GroupPerson::where('person_id',$id)->pluck('group_id')->toArray();

        $groups         = Group::where('category_id',$category_id)
                               ->where('team_id',Auth::user()->team->id)
                               ->whereNotIn('id',$existing_groups)
                               ->get();

        $category       = Category::find($category_id);

        return view('u.groups.instance_new', compact('person','groups','category'));
    }


    public function deleteInstance($id)
    {
        $instance       = GroupPerson::find($id);
        $person         = Person::find($instance->person_id);

        $this->authorize('basic', $person);

        $instance->delete();

        return redirect('u/constituents/'.$person->id);
    }


    public function showInstance($id)
    {
        $instance       = GroupPerson::find($id);
        $data           = json_decode($instance->data, true);
        $person         = Person::find($instance->person_id);
        $group          = Group::find($instance->group_id);

        $this->authorize('basic', $person);
        $this->authorize('basic', $group);

        return view('u.groups.instance', compact('person','instance','data', 'group'));
    }

    public function updateInstance(Request $request, $id, $close = null)
    {
        $instance       = GroupPerson::find($id);
        $data           = json_decode($instance->data);
        $person         = Person::find($instance->person_id);

        $this->authorize('basic', $person);

        $new_data = [];
        if (request('position')) {
            $new_data = array_merge($new_data,['position' => request('position')]);
        } else {
            $new_data = array_merge($new_data,['position' => null]);
        }
        if (request('notes')) {
            $new_data = array_merge($new_data,['notes' => request('notes')]);
        } else {
            $new_data = array_merge($new_data,['notes' => null]);
        }

        $instance->data = json_encode($new_data);
        $instance->save();

        if ($close) {
            return redirect('u/constituents/'.$person->id);
        } else {
            return redirect('u/groups/instance/'.$instance->id);
        }
    }

    //////////////////////////[  BULK ADD GROUPS AJAX ]//////////////////////////

    public function bulkGroupsAdd($group_id, $person_id, $team_id)
    {
        if (Auth::user()->team->id != $team_id) { return "Error"; } //Security Check

        $person = findPersonOrImportVoter($person_id, $team_id);
        $group = Group::find($group_id);

        $this->authorize('basic', $person);
        $this->authorize('basic', $group);

        $pivot = new GroupPerson;

        $pivot->person_id = $person->id;
        $pivot->group_id = $group_id;
        $pivot->team_id = $team_id;


        $pivot->data = json_encode(Category::find($group->category_id)->data_template);



        $pivot->save();

        return '<button data-action="remove" data-group_id="'.$group->id.'" data-person_id="'.$person->id.'" class="toggle-group bg-blue rounded-lg text-white px-2 py-1 text-sm">'.substr($group->name,0,15).'</button>';
    }

    public function bulkGroupsRemove($group_id, $person_id, $team_id)
    {
        if (Auth::user()->team->id != $team_id) { return "Error"; } //Security Check

        // $person = findPersonOrImportVoter($person_id, $team_id);
        $person = Person::find($person_id);
        $group = Group::find($group_id);

        $this->authorize('basic', $person);
        $this->authorize('basic', $group);

        GroupPerson::where('group_id', $group->id)
                   ->where('person_id',$person->id)
                   ->first()
                   ->delete();

        return '<button data-action="add" data-group_id="'.$group->id.'" data-person_id="'.$person->id.'" class="toggle-group bg-grey rounded-lg text-white px-2 py-1 text-sm">'.substr($group->name,0,15).'</button>';
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function showPosition($id, $support)
    {
        return $this->show($id, $support);
    }

    public function show($id, $support = null)
    {
        $group = Group::find($id);

        $this->authorize('basic', $group);

        $people = Person::select(DB::raw('people.id as id'),
                                        'full_name',
                                        'address_city',
                                        'full_address',
                                        'group_person.data')
                        ->join('group_person','people.id','person_id')
                        ->where('group_id',$id);

        if ($support) {
            $people = $people->whereRaw('json_contains(group_person.data, \'{"position":"'.$support.'"}\') = 1');
        }

        $people = $people->orderBy('last_name');

        $people_total = $people->count();

        $people = $people->get();

        if (array_key_exists('position', $group->cat->data_template)) {
            $show_positions = true;
        } else {
            $show_positions = false;
        }

        return view('u.groups.show', compact('group',
                                                         'people',
                                                         'people_total',
                                                         'show_positions'));
    }

    public function update(Request $request, $id, $close = null)
    {
        $group = Group::find($id);

        $this->authorize('basic', $group);

        $group->name = request('name');

        $group->save();

        if ($close) {
            return redirect('u/groups');
        } else {
            return redirect('u/groups/'.$id.'/edit');
        }
    }

    public function edit($id)
    {
        $group = Group::find($id);

        $this->authorize('basic', $group);

        $people = Person::select(DB::raw('people.id as id'),
                                        'full_name',
                                        'address_city',
                                        'full_address')
                        ->join('group_person','people.id','person_id')
                        ->where('group_id',$id)
                        ->orderBy('last_name');

        $people_total = $people->count();

        return view('u.groups.edit', compact('group','people_total'));
    }


    public function index()
    {
        $categories = Category::where('preset','u')
                              ->orWhere(function ($q) {
                                $q->where('team_id', Auth::user()->team->id);
                              })
                              ->orderBy('name')
                              ->get();

        $numgroups      = Group::where('team_id',Auth::user()->team->id)->count();

        return view('u.groups.index', compact('categories',
                                                          'numgroups'));
    }

    public function new(Request $request)
    {
        $this->authorize('new', Group::class);

        $group = new Group;
        $group->name        = request('name');
        $group->category_id = request('category_id');
        $group->team_id     = Auth::user()->team->id;
        $group->save();

        return redirect('u/groups');
    }

    public function delete($id)
    {
        $group = Group::find($id);

        $this->authorize('basic', $group);

        $group->delete();

        return redirect('u/groups');
    }

    public function groupRemove($person_id, $pivot_id)
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
