<?php

namespace App\Http\Controllers;

use App\Category;
use App\Group;
use App\GroupPerson;
use Auth;
use Illuminate\Http\Request;
use Validator;

class CategoriesController extends Controller
{
    public function groupsRadios($app_type, $cat_id, $merge_order, $group_id)
    {
        $category = Category::find($cat_id);
        $groups = $category->groups();
        $groups = $groups->whereNull('archived_at');
        $groups = $groups->where('id', '<>', $group_id);

        $groups = $groups->get();

        return view('shared-features.groups.select-radios', compact('groups', 'merge_order', 'group_id'));
    }

    public function groupsCheckboxes($app_type, $cat_id)
    {
        $category = Category::find($cat_id);
        $groups = $category->groups()->whereNull('archived_at')->get();

        return view('shared-features.groups.select-checkboxes', compact('groups'));
    }

    public function update(Request $request, $app_type, $id, $close = null)
    {
        // dd($request);
        $category = Category::find($id);

        if (! $category->can_edit) {
            return redirect('/'.$app_type.'/groups/');
        }
        // $this->authorize('basic', $group);

        $has_title      = (request('title_or_position') == 'title') ? true : false;
        $has_position   = (request('title_or_position') == 'position') ? true : false;
        $has_notes      = (request('has_notes')) ? true : false;

        $category->name = request('name');
        $category->has_title = $has_title;
        $category->has_position = $has_position;
        $category->has_notes = $has_notes;

        $category->save();

        if (! $close) {
            return redirect('/'.$app_type.'/categories/'.$id.'/edit');
        } else {
            return redirect('/'.$app_type.'/groups/');
        }
    }

    public function edit($app_type, $id)
    {
        $category = Category::find($id);

        if (! $category->can_edit) {
            return redirect('/'.$app_type.'/groups/');
        }
        // $this->authorize('basic', $group);

        return view('shared-features.groups.edit-category', compact('category'));
    }

    public function archive($app_type, $id)
    {
        $category = Category::find($id);

        foreach ($category->groups as $group) {
            $group->archived_at = date('Y-m-d H:i:s');
            $group->save();
        }
        // $this->authorize('basic', $group);

        return redirect()->back();
    }

    public function delete($app_type, $id)
    {
        $category = Category::find($id);

        // Cannot delete top-level categories (e.g. "Legislation")
        if (! $category->can_edit) {
            return redirect('/'.$app_type.'/groups/');
        }

        // Recursively build a list of all sub-categories by ID
        $affected_cats = [];
        $cats_to_check = [];
        $cats_to_check[$id] = true;

        while (count($cats_to_check) > 0) {

            // Add to list of affected categories
            $check_id = array_key_first($cats_to_check);
            $affected_cats[] = $check_id;

            // Get subcategories of this one
            $subs = Category::where('parent_id', $check_id)->get();

            // Add subcategories to list to check
            foreach ($subs as $sub) {
                $cats_to_check[$sub->id] = true;
            }

            // Done checking this one
            unset($cats_to_check[$check_id]);
        }

        // Loop through categories
        foreach ($affected_cats as $cat_id) {
            $cat = Category::find($cat_id);

            // Authorize
            $this->authorize('basic', $cat);

            // Delete Groups and GroupPerson instances in this Category
            foreach ($cat->groups as $thegroup) {
                // Delete instances of group_person
                // GroupPerson::where('group_id', $thegroup->id)->delete();

                // Delete Group
                // $thegroup->delete();
            }

            // Delete Category
            $cat->delete();
        }

        return redirect('/'.$app_type.'/groups');
    }

    public function save(Request $request)
    {
        $validate_array = $request->all();
        $validator = Validator::make($validate_array, [
            'new_category_name' => ['required', 'max:100'],
        ]);

        if (request('new_category_name') == null) {
            $error = true;
        }

        // DEFAULTS
        $has_position = false;
        $has_title = false;
        $has_notes = false;
        $parent_id = null;

        if (request('parent_id')) {
            // INHERIT TEMPLATE VARIABLES
            $parent_cat = Category::find(request('parent_id'));

            if ($parent_cat->depth > 4) {
                // ARBITRARY LIMIT ON NUMBER OF SUBCATEGORIES
                $validator->getMessageBag()->add('Subcategories', 'Too many subcategories');
            }
            $has_position = $parent_cat->has_position;
            $has_title = $parent_cat->has_title;
            $has_notes = $parent_cat->has_notes;
            $parent_id = $parent_cat->id;
        }

        // NEW CATEGORY
        if ($validator->errors()->count() <= 0) {
            $cat = new Category();
            $cat->name = request('new_category_name');
            $cat->team_id = Auth::user()->team->id;
            $cat->can_edit = true;		// NOT A PRESET SO CAN CHANGE NAME
            $cat->has_position = $has_position;
            $cat->has_title = $has_title;
            $cat->has_notes = $has_notes;
            $cat->parent_id = $parent_id;
            $cat->save();

            $cat->depth = $cat->getDepth();
            $cat->save();
        }

        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        } else {
            return redirect()->back();
        }
    }
}
