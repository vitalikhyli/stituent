<?php

namespace Database\Seeders;

use App\Category;
use App\Group;
use App\GroupPerson;
use App\Models\CC\CCConstituentGroup;
use App\Models\CC\CCIssueGroup;
use App\Person;
use App\Team;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $valid_campaign_ids = Team::pluck('old_cc_id')
                    ->unique();

        echo "Clearing existing categories, groups, and group_person.\n";
        Category::truncate();
        Group::truncate();
        GroupPerson::truncate();

        // ===============================================================> CATEGORIES

        echo "Adding categories.\n";
        $category_cg = new Category;
        $category_cg->team_id = 0;
        $category_cg->preset = 'office';
        $category_cg->name = 'Constituent Groups';
        $category_cg->has_position = false;
        $category_cg->data_template = ['notes' => null];
        $category_cg->save();

        $category_ig = new Category;
        $category_ig->team_id = 0;
        $category_ig->preset = 'office';
        $category_ig->name = 'Issue Groups';
        $category_ig->has_position = true;
        $category_ig->data_template = ['notes' => null, 'position' => null];
        $category_ig->save();

        $category_l = new Category;
        $category_l->team_id = 0;
        $category_l->preset = 'office';
        $category_l->name = 'Legislation';
        $category_l->has_position = true;
        $category_l->data_template = ['notes' => null, 'position' => null];
        $category_l->save();

        // ===============================================================> ISSUE GROUPS

        $count = 0;
        CCIssueGroup::whereIn('campaignID', $valid_campaign_ids)
                          ->chunk(100, function ($cc_issue_groups) use (&$count, $category_ig) {
                              echo "=============================> Running $count...\n";
                              $count += 100;
                              foreach ($cc_issue_groups as $cc_i_group) {
                                  $team = Team::where('old_cc_id', $cc_i_group->campaignID)->first();

                                  if (! $team) {
                                      continue;
                                  }
                                  echo $team->name.': Starting group '.$cc_i_group->category_name."\n";

                                  $group = new Group;
                                  $group->team_id = $team->id;
                                  $group->old_cc_id = $cc_i_group->categoryID;
                                  $group->name = $cc_i_group->category_name;
                                  $group->notes = $cc_i_group->category_notes;
                                  $group->category_id = $category_ig->id;

                                  $groupinfo = [];
                                  $group->additional_info = $groupinfo;

                                  if ($tempdate = dateIsClean($cc_i_group->create_date)) {
                                      $group->created_at = $tempdate;
                                  }
                                  if ($tempdate = dateIsClean($cc_i_group->update_date)) {
                                      $group->updated_at = $tempdate;
                                  }

                                  $group->save();

                                  if (! $team->account->billygoat_id && $cc_i_group->assignments()->count() > 500) {
                                      echo 'Skipping Group Assignment on Large Group ('.$cc_i_group->assignments()->count().') for '.$team->name."\n";
                                      continue;
                                  }

                                  if ($cc_i_group->assignments()->count() > 5000) {
                                      echo 'WAY TOO LARGE GROUP ('.$cc_i_group->assignments()->count().') for '.$team->name."\n";
                                      continue;
                                  }

                                  echo 'Processing '.$cc_i_group->assignments()->count()." assignments\n";

                                  $cc_i_group->assignments()->with('ccVoter')
                           ->chunk(1000, function ($cc_assignments) use ($team, $group) {
                               foreach ($cc_assignments as $cc_assignment) {
                                   $person = Person::where('old_cc_id', $cc_assignment->voterID)->first();
                                   if (! $person) {
                                       //dd($cc_assignment);
                                       $ccvoter = $cc_assignment->ccVoter;
                                       if (! $ccvoter) {
                                           continue;
                                       }
                                       if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                                           $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                                       } else {
                                           $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                                       }
                                       if (! $person) {
                                           echo $team->name." Couldn't find ".$ccvoter->voter_code."\n";
                                           continue;
                                       }
                                       if ($tempdate = dateIsClean($cc_assignment->update_date)) {
                                           $person->updated_at = $tempdate;
                                       }
                                       if ($tempdate = dateIsClean($cc_assignment->create_date)) {
                                           $person->created_at = $tempdate;
                                       }
                                   }
                                   $groupperson = new GroupPerson;
                                   $groupperson->team_id = $team->id;
                                   $groupperson->group_id = $group->id;
                                   $groupperson->person_id = $person->id;
                                   $groupperson->position = $cc_assignment->position;
                                   $groupperson->data = $cc_assignment->notes;
                                   if ($tempdate = dateIsClean($cc_assignment->update_date)) {
                                       $groupperson->updated_at = $tempdate;
                                   }
                                   if ($tempdate = dateIsClean($cc_assignment->create_date)) {
                                       $groupperson->created_at = $tempdate;
                                   }
                                   $groupperson->save();
                               }
                           });
                              }
                          });

        // ===============================================================> CONSTITUENT GROUPS

        echo "Adding Constituent Groups.\n";

        $count = 0;
        CCConstituentGroup::whereIn('campaignID', $valid_campaign_ids)
                          ->chunk(100, function ($cc_constituent_groups) use (&$count, $category_cg) {
                              echo "=============================> Running $count...\n";
                              $count += 100;
                              foreach ($cc_constituent_groups as $cc_c_group) {
                                  //echo "Starting group ".$cc_c_group->group_name."\n";
                                  $team = Team::where('old_cc_id', $cc_c_group->campaignID)->first();

                                  if (! $team) {
                                      continue;
                                  }
                                  echo $team->name.': Starting group '.$cc_c_group->group_name."\n";

                                  $group = new Group;
                                  $group->team_id = $team->id;
                                  $group->old_cc_id = $cc_c_group->groupID;
                                  $group->name = $cc_c_group->group_name;
                                  $group->notes = $cc_c_group->group_notes;
                                  $group->category_id = $category_cg->id;

                                  $groupinfo = [];
                                  $groupinfo['address_1'] = $cc_c_group->group_address_1;
                                  $groupinfo['address_2'] = $cc_c_group->group_address_2;
                                  $groupinfo['city'] = $cc_c_group->group_city;
                                  $groupinfo['state'] = $cc_c_group->group_state;
                                  $groupinfo['zip_code'] = $cc_c_group->group_postal_code;
                                  $groupinfo['phone_1'] = $cc_c_group->group_phone_1;
                                  $groupinfo['phone_2'] = $cc_c_group->group_phone_2;
                                  $groupinfo['fax'] = $cc_c_group->group_fax;
                                  $groupinfo['email'] = $cc_c_group->group_email;
                                  $groupinfo['web'] = $cc_c_group->group_web;
                                  $group->additional_info = $groupinfo;

                                  if ($tempdate = dateIsClean($cc_c_group->create_date)) {
                                      $group->created_at = $tempdate;
                                  }
                                  if ($tempdate = dateIsClean($cc_c_group->update_date)) {
                                      $group->updated_at = $tempdate;
                                  }
                                  if ($tempdate = dateIsClean($cc_c_group->deleted_at)) {
                                      $group->deleted_at = $tempdate;
                                  }

                                  $group->save();

                                  //echo "Starting Group Assignments ".$cc_c_group->group_name."\n";

                                  if (! $team->account->billygoat_id && $cc_c_group->assignments()->count() > 500) {
                                      echo 'Skipping Group Assignment on Large Group ('.$cc_c_group->assignments()->count().') for '.$team->name."\n";
                                      continue;
                                  }

                                  if ($cc_c_group->assignments()->count() > 5000) {
                                      echo 'WAY TOO LARGE GROUP ('.$cc_c_group->assignments()->count().') for '.$team->name."\n";
                                      continue;
                                  }

                                  echo 'Processing '.$cc_c_group->assignments()->count()." assignments\n";

                                  $cc_c_group->assignments()->with('ccVoter')
                           ->chunk(1000, function ($cc_assignments) use ($team, $group) {
                               foreach ($cc_assignments as $cc_assignment) {
                                   $person = Person::where('old_cc_id', $cc_assignment->voterID)->first();
                                   if (! $person) {
                                       //dd($cc_assignment);
                                       $ccvoter = $cc_assignment->ccVoter;
                                       if (! $ccvoter) {
                                           continue;
                                       }
                                       if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                                           $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                                       } else {
                                           $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                                       }
                                       if (! $person) {
                                           echo $team->name." Couldn't find ".$ccvoter->voter_code."\n";
                                           continue;
                                       }
                                   }
                                   $groupperson = new GroupPerson;
                                   $groupperson->team_id = $team->id;
                                   $groupperson->group_id = $group->id;
                                   $groupperson->person_id = $person->id;
                                   $groupperson->position = $cc_assignment->position_held;
                                   $groupperson->data = $cc_assignment->notes;
                                   if ($tempdate = dateIsClean($cc_assignment->update_date)) {
                                       $groupperson->updated_at = $tempdate;
                                   }
                                   if ($tempdate = dateIsClean($cc_assignment->deleted_at)) {
                                       $groupperson->deleted_at = $tempdate;
                                   }
                                   $groupperson->save();
                               }
                           });
                              }
                          });
    }

    public function dateIsClean($date)
    {
        if (! $date) {
            return false;
        }
        if (Str::startsWith($date, '0000-00-00')) {
            return false;
        }

        try {
            $carbondate = Carbon::parse($date);
        } catch (\Exception $e) {
            return false;
        }
        $datearr = explode('-', $date);
        $year = (int) $datearr[0];
        $month = (int) $datearr[1];
        $day = (int) $datearr[2];
        if ($day < 1) {
            $day = 1;
        }
        if ($month < 1) {
            $month = 1;
        }
        $carbondate = Carbon::parse("$year-$month-$day");
        if ($carbondate > Carbon::parse('1900-01-01')) {
            return "$year-$month-$day";
        }

        return false;
    }
}
