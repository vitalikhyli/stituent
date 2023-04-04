<?php

namespace App\Http\Controllers\University;

use App\Entity;
use App\Http\Controllers\Controller;
use App\Person;
use App\Relationship;
use Auth;
use Illuminate\Http\Request;
use Validator;

class RelationshipsController extends Controller
{
    public function searchEntities($v = null)
    {
        if (! $v) {
            exit;
        }

        $entities = Entity::where('name', 'like', '%'.$v.'%')
                          ->where('team_id', Auth::user()->team->id)
                          ->get();

        return view('u.relationships.list-entities', compact('entities'));
    }

    public function searchPeople($v = null)
    {
        if (! $v) {
            exit;
        }

        $people = Person::where('full_name', 'like', '%'.$v.'%')
                        ->where('team_id', Auth::user()->team->id)
                        ->orderBy('last_name')
                        ->orderBy('first_name')
                        ->get();

        return view('u.relationships.list-people', compact('people'));
    }

    public function searchKinds($type, $v = null)
    {
        if (! $v) {
            exit;
        }

        $subject_type = substr($type, 0, 1);
        $object_type = substr($type, 2, 1);

        $kinds = Relationship::select('kind')
                             ->where('kind', 'like', '%'.$v.'%')
                             ->where('team_id', Auth::user()->team->id)
                             ->where('subject_type', $subject_type)
                             ->where('object_type', $object_type)
                             ->groupBy('kind')
                             ->orderBy('kind')
                             ->get();

        return view('u.relationships.list-kinds', compact('kinds'));
    }

    public function delete($id)
    {
        $relationship = Relationship::find($id);

        $this->authorize('basic', $relationship);

        $subject_type = $relationship->subject_type;

        $relationship->delete();

        if ($subject_type == 'p') {
            return redirect('u/constituents/'.$relationship->subject_id);
        }

        if ($subject_type == 'e') {
            return redirect('u/entities/'.$relationship->subject_id);
        }
    }

    public function update(Request $request, $id, $close = null)
    {
        $validator = Validator::make(request()->toArray(),
                    [
                        'object_id' => ['required'],
                        'kind' => ['required'],
                    ],
                    [
                       'object_id.required'   => 'Please enter an object of the relationship.',
                       'kind.required'        => 'Please enter a kind.',
                    ]
                    );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $relationship = Relationship::find($id);

        $this->authorize('basic', $relationship);

        $relationship->object_id = request('object_id');
        $relationship->kind = request('kind');

        $relationship->save();

        if ($close) {
            if ($relationship->subject_type == 'p') {
                return redirect('u/constituents/'.$relationship->subject_id);
            }
            if ($relationship->subject_type == 'e') {
                return redirect('u/entities/'.$relationship->subject_id);
            }
        } else {
            return redirect('u/relationships/'.$relationship->id.'/edit');
        }
    }

    public function save(Request $request, $id, $type)
    {
        $subject_type = substr($type, 0, 1);
        $object_type = substr($type, 2, 1);

        $validator = Validator::make(request()->toArray(),
                    [
                        'object_id' => ['required'],
                        'kind' => ['required'],
                    ],
                    [
                       'object_id.required'   => 'Please enter an object of the relationship.',
                       'kind.required'        => 'Please enter a kind.',
                    ]
                    );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $relationship = new Relationship;

        $relationship->subject_id = $id;
        $relationship->subject_type = $subject_type;

        $relationship->object_id = request('object_id');
        $relationship->object_type = $object_type;

        $relationship->kind = request('kind');

        $relationship->team_id = Auth::user()->team->id;

        $relationship->save();

        if ($subject_type == 'p') {
            return redirect('u/constituents/'.$id);
        }
        if ($subject_type == 'e') {
            return redirect('u/entities/'.$id);
        }
    }

    public function edit($id)
    {
        $relationship = Relationship::find($id);

        $this->authorize('basic', $relationship);

        $type = $relationship->subject_type.'2'.$relationship->object_type;
        $mode = 'update';

        if ($type == 'p2e') {
            $person = Person::find($relationship->subject_id);
            $object = Entity::find($relationship->object_id);

            return view('u.relationships.edit-p2e', compact('person', 'mode', 'relationship', 'object'));
        }

        if ($type == 'p2p') {
            $person = Person::find($relationship->subject_id);
            $object = Person::find($relationship->object_id);

            return view('u.relationships.edit-p2p', compact('person', 'mode', 'relationship', 'object'));
        }

        if ($type == 'e2p') {
            $entity = Entity::find($relationship->subject_id);
            $object = Person::find($relationship->object_id);

            return view('u.relationships.edit-e2p', compact('entity', 'mode', 'relationship', 'object'));
        }

        if ($type == 'e2e') {
            $entity = Entity::find($relationship->subject_id);
            $object = Entity::find($relationship->object_id);

            return view('u.relationships.edit-e2e', compact('entity', 'mode', 'relationship', 'object'));
        }
    }

    public function newPersonToPerson($id)
    {
        $person = Person::find($id);

        $this->authorize('basic', $person);

        $mode = 'save';

        return view('u.relationships.edit-p2p', compact('person', 'mode'));
    }

    public function newPersonToEntity($id)
    {
        $person = Person::find($id);

        $this->authorize('basic', $person);

        $mode = 'save';

        return view('u.relationships.edit-p2e', compact('person', 'mode'));
    }

    public function newEntityToPerson($id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        $mode = 'save';

        return view('u.relationships.edit-e2p', compact('entity', 'mode'));
    }

    public function newEntityToEntity($id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        $mode = 'save';

        return view('u.relationships.edit-e2e', compact('entity', 'mode'));
    }
}
