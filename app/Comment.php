<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\User;
use Auth;


class Comment extends Model
{
    // use HasFactory;

    use SoftDeletes;

    protected $casts = ['up_users' => 'array',
						'down_users' => 'array'];

	public function getClosedByNameAttribute()
	{
		$user = User::find($this->closed_by);
		return ($user) ? $user->name : null;
	}

	public function getUpVoteMeAttribute()
	{
		return $this->didUserVote('up');
	}

	public function getDownVoteMeAttribute()
	{
		return $this->didUserVote('down');
	}

	public function didUserVote($dir)
	{
		$current_dir_users = collect($this->{ $dir.'_users' })->toArray();
		if (in_array(Auth::user()->id, $current_dir_users)) {
			return true;
		} else {
			return false;
		}
	}
}
