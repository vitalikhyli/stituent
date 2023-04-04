<?php

// namespace App\Models\BaseChat;

// use Illuminate\Database\Eloquent\Model;
// use App\User;
// use App\Team;

// class ChatRoom extends Model
// {
//     public function getOtherPersonNameAttribute()
//     {
//     	if ($this->direct) {

//     	}
//     	return null;
//     }
//     public function messages()
//     {
//     	return $this->hasMany(ChatMessage::class, 'room_id')->latest();
//     }
//     public function access()
//     {
//     	return $this->hasMany(ChatRoomAccess::class, 'room_id');
//     }
//     public function getMemberIdsAttribute()
//     {
//         //dd($this->access()->teamType()->toSql());
//         $teamids = $this->access()->teamType()->pluck('team_id');
//         $userids = User::whereIn('current_team_id', $teamids)->pluck('id');
//         $individualuserids = $this->access()->userType()->pluck('user_id');
//         $userids = $userids->merge($individualuserids);
//         $userids = $userids->unique();
//         //dd($individualuserids, $userids);
//         return $userids;
//     }
//     public function getTeamIdsAttribute()
//     {
//         return $this->access()->teamType()->pluck('team_id');
//     }
//     public function members()
//     {
//     	return User::whereIn('id', $this->member_ids);
//     }
//     public function teams()
//     {
//         return Team::whereIn('id', $this->team_ids);
//     }
//     public function getMemberCountAttribute()
//     {
//         return $this->members()->count();
//     }
//     public function users()
//     {
//         // DIFFERENT THAN MEMBERS, which is everyone
//         $userids = $this->access()->userType()->pluck('user_id');
//         return User::whereIn('id', $userids);
//     }
//     public function getMembersStrAttribute()
//     {
//     	$str = "";
//     	foreach ($this->members()->get() as $member) {
// 	    	$str .= "<span class='room-member cursor-pointer hover:text-white'>".$member->name."</span>, ";
// 	    }
// 	    return $str;
//     }
//     public function save(array $options = [])
//     {
//     	$members = 0;
//     	if ($this->direct) {
//     		$members = 2;
//     	} else {
//     		$memberids = $this->member_ids;
//     		$members = $memberids->count();
//     	}
//     	$this->member_count = $members;
//     	return parent::save();
//     }
// }
