<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecursiveModel extends Model
{
    public function subModels()
    {
        return get_class($this)::where('parent_id', $this->id)->orderBy('name')->get();
    }

    public function getShortenedNameAttribute()
    {
        $indentation_string_length = $this->depth *= 2; //Each indent is "--" i.e. two chars
        $max_desired = 22;
        $max_length = $max_desired - $indentation_string_length;

        return strlen($this->name) > $max_length ? substr($this->name, 0, $max_length).'...' : $this->name;
    }

    public function getDepth()
    {
        $d = 0;
        $current = get_class($this)::find($this->id);

        while ($current) {
            $current = get_class($this)::find($current->parent_id);
            if ($current) {
                $d++;
            }
        }

        return $d;
    }
}
