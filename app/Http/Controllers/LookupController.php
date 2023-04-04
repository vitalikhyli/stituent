<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Group;
use App\Participant;
use App\Person;
use App\Traits\ConstituentQueryTrait;
use App\Traits\ParticipantQueryTrait;
use App\Voter;
use App\WorkCase;
use Auth;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    use ConstituentQueryTrait;
    use ParticipantQueryTrait;

    ////////////////////////////////////////////////////////////////////////////

    public function linkToEntity($app_type, $entity_id, $str = null)
    {
        $entity = Entity::find($entity_id);

        if (! $str) {
            return;
        }
        $str_arr = explode(' ', $str, 2);

        if (count($str_arr) == 1) {
            $fname_input = [];
            $fname_input['first_name'] = $str;
            $fpeople = $this->constituentQuery($fname_input, 10);

            $lname_input = [];
            $lname_input['last_name'] = $str;
            $lpeople = $this->constituentQuery($lname_input, 10);

            $people = $fpeople->merge($lpeople)->take(10);
        }
        if (count($str_arr) == 2) {
            $input = [];
            $input['first_name'] = $str_arr[0];
            $input['last_name'] = $str_arr[1];
            $people = $this->constituentQuery($input, 10);
        }

        $groups = Group::where('team_id', Auth::user()->team->id)
                       ->where('name', 'LIKE', '%'.$str.'%')
                       ->get();

        $cases = WorkCase::where('team_id', Auth::user()->team->id)
                         ->where('subject', 'LIKE', '%'.$str.'%')
                         ->get();

        $personal_emails = Person::where('team_id', Auth::user()->team->id)
                         ->where('primary_email', 'LIKE', '%'.$str.'%')
                         ->take(10)
                         ->get();

        $work_emails = Person::where('team_id', Auth::user()->team->id)
                         ->where('work_email', 'LIKE', '%'.$str.'%')
                         ->take(10)
                         ->get();

        $emails = $personal_emails->merge($work_emails)->whereNotIn('id', $people->pluck('id'));

        return view('shared-features.lookup.link-to-entity', compact('str', 'people', 'groups', 'cases', 'emails', 'entity'));
    }

    ////////////////////////////////////////////////////////////////////////////

    public function main($app_type, $str = null)
    {
        if ($app_type == 'campaign') {
            return $this->campaign($app_type, $str);
        }
        //dd("Laz");
        ////////////////////////////////////////////////////////////////////////////

        if (!$str) return;

        $people = collect([]);
        $emails = collect([]);
        $phones = collect([]);
        $groups = collect([]);
        $cases  = collect([]);

        $words_str = explode(' ', $str, 2);

        $phone_str = phoneOnlyNumbers($str);

        // $is_phone = (is_numeric($phone_str) && strlen($phone_str) >= 10);
        $is_phone = (is_numeric($phone_str) && strlen($phone_str) >= 5);

        $is_email = filter_var($str, FILTER_VALIDATE_EMAIL);

        if ($is_email) {

            $input['email'] = $str;
            $emails = $this->constituentQuery($input, 10);

        } elseif ($is_phone) {

            $input['phone'] = $phone_str;
            $phones = $this->constituentQuery($input, 10);

        } else {

            if (count($words_str) == 1) {

                $fname_input = [];
                $fname_input['first_name'] = $str;
                $fpeople = $this->constituentQuery($fname_input, 10);

                $lname_input = [];
                $lname_input['last_name'] = $str;
                $lpeople = $this->constituentQuery($lname_input, 10);

                $people = $fpeople->merge($lpeople);

            } elseif (count($words_str) == 2) {

                $input = [];
                $input['first_name'] = $words_str[0];
                $input['last_name'] = $words_str[1];
                $people = $this->constituentQuery($input, 10);

            }

            $groups = Group::where('team_id', Auth::user()->team->id)
                           ->where('name', 'LIKE', '%'.$str.'%')
                           ->get();

            $cases = WorkCase::where('team_id', Auth::user()->team->id)
                             ->where('subject', 'LIKE', '%'.$str.'%')
                             ->get();

            $input['email'] = $str;
            $emails = $this->constituentQuery($input, 10);

        }

        ////////////////////////////////////////////////////////////////////////////

        if ($phones) {
            $phones = $phones->each(function ($item) {
                $list = [];
                if($item->primary_phone)    $list[] = $item->primary_phone;
                if($item->work_phone)       $list[] = $item->work_phone;
                if ($item->other_phones) {
                    foreach($item->other_phones as $phone) {
                        if (!is_array($phone)) continue;
                        if (is_array($phone[0]) || is_array($phone[1])) continue;
                        $list[] = trim($phone[0].' '.$phone[1]);
                    }
                }
                $item->phoneDisplay = trim(implode(', ', $list));
            });
        }

        if ($emails) {
            $emails = $emails->each(function ($item) {
                $list = [];
                if($item->primary_email)    $list[] = $item->primary_email;
                if($item->work_email)       $list[] = $item->work_email;

                if ($item->other_emails) {
                    foreach($item->other_emails as $email) {
                        if (!is_array($email)) continue;
                        if (isset($email[1])) {
                            if (is_array($email[0]) || is_array($email[1])) continue;
                            $list[] = trim($email[0].' '.$email[1]);
                        } else {
                            if (is_array($email[0])) {
                                $list[] = array_pop(array_reverse($email[0]));
                            } else {
                                $list[] = trim($email[0]);
                            }
                            
                        }
                    }
                }
                $item->emailDisplay = trim(implode(', ', $list));
            });
        }
        ////////////////////////////////////////////////////////////////////////////

        return view('shared-features.lookup.main', compact('str', 
                                                           'people', 
                                                           'groups', 
                                                           'cases', 
                                                           'emails',
                                                            'phones'));
    }

    ////////////////////////////////////////////////////////////////////////////

    // public function campaign($app_type, $str = null)
    // {
    //     if (!$str) return;

    //     $input['limit'] = 10;

    //     $words = explode(' ', $str);

    //     if (count($words) == 1) {

    //         $input['first_name'] = $str;
    //         $input['last_name']  = null;
    //         $a = $this->participantQuery($input);

    //         $input['last_name']  = $str;
    //         $input['first_name']  = null;
    //         $b = $this->participantQuery($input);

    //         $names = $a->merge($b);

    //     } elseif (count($words) > 1) {

    //         $input['first_name'] = array_shift($words);
    //         $input['last_name']  = implode(' ', $words);
    //         $names = $this->participantQuery($input);
    //     }

    //     $input['email'] = $str;
    //     $input['first_name']  = null;
    //     $input['last_name']  = null;
    //     $emails = $this->participantQuery($input);

    //     $results = $names->merge($emails)->take(20);

    //     return view('campaign.lookup.main', ['participants' => $results]);
    // }


    public function campaign($app_type, $str = null)
    {

        ////////////////////////////////////////////////////////////////////////////

        if (!$str) return;

        $participants = collect([]);
        $emails       = collect([]);
        $phones       = collect([]);

        $words_str = explode(' ', $str, 2);

        $phone_str = phoneOnlyNumbers($str);

        $is_phone = (is_numeric($phone_str) && strlen($phone_str) >= 10);

        $is_email = filter_var($str, FILTER_VALIDATE_EMAIL);

        if ($is_email) {

            $input['email'] = $str;
            $emails = $this->participantQuery($input, 10);

        } elseif ($is_phone) {

            $input['phone'] = $phone_str;
            $phones = $this->participantQuery($input, 10);

        } else {

            if (count($words_str) == 1) {

                $fname_input = [];
                $fname_input['first_name'] = $str;
                $fparticipants = $this->participantQuery($fname_input, 10);

                $lname_input = [];
                $lname_input['last_name'] = $str;
                $lparticipants = $this->participantQuery($lname_input, 10);

                $participants = $fparticipants->merge($lparticipants)->take(10);

            } elseif (count($words_str) == 2) {

                $input = [];
                $input['first_name'] = $words_str[0];
                $input['last_name'] = $words_str[1];
                $participants = $this->participantQuery($input, 10);

            }

            $input['email'] = $str;
            $emails = $this->participantQuery($input, 10);

        }

        ////////////////////////////////////////////////////////////////////////////

        if ($phones) {
            $phones = $phones->each(function ($item) {
                $list = [];
                if($item->primary_phone)    $list[] = $item->primary_phone;
                if($item->work_phone)       $list[] = $item->work_phone;
                if ($item->other_phones) {
                    foreach($item->other_phones as $phone) {
                        if (!is_array($phone)) continue;
                        if (is_array($phone[0]) || is_array($phone[1])) continue;
                        $list[] = trim($phone[0].' '.$phone[1]);
                    }
                }
                $item->phoneDisplay = trim(implode(', ', $list));
            });
        }

        if ($emails) {
            $emails = $emails->each(function ($item) {
                $list = [];
                if($item->primary_email)    $list[] = $item->primary_email;
                if($item->work_email)       $list[] = $item->work_email;

                if ($item->other_emails) {
                    foreach($item->other_emails as $email) {
                        if (!is_array($email)) continue;
                        if (is_array($email[0]) || is_array($email[1])) continue;
                        $list[] = trim($email[0].' '.$email[1]);
                    }
                }
                $item->emailDisplay = trim(implode(', ', $list));
            });
        }
        ////////////////////////////////////////////////////////////////////////////

        return view('campaign.lookup.main', compact('str', 
                                                    'participants',  
                                                    'emails',
                                                    'phones'));
    }

}
