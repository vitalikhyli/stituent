<?php

namespace App;

use App\Campaign;
use App\Contact;
use App\Permission;
use Auth;
use Carbon\Carbon;
// use App\Models\BaseChat\ChatRoom;
// use App\Models\BaseChat\ChatRoomAccess;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Mpociot\Teamwork\Traits\UserHasTeams;

class User extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    use UserHasTeams;

    public function getBGColorAttribute()
    {
        // if (session('team_state') == 'RI') {
        //     return ['r' => 0, 'g' => 100, 'b' => 80];
        // }
        return [];
    }
    public function getBGImageAttribute()
    {
        // if (session('team_state') == 'RI') {

        // }
        return null;
    }
    public function people()
    {
        return $this->team->people();
    }
    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    public function getCurrentAppPinAttribute()
    {
        $device = Device::where('user_id', $this->id)
                        ->whereNull('live_at')
                        ->first();
        if (!$device) {
            $device = new Device;
            $device->team_name =    $this->team->name;
            $device->team_id =      $this->team->id;
            $device->user_name =    $this->name;
            $device->user_id =      $this->id;

            $device->team_state =   'MA';
            $device->team_table =   $this->team->db_slice;

            $random_pin = rand(10000,99999);
            while (Device::where('pin', $random_pin)->exists()) {
                $random_pin = rand(10000,99999);
            }

            $device->pin = $random_pin;
            $device->save();
        }

        if ($device->updated_at < Carbon::today()->subDay()) {
            $random_pin = rand(10000,99999);
            while (Device::where('pin', $random_pin)->exists()) {
                $random_pin = rand(10000,99999);
            }

            $device->pin = $random_pin;
            $device->save();
        }
        return $device->pin;
    }
    public function getAppTypeAttribute()
    {
        return $this->team->app_type;
    }
    public function updateEmail($email)
    {
        $this->email = $email;
        $this->save();
    }

    public function getTeamLanguageAttribute()
    {
        return $this->team->app_type.'-'.$this->language; //e.g. u-en -- change key words in share-features
    }
    public function contactTypes()
    {
        return $this->team->contactTypes();
    }

    public function account()
    {
        return $this->team->account();
    }

    public function campaign_current()
    {
        $campaign = Campaign::where('team_id', $this->team->id)
                            ->where('current', true)
                            ->first();

        return $campaign;
    }

    public function currentCampaign()
    {
        return $this->campaign_current();
    }

    public function getOfficeTeamAttribute()
    {
        return $this->team->account->teams()->where('app_type', 'office')->first();
    }

    public function getUniversityTeamAttribute()
    {
        return $this->team->account->teams()->where('app_type', 'u')->first();
    }


    public function isSenate()
    {
        if (Str::contains(session('team_table'), '_S_')) {
            return true;
        }

        return false;
    }

    public function permissionsForTeam($theteam)
    {
        $pivot = Permission::where('user_id', $this->id)
                           ->where('team_id', $theteam->id)
                           ->first();
        if (! $pivot) {
            $pivot = new Permission;
        }

        return $pivot;
    }
    public function groupPerson()
    {
        return $this->team->groupPerson();
    }

    public function getPermissionsArrayFor($theteam)
    {
        $pivot = Permission::where('user_id', $this->id)
                                ->where('team_id', $theteam->id)
                                ->first();

        $available_permissions = ['developer', 'admin', 'export', 'reports', 'constituents', 'createconstituents', 'metrics', 'creategroups'];

        $string = null;

        $all = [];

        if (! $pivot) {
            return [];
        }

        foreach ($available_permissions as $permission) {
            $obj = new class {
            };
            $obj->id = $pivot->id;
            $obj->name = $permission;
            $obj->value = ($pivot->$permission) ? true : false;

            $all[] = $obj;
        }

        return $all;
    }

    public function openFileDirectory($id)
    {
        $dirs = $this->getMemory('open_dirs');
        if (! $dirs) {
            $dirs = [$id];
            $this->addMemory('open_dirs', $dirs);
        } else {
            if (! in_array($id, $dirs)) {
                $dirs[] = $id;
                $this->addMemory('open_dirs', $dirs);
            }
        }
    }

    public function closeFileDirectory($id)
    {
        $dirs = $this->getMemory('open_dirs');
        if (! $dirs) {
            // Do nothing, but this should never occur
        } else {
            if (in_array($id, $dirs)) {
                foreach ($dirs as $key => $checkdir) {
                    if ($checkdir == $id) {
                        unset($dirs[$key]);
                    }
                }
                $this->addMemory('open_dirs', $dirs);
            }
        }
    }

    public function getClicksAttribute()
    {
        if ($this->saved_clicks) {
            return $this->saved_clicks;
        }
        $this->saved_clicks = $this->userLogs()->whereNull('type')->whereNull('mock_id')->count();

        return $this->saved_clicks;
    }

    public function getLastActivityAttribute()
    {
        $last = $this->userLogs()
                     ->whereNull('type')
                     ->whereNull('mock_id')
                     ->orderBy('created_at', 'desc')
                     ->first();
        if ($last) {
            return $last->created_at;
        }

        return null;
    }

    public function userLogs()
    {
        return $this->hasMany(UserLog::class);
    }

    public function generateLoginToken()
    {
        return $this->login_token = substr(md5(rand(0, 9).$this->email.time()), 0, 32);
    }

    public function getFirstNameAttribute()
    {
        $username = $this->name;
        $arr = explode(' ', $username);

        return $arr[0];
    }

    public function getLastNameAttribute()
    {
        $username = $this->name;
        $arr = explode(' ', $username);
        if (count($arr) > 1) {
            return $arr[count($arr) - 1];
        }
    }

    public function getShortNameAttribute()
    {
        return substr($this->first_name, 0, 1).' '.$this->last_name;
    }

    public function outstandingFollowUps()
    {
        $followups = Contact::where('followup', 1)
                            ->where('followup_done', 0)
                            ->where('private', 0)
                            ->where('team_id', $this->team->id)
                            ->orWhere(function ($q) {
                                $q->where('private', 1);
                                $q->where('user_id', Auth::user()->id);
                                $q->where('followup_done', 0);
                                $q->where('followup', 1);
                                $q->where('team_id', $this->team->id);
                            })
                            ->get();

        return $followups;
    }

    public function doneFollowUps()
    {
        $followups = Contact::where('followup', 1)
                            ->where('followup_done', 1)
                            ->where('private', 0)
                            ->where('team_id', $this->team->id)
                            ->orWhere(function ($q) {
                                $q->where('private', 1);
                                $q->where('user_id', Auth::user()->id);
                                $q->where('followup_done', 1);
                                $q->where('followup', 1);
                                $q->where('team_id', $this->team->id);
                            })
                            ->get();

        return $followups;
    }

    public function acceptTerms()
    {
        $this->accepted_terms = now();
        $this->save();
    }

    public function categories()
    {
        return $this->team->categories();
    }
    public function campaignLists()
    {
        return $this->team->campaignLists();
    }

    public function entities()
    {
        return $this->team->entities();
    }

    public function breadcrumb($current_title, $current_page_type, $reset = null)
    {

        // dd(url()->current(), parse_url(url()->current(), PHP_URL_PATH), dirname(parse_url(url()->current(), PHP_URL_PATH)));

        $first_dir = dirname(parse_url(url()->current(), PHP_URL_PATH));

        if (($this->breadcrumb == null)) {
            $breadcrumb = [];
            $breadcrumb[0] = ['url' => url()->current(), 'type' => $current_page_type, 'title' => $current_title];
            $this->breadcrumb = json_encode($breadcrumb);
        } elseif ($reset == 'level_0') {
            $breadcrumb = [];
            $breadcrumb[0] = ['url' => url()->current(), 'type' => $current_page_type, 'title' => $current_title];
            $this->breadcrumb = json_encode($breadcrumb);
        } elseif ($reset == 'level_1') {
            $breadcrumb = [];
            $breadcrumb[0] = ['url' => $first_dir, 'type' => 'dashboard', 'title' => 'Home'];
            $breadcrumb[1] = ['url' => url()->current(), 'type' => $current_page_type, 'title' => $current_title];
            $this->breadcrumb = json_encode($breadcrumb);
        }
        $this->save();

        $html = '';

        $breadcrumb = json_decode($this->breadcrumb, true);

        $highest_key = array_key_last($breadcrumb);

        // IF WAS SAME TYPE OF PAGE, GO BACK TO THE FIRST INSTANCE OF THE TYZPE
        $k = 0;
        foreach ($breadcrumb as $crumb) {
            if ($crumb['type'] == $current_page_type) {
                if ($k != $highest_key) {
                    $key_end = $k + 1;
                }
            }
            $k++;
        }
        if (isset($key_end)) {
            $breadcrumb = array_slice($breadcrumb, 0, $key_end);
        }

        $highest_key = array_key_last($breadcrumb);

        if ($breadcrumb[$highest_key]['url'] != url()->current()) {
            if ($breadcrumb[$highest_key]['type'] != $current_page_type) {
                $breadcrumb[$highest_key + 1]['url'] = url()->current();
                $breadcrumb[$highest_key + 1]['type'] = $current_page_type;
                $breadcrumb[$highest_key + 1]['title'] = $current_title;
            } else {
                $breadcrumb[$highest_key]['url'] = url()->current();
                $breadcrumb[$highest_key]['type'] = $current_page_type;
                $breadcrumb[$highest_key]['title'] = $current_title;
            }
        }

        $this->breadcrumb = json_encode($breadcrumb);
        $this->save();

        $counter = 0;
        $total = count($breadcrumb);
        foreach ($breadcrumb as $crumb) {
            if ($total == 1) {
                //Home
                $html .= '<span class="text-grey-lightest cursor-pointer">'.$crumb['title'].'</span>';
            } elseif ($counter == $total - 1) {
                $html .= '<span class="rounded-lg bg-blue-lighter text-grey-darkest ml-1 px-2 py-1 cursor-pointer">'.$crumb['title'].'</span>';
            } else {
                $html .= '<a class="text-grey-lightest hover:text-grey-light cursor-pointer" href="'.$crumb['url'].'">'.$crumb['title'].'</a> > ';
            }
            $counter++;
        }

        return $html;
    }

    public function privateContacts()
    {
        return $this->hasMany(Contact::class)->where('private', true);
    }

    // public function getIsAdminAttribute()
    // {
    //     return $this->permissions->admin;
    // }

    public function getAdminAttribute()
    {
        if ($this->permissions) return $this->permissions->admin;
    }

    public function contacts()
    {
        if (Auth::user()->permissions->admin) {
            return Contact::where('team_id', $this->current_team_id);
        }

        $private_ids = $this->privateContacts()->pluck('id');

        return Contact::where('team_id', $this->current_team_id)
                      ->where(function ($query) use ($private_ids) {
                          $query->where('private', false)
                                  ->orWhereIn('id', $private_ids);
                      });
    }

    public function cases()
    {
        return $this->hasMany(WorkCase::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'memory'            => 'array',
    ];

    protected $with = [
        //'permissions',
    ];

    public function memberOfTeam($team)
    {
        return $team->usersall->contains($this);
    }

    // public function team()
    // {
    //     return $this->currentTeam();
    // }

    public function team()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
        // dd($this, $this->belongsTo(Team::class, 'current_team_id')->toSql());
    }

    public function allteams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function allteams_and_accountteams()
    {
        // This is in case a user belongs to teams in different accounts
        // Probably only the case for developers

        $user = $this->belongsToMany(Team::class)->get();
        $team = $this->team->account->teams;

        return $user->merge($team);
    }
    public function getDeveloperAttribute()
    {
        return $this->permissions->developer;
    }
    public function getTeamIdAttribute()
    {
        return $this->current_team_id;
    }

    public function searches()
    {
        return $this->team->searches()->where('bulk_email', false);
    }

    public function permissions()
    {
        return $this->hasOne(Permission::class)->where('team_id', $this->current_team_id);
    }

    public function teamuser()
    {
        return $this->hasOne(TeamUser::class)->where('team_id', $this->current_team_id);
    }

    // public function chatUserMemory()
    // {
    //     return $this->hasOne(Models\BaseChat\ChatUserMemory::class);
    // }
    // public function chatRooms()
    // {
    //     // Complex. Each room has team or user access
    //     $teamrooms = ChatRoomAccess::where('type', 'team')
    //                                          ->where('team_id', $this->team_id)
    //                                          ->pluck('room_id');

    //     $userrooms = ChatRoomAccess::where('type', 'user')
    //                                          ->where('user_id', $this->id)
    //                                          ->pluck('room_id');

    //     $allroom_ids = $teamrooms->merge($userrooms);
    //     $allroom_ids = $allroom_ids->unique();

    //     //dd($allroom_ids);

    //     if (!$this->permissions->chat_external) {
    //         return ChatRoom::whereIn('id', $allroom_ids)
    //                             ->where('external', false);
    //     }
    //     return ChatRoom::whereIn('id', $allroom_ids);
    // }
    // public function getLastRoomIdAttribute()
    // {
    //     return $this->current_chat_room;
    // }
    // public function getCurrentChatRoomAttribute()
    // {
    //     $chatusermemory = $this->chatUserMemory;
    //     if (!$chatusermemory) {
    //         $chatusermemory = new Models\BaseChat\ChatUserMemory;
    //         $chatusermemory->user_id = $this->id;
    //         $chatusermemory->team_id = $this->team_id;
    //         $chatusermemory->recent_rooms = [];
    //         $chatusermemory->unread_messages = [];
    //         $chatusermemory->save();
    //     }
    //     if ($chatusermemory->current_room_id) {
    //         return $chatusermemory->current_room_id;
    //     }
    //     if (count($chatusermemory->recent_rooms) > 0) {
    //         return collect($chatusermemory->recent_rooms)->last();
    //     }
    //     return null;
    // }

    public function getmemory($key, $default = null)
    {
        $everything = $this->memory;
        if (isset($everything[$key])) {
            return $everything[$key];
        } else {
            // If there is no such key value, check if a default was given then use that
            if ($default) {
                // $this->addmemory("key",$default);
                $this->addmemory($key, $default);

                return $default;
            }
        }
    }

    public function addmemory($key, $val)
    {
        $addkey = $key;
        $everything = $this->memory;
        if ($everything) {
            if (($key = array_search($key, $everything)) !== false) {
                unset($everything[$key]);
            }
        } else {
            $everything = [];
        }
        $everything[$addkey] = $val;
        $everything = $everything;
        $this->memory = $everything;
        $this->save();
    }
}
