<?php

namespace App;

use App\Category;
use App\Group;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends RecursiveModel
{
    use SoftDeletes;

    public $table = 'categories';

    protected $primaryKey = 'id';

    protected $casts = [
        'data_template'          => 'array',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    ////////////////////////////////////////////////////////////////////////////

    public function groups()
    {
        return $this->hasMany(Group::class)
                     ->whereNull('groups.deleted_at'); // SOFT DELETES
    }

  
    public function subCategories()
    {
        $categories = self::where('parent_id', $this->id)
                              ->withCount(['groups' => function ($q) {
                                  $q->whereNull('archived_at');
                              }])
                              ->with(['groups' => function ($q) {
                                  $q->whereNull('archived_at');
                                  $q->orderBy('name');
                              }])
                              ->orderBy('name')
                              ->get();

        return $categories;
    }

    // public function groupsWithPositions()
    // {
    //     $groups = Group::where('team_id', Auth::user()->team->id)
    //                   ->where('category_id', $this->id)
    //                   ->get();

    //     if ($this->has_position) {

    //         // foreach($groups as $thegroup) {
    //         //     $groups[] = ['id' => $thegroup->id, 'name' => $thegroup->name." (Oppose)"];
    //         // }

    //     }

    //     return $groups;
    // }
}
