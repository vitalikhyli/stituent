<?php

namespace App\Http\Controllers;

use App\Models\BaseChat\ChatMessage;
use App\Models\BaseChat\ChatRoom;
use App\Models\BaseChat\ChatRoomAccess;
use App\Models\BaseChat\ChatUserMemory;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BaseChatController extends Controller
{
    public function addRoom()
    {
        $current_room = new ChatRoom;
        $current_room->team_id = Auth::user()->team_id;
        $current_room = $this->updateRoomByRequest($current_room);
        $current_room->created_by = Auth::user()->id;
        $current_room->save();

        return $this->loadRoom($current_room->id);
    }

    public function save($room_id)
    {
        $current_room = ChatRoom::find($room_id);
        $current_room = $this->updateRoomByRequest($current_room);
        $current_room->save();

        return $this->loadRoom($current_room->id);
    }

    public function archive($room_id)
    {
        $current_room = ChatRoom::find($room_id);
        $current_room->delete();

        return $this->loadRoom(null);
    }

    public function updateRoomByRequest($current_room)
    {
        $current_room->name = request()->input('name');
        $current_room->slug = Str::slug(request()->input('name'));

        if ($current_room->access()->first()) {
            $current_room->access()->delete();
        }

        if (request()->input('external') == '') {
            $user_id = request()->input('access_user');
            $user = User::find($user_id);
            if ($user->team_id == Auth::user()->team_id) {
                $current_room->external = false;
            } else {
                $current_room->external = true;
            }
        } else {
            $current_room->external = request()->input('external');
        }
        $current_room->direct = request()->input('direct');

        if (! $current_room->created_by) {
            $current_room->created_by = Auth::user()->id;
        }
        $current_room->save();

        if (request()->input('access_level') == 'internal') {
            if (request()->input('access_type') == 'team') {
                $roomaccess = new ChatRoomAccess;
                $roomaccess->type = 'team';
                $roomaccess->team_id = Auth::user()->team_id;
                $roomaccess->room_id = $current_room->id;
                $roomaccess->save();
            }
            if (request()->input('access_type') == 'user') {
                $roomaccess = new ChatRoomAccess;
                $roomaccess->type = 'user';
                $roomaccess->user_id = Auth::user()->id;
                $roomaccess->room_id = $current_room->id;
                $roomaccess->save();
                //dd(request()->input());
                if (request()->input('access_users')) {
                    foreach (request()->input('access_users') as $userid) {
                        $roomaccess = new ChatRoomAccess;
                        $roomaccess->type = 'user';
                        $roomaccess->user_id = $userid;
                        $roomaccess->room_id = $current_room->id;
                        $roomaccess->save();
                    }
                }
            }
        }
        if (request()->input('access_level') == 'external') {
            $roomaccess = new ChatRoomAccess;
            $roomaccess->type = 'user';
            $roomaccess->user_id = Auth::user()->id;
            $roomaccess->room_id = $current_room->id;
            $roomaccess->save();

            if (request()->input('access_teams')) {
                foreach (request()->input('access_teams') as $teamid) {
                    $roomaccess = new ChatRoomAccess;
                    $roomaccess->type = 'team';
                    $roomaccess->team_id = $teamid;
                    $roomaccess->room_id = $current_room->id;
                    $roomaccess->save();
                }
            }

            if (request()->input('access_users')) {
                foreach (request()->input('access_users') as $userid) {
                    $roomaccess = new ChatRoomAccess;
                    $roomaccess->type = 'user';
                    $roomaccess->user_id = $userid;
                    $roomaccess->room_id = $current_room->id;
                    $roomaccess->save();
                }
            }
        }
        if (request()->input('access_level') == 'direct') {
        }

        return $current_room;
    }

    public function sendMessage($room_id)
    {
        $message = new ChatMessage;
        $message->team_id = Auth::user()->team_id;
        $message->user_id = Auth::user()->id;
        $message->room_id = $room_id;
        $message->message = request()->input('message');
        $message->save();

        $current_room = ChatRoom::find($room_id);
        $current_room->last_message_at = Carbon::now();
        $current_room->save();

        $roomusers = $current_room->members()->with('chatUserMemory')->get();
        foreach ($roomusers as $user) {
            if ($user->id == Auth::user()->id) {
                continue;
            }
            $chatusermemory = $this->getOrCreateChatUserMemory($user);

            $unread = $chatusermemory->unread_messages;
            if (! isset($unread[$current_room->id])) {
                $unread[$current_room->id] = [];
            }
            $unread[$current_room->id][] = $message->id;
            $chatusermemory->unread_messages = $unread;
            $chatusermemory->save();
        }

        return $this->loadMessages($room_id);
    }

    public function markRoomAsRead($room_id)
    {
        $room = ChatRoom::find($room_id);
        $chatusermemory = $this->getOrCreateChatUserMemory(Auth::user());
        $unread = $chatusermemory->unread_messages;
        //dd($unread, $room->id);
        if (isset($unread[$room->id])) {
            unset($unread[$room->id]);
            $chatusermemory->unread_messages = $unread;
            $chatusermemory->save();
        }

        return $unread;
    }

    public function loadRoom($room_id)
    {
        $current_room = ChatRoom::find($room_id);
        $internalrooms = Auth::user()->chatRooms()->whereExternal(false)->whereDirect(false)->get();
        $externalrooms = Auth::user()->chatRooms()->whereExternal(true)->whereDirect(false)->get();
        $directrooms = Auth::user()->chatRooms()->whereDirect(true)->get();

        $chatusermemory = $this->getOrCreateChatUserMemory(Auth::user());
        $chatusermemory->addRecentRoom($current_room);
        $unread_messages = collect($chatusermemory->unread_messages);
        $recent_rooms = collect($chatusermemory->recent_rooms);
        //dd($externalrooms);

        return view('shared-features.basechat.main', compact('internalrooms', 'externalrooms', 'directrooms', 'current_room', 'chatusermemory'));
    }

    public function getOrCreateChatUserMemory($user)
    {
        $chatusermemory = $user->chatUserMemory;
        if (! $chatusermemory) {
            $chatusermemory = new ChatUserMemory;
            $chatusermemory->user_id = $user->id;
            $chatusermemory->team_id = $user->team_id;
            $chatusermemory->recent_rooms = [];
            $chatusermemory->unread_messages = [];
            $chatusermemory->save();
        }

        return $chatusermemory;
    }

    public function loadMessages($room_id)
    {
        //dd($externalrooms);
        $current_room = ChatRoom::find($room_id);

        $html = view('shared-features.basechat.room', compact('current_room'))->render();
        $hash = crc32($html);
        if ($hash != session('basechat-messages-hash')) {
            session(['basechat-messages-hash' => $hash]);
            //dd($html);
            return $html;
        }

        return 'SAME';
    }

    public function checkUnread()
    {
        $json = collect([]);
        $chatusermemory = $this->getOrCreateChatUserMemory(Auth::user());
        $html = view('shared-features.basechat.quick-access', compact('chatusermemory'))->render();
        $hash = crc32($html);
        if ($hash != session('basechat-unread-hash')) {
            session(['basechat-unread-hash' => $hash]);
            $json['html'] = $html;
        }
        $json['unread'] = $chatusermemory->unread_messages;

        return $json;
    }

    public function updateChat($room_id)
    {
        $chat_update = collect([]);
        $chat_update['messages'] = $this->loadMessages($room_id);
        $chat_update['check_unread'] = $this->checkUnread();

        return $chat_update;
    }
}
