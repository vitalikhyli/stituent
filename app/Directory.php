<?php

namespace App;

use App\Directory;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Directory extends RecursiveModel
{
    public function files()
    {
        return $this->hasMany(WorkFile::class); // SOFT DELETES
    }

    public function getOpenAttribute()
    {
        $open_dirs = Auth::user()->getMemory('open_dirs');
        // dd($open_dirs);
        if ($open_dirs) {
            if (in_array($this->id, $open_dirs)) {
                return true;
            } else {
                return false;
            }
        } else {
            Auth::user()->addMemory('open_dirs', []);

            return false;
        }
    }
}
