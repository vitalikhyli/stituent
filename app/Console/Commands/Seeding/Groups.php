<?php

namespace App\Console\Commands\Seeding;

use App\Category;
use App\Group;
use App\GroupPerson;
use App\Models\CC\CCConstituentGroup;
use App\Models\CC\CCIssueGroup;
use App\Models\CC\CCVoterArchive;
use App\Person;
use App\Team;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Groups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_groups {--campaign=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    //if (env('LOCAL_MACHINE') != 'Slothe')             return;
        if (! $this->confirm('GROUPS: Do you wish to continue?')) {
            return;
        }

        if ($this->option('campaign')) {
            $valid_campaign_ids = [$this->option('campaign')];
            echo date('Y-m-d h:i:s').' Starting Groups with Campaign ID '.$this->option('campaign')."\n";
        } else {
            echo "No campaign";
            return;
            $valid_campaign_ids = Team::pluck('old_cc_id')
                                      ->unique();
            echo date('Y-m-d h:i:s').' Starting Groups with '.count($valid_campaign_ids)." Campaigns\n";
            dd();
        }

        // ===============================================================> OFFICE CATEGORIES

        echo date('Y-m-d h:i:s')." Adding categories.\n";
        $category_cg = Category::whereName('Constituent Groups')->where('preset', 'office')->first();
        if (! $category_cg) {
            $category_cg = new Category;
            $category_cg->team_id = 0;
            $category_cg->preset = 'office';
            $category_cg->name = 'Constituent Groups';
            $category_cg->can_edit = false;
            $category_cg->has_position = false;
            $category_cg->has_title = true;
            $category_cg->has_notes = true;
            $category_cg->save();
        }

        $category_ig = Category::whereName('Issue Groups')->where('preset', 'office')->first();
        if (! $category_ig) {
            $category_ig = new Category;
            $category_ig->team_id = 0;
            $category_ig->preset = 'office';
            $category_ig->name = 'Issue Groups';
            $category_ig->can_edit = false;
            $category_ig->has_position = true;
            $category_ig->has_title = false;
            $category_ig->has_notes = true;
            $category_ig->save();
        }

        $category_l = Category::whereName('Legislation')->where('preset', 'office')->first();
        if (! $category_l) {
            $category_l = new Category;
            $category_l->team_id = 0;
            $category_l->preset = 'office';
            $category_l->name = 'Legislation';
            $category_l->can_edit = false;
            $category_l->has_position = true;
            $category_l->has_title = false;
            $category_l->has_notes = true;
            $category_l->save();
        }

        // ===============================================================> U CATEGORIES

        $category_u = Category::whereName('Miscellaneous Groups')->where('preset', 'u')->first();
        if (! $category_u) {
            $category_u = new Category;
            $category_u->team_id = 0;
            $category_u->preset = 'u';
            $category_u->name = 'Miscellaneous Groups';
            $category_u->can_edit = false;
            $category_u->has_position = true;
            $category_u->has_title = false;
            $category_u->has_notes = true;
            $category_u->save();
        }

        $category_u = Category::whereName('Issue Groups')->where('preset', 'u')->first();
        if (! $category_u) {
            $category_u = new Category;
            $category_u->team_id = 0;
            $category_u->preset = 'u';
            $category_u->name = 'Issue Groups';
            $category_u->can_edit = false;
            $category_u->has_position = true;
            $category_u->has_title = false;
            $category_u->has_notes = true;
            $category_u->save();
        }

        $category_u = Category::whereName('Campus Groups')->where('preset', 'u')->first();
        if (! $category_u) {
            $category_u = new Category;
            $category_u->team_id = 0;
            $category_u->preset = 'u';
            $category_u->name = 'Campus Groups';
            $category_u->can_edit = false;
            $category_u->has_position = true;
            $category_u->has_title = false;
            $category_u->has_notes = true;
            $category_u->save();
        }

        // ===============================================================> ISSUE GROUPS

        $count = 0;
        foreach ($valid_campaign_ids as $campaign_id) {
            $team = Team::where('old_cc_id', $campaign_id)->first();
            echo date('Y-m-d h:i:s').' '.$team->name.": About to add Issue Groups\n";

            if (!session('live')) {
                return;
            }
            //ADD THE TEAM'S OWN INSTANCE OF PRESET CATEGORY

            $presets = Category::wherePreset($team->app_type)->get();
            foreach ($presets as $preset_category) {
                $team_category = Category::where('team_id', $team->id)
                                             ->where('name', $preset_category->name)
                                             ->first();
                if (! $team_category) {
                    $template_category = Category::whereNotNull('preset')
                                                 ->where('name', $preset_category->name)
                                                 ->first();
                    $team_category = $template_category->replicate();
                    $team_category->team_id = $team->id;
                    $team_category->preset = null;
                    $team_category->save();
                }
            }

            CCIssueGroup::where('campaignID', $campaign_id)
                              ->chunk(100, function ($cc_issue_groups) use (&$count, $category_ig, &$team) {
                                  echo date('Y-m-d h:i:s')." =============================> Running $count...\n";
                                  $count += 100;
                                  foreach ($cc_issue_groups as $cc_i_group) {
                                      $team = Team::where('old_cc_id', $cc_i_group->campaignID)->first();

                                      if (! $team) {
                                          continue;
                                      }
                                      echo date('Y-m-d h:i:s').' '.$team->name.': Starting group '.$cc_i_group->category_name;

                                      $group = Group::where('old_cc_id', $cc_i_group->categoryID)->first();
                                      if (! $group) {
                                          $group = new Group;
                                          $group->team_id = $team->id;
                                          $group->old_cc_id = $cc_i_group->categoryID;
                                          $group->name = $cc_i_group->category_name;
                                          $group->notes = $cc_i_group->category_notes;
                                          // $group->category_id = $category_ig->id;
                                          $group->category_id = Category::where('team_id', $team->id)
                                                      ->where('name', 'Issue Groups')
                                                      ->first()->id;

                                          $groupinfo = [];
                                          $group->additional_info = $groupinfo;

                                          if ($tempdate = dateIsClean($cc_i_group->create_date)) {
                                              $group->created_at = $tempdate;
                                          }
                                          if ($tempdate = dateIsClean($cc_i_group->update_date)) {
                                              $group->updated_at = $tempdate;
                                          }
                                          $group->save();
                                      }

                                      if (! $team->account->billygoat_id && $cc_i_group->assignments()->count() > 500) {
                                          echo 'Skipping Group Assignment on Large Group ('.$cc_i_group->assignments()->count().') for '.$team->name."\n";
                                          continue;
                                      }

                                      if ($cc_i_group->assignments()->count() > 5000) {
                                          echo 'WAY TOO LARGE GROUP ('.$cc_i_group->assignments()->count().') for '.$team->name."\n";
                                          continue;
                                      }

                                      echo ' ('.$cc_i_group->assignments()->count().")\n";

                                      $cc_i_group->assignments()->with('ccVoter')
                               ->chunk(1000, function ($cc_assignments) use ($team, $group) {
                                   foreach ($cc_assignments as $cc_assignment) {
                                       $person = Person::where('team_id', $team->id)
                                            ->where('old_cc_id', $cc_assignment->voterID)->first();
                                       if (! $person) {
                                           //dd($cc_assignment);
                                           $ccvoter = $cc_assignment->ccVoter;
                                           if (! $ccvoter) {
                                               $ccvoter = CCVoterArchive::find($cc_assignment->voterID);
                                           }
                                           if (! $ccvoter) {
                                               echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_assignment->voterID."\n";

                                               continue;
                                           }
                                           if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                                               $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                                           } else {
                                               $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                                           }
                                       }
                                       if (! $person) {
                                           // Double check remote voters table

                                           $person = $ccvoter->createAndReturnPerson();
                                       }
                                       if (! $person) {
                                           $archived = CCVoterArchive::where('voter_code', $ccvoter->voter_code)->first();
                                           if ($archived) {
                                               $person = $archived->createAndReturnPerson();
                                           }
                                       }
                                       if (! $person) {
                                           echo date('Y-m-d h:i:s')." Couldn't find ".$ccvoter->voter_code."\n";
                                           continue;
                                       }
                                       $person->team_id = $team->id;

                                       if ($tempdate = dateIsClean($cc_assignment->update_date)) {
                                           $person->updated_at = $tempdate;
                                       }
                                       if ($tempdate = dateIsClean($cc_assignment->create_date)) {
                                           $person->created_at = $tempdate;
                                       }

                                       $person->save();

                                       $groupperson = GroupPerson::where('person_id', $person->id)
                                                      ->where('group_id', $group->id)
                                                      ->where('team_id', $team->id)
                                                      ->first();
                                       if (! $groupperson) {
                                           $groupperson = new GroupPerson;
                                           $groupperson->team_id = $team->id;
                                           $groupperson->group_id = $group->id;
                                           $groupperson->person_id = $person->id;

                                           // $groupperson->position = $cc_assignment->position;
                                           // TWO KINDS OF "POSITIONS"
                                           if (in_array($cc_assignment->position,
                                            ['Undecided', 'Opposed', 'Concerned', 'Supports'])) {
                                               $groupperson->position = $cc_assignment->position;
                                           } else {
                                               $groupperson->title = ($cc_assignment->position) ? $cc_assignment->position : null;
                                           }

                                           // $groupperson->data = $cc_assignment->notes;
                                           $groupperson->notes = ($cc_assignment->notes) ? $cc_assignment->notes : null;

                                           if ($tempdate = dateIsClean($cc_assignment->update_date)) {
                                               $groupperson->updated_at = $tempdate;
                                           }
                                           if ($tempdate = dateIsClean($cc_assignment->create_date)) {
                                               $groupperson->created_at = $tempdate;
                                           }
                                           $groupperson->save();
                                       }
                                   }
                               });
                                  }
                              });

            // ===============================================================> CONSTITUENT GROUPS

            echo date('Y-m-d h:i:s').' '.$team->name.": About to add Constituent Groups\n";

            $count = 0;
            CCConstituentGroup::where('campaignID', $campaign_id)
                              ->chunk(100, function ($cc_constituent_groups) use (&$count, $category_cg, &$team) {
                                  echo date('Y-m-d h:i:s')." =============================> Running $count...\n";
                                  $count += 100;
                                  foreach ($cc_constituent_groups as $cc_c_group) {
                                      //echo "Starting group ".$cc_c_group->group_name."\n";

                                      if (! $team) {
                                          continue;
                                      }
                                      echo date('Y-m-d h:i:s').' '.$team->name.': Starting Constituent Group '.$cc_c_group->group_name;

                                      $group = Group::where('old_cc_id', $cc_c_group->groupID)->first();
                                      if (! $group) {
                                          $group = new Group;
                                          $group->team_id = $team->id;
                                          $group->old_cc_id = $cc_c_group->groupID;
                                          $group->name = $cc_c_group->group_name;
                                          $group->notes = $cc_c_group->group_notes;
                                          // $group->category_id = $category_cg->id;
                                          $group->category_id = Category::where('team_id', $team->id)
                                                      ->where('name', 'Constituent Groups')
                                                      ->first()->id;

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
                                      }

                                      //echo "Starting Group Assignments ".$cc_c_group->group_name."\n";

                                      if (! $team->account->billygoat_id && $cc_c_group->assignments()->count() > 500) {
                                          echo 'NOT Skipping Group Assignment on Large Group ('.$cc_c_group->assignments()->count().') for '.$team->name."\n";
                                          //continue;
                                      }

                                      if ($cc_c_group->assignments()->count() > 5000) {
                                          echo 'WAY TOO LARGE GROUP ('.$cc_c_group->assignments()->count().') for '.$team->name."\n";
                                          continue;
                                      }

                                      echo ' ('.$cc_c_group->assignments()->count().")\n";

                                      $cc_c_group->assignments()->with('ccVoter')
                               ->chunk(1000, function ($cc_assignments) use ($team, $group) {
                                   foreach ($cc_assignments as $cc_assignment) {
                                       $person = Person::where('team_id', $team->id)
                                            ->where('old_cc_id', $cc_assignment->voterID)->first();
                                       if (! $person) {
                                           //dd($cc_assignment);
                                           $ccvoter = $cc_assignment->ccVoter;
                                           if (! $ccvoter) {
                                               $ccvoter = CCVoterArchive::find($cc_assignment->voterID);
                                           }
                                           if (! $ccvoter) {
                                               echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_assignment->voterID."\n";

                                               continue;
                                           }
                                           if (Str::startsWith($ccvoter->voter_code, 'BG') || Str::startsWith($ccvoter->voter_code, 'NE')) {
                                               $person = findPersonOrImportVoter($ccvoter->voter_code, $team->id);
                                           } else {
                                               $person = findPersonOrImportVoter('MA_'.$ccvoter->voter_code, $team->id);
                                           }
                                       }
                                       if (! $person) {
                                           // Double check remote voters table

                                           $person = $ccvoter->createAndReturnPerson();
                                       }
                                       if (! $person) {
                                           $archived = CCVoterArchive::where('voter_code', $ccvoter->voter_code)->first();
                                           if ($archived) {
                                               $person = $archived->createAndReturnPerson();
                                           }
                                       }
                                       if (! $person) {
                                           echo date('Y-m-d h:i:s')." Couldn't find ".$ccvoter->voter_code."\n";
                                           continue;
                                       }
                                       $person->team_id = $team->id;

                                       if ($tempdate = dateIsClean($cc_assignment->update_date)) {
                                           $person->updated_at = $tempdate;
                                       }
                                       if ($tempdate = dateIsClean($cc_assignment->create_date)) {
                                           $person->created_at = $tempdate;
                                       }

                                       $person->save();

                                       $groupperson = GroupPerson::where('person_id', $person->id)
                                                      ->where('group_id', $group->id)
                                                      ->where('team_id', $team->id)
                                                      ->first();
                                       if (! $groupperson) {
                                           $groupperson = new GroupPerson;
                                           $groupperson->team_id = $team->id;
                                           $groupperson->group_id = $group->id;
                                           $groupperson->person_id = $person->id;

                                           // $groupperson->position = $cc_assignment->position_held;
                                           // TWO KINDS OF "POSITIONS"
                                           if (in_array($cc_assignment->position_held,
                                            ['Undecided', 'Opposed', 'Concerned', 'Supports'])) {
                                               $groupperson->position = $cc_assignment->position_held;
                                           } else {
                                               $groupperson->title = ($cc_assignment->position_held) ? $cc_assignment->position_held : null;
                                           }

                                           // $groupperson->data = $cc_assignment->notes;
                                           $groupperson->notes = ($cc_assignment->notes) ? $cc_assignment->notes : null;

                                           if ($tempdate = dateIsClean($cc_assignment->update_date)) {
                                               $groupperson->updated_at = $tempdate;
                                           }
                                           if ($tempdate = dateIsClean($cc_assignment->create_date)) {
                                               $groupperson->created_at = $tempdate;
                                           }
                                           $groupperson->save();
                                       }
                                   }
                               });
                                  }
                              });
        }
    }
}
