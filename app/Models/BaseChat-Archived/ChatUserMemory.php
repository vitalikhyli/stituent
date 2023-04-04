<?php

// namespace App\Models\BaseChat;

// use Illuminate\Database\Eloquent\Model;

// class ChatUserMemory extends Model
// {
//     protected $table = 'chat_user_memory';
//     protected $casts = ['unread_messages' => 'array', 'recent_rooms' => 'array'];

//     public function addRecentRoom($room)
//     {
//     	if (!$room) {
//     		return;
//     	}
//     	$recent_rooms = collect($this->recent_rooms);
//     	$recent_rooms[] = $room->id;
//     	if (count($recent_rooms) > 5) {
//     		// keeps it at just last 5 rooms
//     		$recent_rooms = $recent_rooms->slice(1);
//     	}
//     	$this->recent_rooms = $recent_rooms;
//     	$this->current_room_id = $room->id;
//     	$this->save();
//     }
// }
