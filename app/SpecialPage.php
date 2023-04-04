<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class SpecialPage extends Model
{
    use SoftDeletes;

    protected $casts = ['stars' => 'array'];

    public function getStarCountAttribute()
    {
    	if ($this->stars) {
    		return count($this->stars);
    	}
    	return 0;
    }
    public function addStar()
    {
    	$stars = $this->stars;
    	if ($stars) {
    		$stars[Auth::user()->id] = "";
    	} else {
    		$stars = [];
    		$stars[Auth::user()->id] = "";
    	}
    	$this->stars = $stars;
    	$this->save();
    }
    public function removeStar()
    {
    	$stars = $this->stars;
    	if ($stars) {
    		unset($stars[Auth::user()->id]);
    	} else {
    		$stars = [];
    	}
    	$this->stars = $stars;
    	$this->save();
    }
    public function getStarredAttribute()
    {
    	if (!$this->stars) {
    		return false;
    	}
    	foreach ($this->stars as $user_id => $comment) {
    		if (Auth::user()->id == $user_id) {
    			return true;
    		}
    	}
    	return false;
    }
}
