<?php

namespace App\Http\Controllers\Campaign;

use App\CampaignParticipant;
use App\Donation;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index()
    {
        $renumber_id = 0; // So collections are merged without removals

        //////////////////////////////////////////////////////////////////

        $support = CampaignParticipant::thisTeam()
                                      ->thisCampaign()
                                      ->whereNotNull('support')
                                      ->with('participant:id,full_name')
                                      ->orderBy('updated_at')
                                      ->get();

        $support_counter = [1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1];

        $support->each(function ($item) {
            return $item['type'] = 'support';
        });
        $support->each(function ($item) {
            return $item['qual'] = SupportNumberToEnglish($item->support);
        });
        $support->each(function ($item) use (&$support_counter) {
            return $item['qual2'] = 'Total '
                                    .SupportNumberToEnglish($item->support)
                                    .': '
                                    .$support_counter[$item->support]++;
        });
        $support->each(function ($item) use (&$renumber_id) {
            return $item['id'] = $renumber_id++;
        });

        //////////////////////////////////////////////////////////////////

        $lawnsigns = CampaignParticipant::thisTeam()
                                      ->thisCampaign()
                                      ->where('volunteer_lawnsign', true)
                                      ->with('participant:id,full_name')
                                      ->orderBy('updated_at')
                                      ->get();

        $lawnsigns_counter = 1;

        $lawnsigns->each(function ($item) {
            return $item['type'] = 'lawnsigns';
        });
        $lawnsigns->each(function ($item) {
            return $item['qual'] = 'Lawnsign';
        });
        $lawnsigns->each(function ($item) use (&$lawnsigns_counter) {
            return $item['qual2'] = 'Total Lawnsigns: '.$lawnsigns_counter++;
        });
        $lawnsigns->each(function ($item) use (&$renumber_id) {
            return $item['id'] = $renumber_id++;
        });

        //////////////////////////////////////////////////////////////////

        $volunteers = CampaignParticipant::thisTeam()
                                      ->thisCampaign()
                                      ->where(function ($q) {
                                          $q->orWhere('volunteer_general', true);
                                          $q->orWhere('volunteer_door_knock', true);
                                          $q->orWhere('volunteer_phone_calls', true);
                                          $q->orWhere('volunteer_hold_signs', true);
                                          $q->orWhere('volunteer_office_work', true);
                                          $q->orWhere('volunteer_write_letters', true);
                                      })
                                      ->with('participant:id,full_name')
                                      ->orderBy('updated_at')
                                      ->get();

        $volunteers_counter = 1;

        $volunteers->each(function ($item) {
            return $item['type'] = 'volunteers';
        });
        $volunteers->each(function ($item) {
            $v_types = [];
            if ($item->volunteer_general) {
                $v_types[] = 'General';
            }
            if ($item->volunteer_door_knock) {
                $v_types[] = 'Doors';
            }
            if ($item->volunteer_phone_calls) {
                $v_types[] = 'Calls';
            }
            if ($item->volunteer_hold_signs) {
                $v_types[] = 'Signs';
            }
            if ($item->volunteer_office_work) {
                $v_types[] = 'Office';
            }
            if ($item->volunteer_write_letters) {
                $v_types[] = 'Letters';
            }

            return $item['qual'] = 'Volunteer ('.implode(', ', $v_types).')';
        });
        $volunteers->each(function ($item) use (&$volunteers_counter) {
            return $item['qual2'] = 'Total Volunteers: '.$volunteers_counter++;
        });
        $volunteers->each(function ($item) use (&$renumber_id) {
            return $item['id'] = $renumber_id++;
        });

        //////////////////////////////////////////////////////////////////

        $donations = Donation::thisTeam()->with('participant:id,full_name')->get();
        $donations->each(function ($item) {
            return $item['type'] = 'contribution';
        });
        $donations->each(function ($item) {
            return $item['qual'] = '$'.number_format($item->amount, 2, '.', ',');
        });
        $donations->each(function ($item) use (&$renumber_id) {
            return $item['id'] = $renumber_id++;
        });

        //////////////////////////////////////////////////////////////////

        $items = $support->merge($lawnsigns)
                         ->merge($volunteers)
                         ->merge($donations);

        $items->each(function ($item) {
            if (Carbon::now()->diffInSeconds($item['updated_at']) <= 600) {
                return $item['is_new'] = true;
            }
        });

        $items->each(function ($item) {
            if (Carbon::parse($item['updated_at'])->isToday()) {
                return $item['the_date'] = 'TODAY - '.Carbon::parse($item['updated_at'])->format('h:i a');
            }

            return $item['the_date'] = Carbon::parse($item['updated_at'])->format('M n - h:i a');
        });

        //////////////////////////////////////////////////////////////////

        $items_count = $items->count();
        $items = $items->sortByDesc('updated_at');
        $items->take(100);

        //////////////////////////////////////////////////////////////////

        return view('campaign.progress.index', compact('items', 'items_count'));
    }
}
