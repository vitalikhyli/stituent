<?php

namespace App\Console\Commands\Seeding;

use App\CaseFile;
use App\Directory;
use App\Group;
use App\GroupFile;
use App\Models\CC\CCCampaign;
use App\Models\CC\CCFileCase;
use App\Models\CC\CCFileGroup;
use App\Models\CC\CCUser;
use App\Models\CC\CCVoterArchive;
use App\Person;
use App\PersonFile;
use App\Team;
use App\User;
use App\WorkCase;
use App\WorkFile;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Files extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_files {--campaign=} {--login=}';

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
        if ($this->option('campaign')) {
            $valid_campaign_ids = [$this->option('campaign')];
            echo date('Y-m-d h:i:s').' Starting Contacts with Campaign ID '.$this->option('campaign')."\n";
        } elseif ($this->option('login')) {
            $login = $this->option('login');
            $cc_user = CCUser::where('login', $login)->first();
            $valid_campaign_ids = [$cc_user->campaignID];
        } else {
            echo "No campaign";
            return;
            $valid_campaign_ids = Team::whereAppType('office')
                                      ->pluck('old_cc_id')
                                      ->unique();
            echo date('Y-m-d h:i:s').' Starting Files with '.count($valid_campaign_ids)." Campaigns\n";
        }
        //dd($valid_campaign_ids);

        foreach ($valid_campaign_ids as $campaign_id) {
            $team = Team::whereAppType('office')->where('old_cc_id', $campaign_id)->first();

            $main_dir = Directory::where('name', $team->name."'s folder")->first();
            if (! $main_dir) {
                $main_dir = new Directory;
                $main_dir->team_id = $team->id;
                $main_dir->name = $team->name."'s folder";
                $main_dir->save();
            }

            $cc_casefiles = CCFileCase::where('campaignID', $campaign_id)->get();

            foreach ($cc_casefiles as $cc_casefile) {
                //dd($cc_file);
                $original_folder = '/home/capitcj5/public_html/secure/html/cms/';
                $local_folder = storage_path().'/mnt/cc_files/';
                $local_path = str_replace($original_folder, $local_folder, $cc_casefile->doc_link);

                $teamfolder = $team->user_files_folder;

                $new_path = $teamfolder.'/'.$cc_casefile->file_name;

                //dd($new_path, $local_path);

                if (! file_exists($new_path)) {
                    if (file_exists($local_path)) {
                        $cp_command = 'cp "'.$local_path.'" "'.$new_path.'"';
                        echo "$cp_command\n";
                        shell_exec($cp_command);
                    } else {
                        echo 'MISSING file '.$local_path."\n";
                    }
                }

                //dd($case);
                //$padded_file_id = str_pad($this->id, 6, '0', STR_PAD_LEFT);

                $file = WorkFile::where('team_id', $team->id)
                                ->where('old_cc_id', $cc_casefile->docID)
                                ->first();
                if (! $file) {
                    $file = new WorkFile;
                    $file->directory_id = $main_dir->id; //ADDED THIS
                    $file->team_id = $team->id;
                    $user = User::where('current_team_id', $team->id)
                                ->where('username', '=', $cc_casefile->create_login)
                                ->first();
                    if (! $user) {
                        $user = User::where('current_team_id', $team->id)->first();
                        if (! $user) {
                            echo $cc_casefile->create_login.' not found, campaign ID '.$cc_casefile->campaignID."\n";
                            continue;
                        }
                    }
                    $file->user_id = $user->id;
                    $file->name = $cc_casefile->file_name;
                    if ($cc_casefile->short_desc) {
                        $file->description = $cc_casefile->short_desc;
                    }

                    $userpath = storage_path().'/app/'.config('app.user_upload_dir');
                    $userpath_no_slash = substr($userpath, 1, strlen($userpath));
                    $new_path = str_replace([$userpath, $userpath_no_slash], '', $new_path);
                    $new_path = str_replace('//', '/', $new_path);
                    $file->path = $new_path;

                    $file->old_cc_id = $cc_casefile->docID;

                    if (! $file->updated_at || $file->updated_at > Carbon::yesterday()) {
                        if ($tempdate = dateIsClean($cc_casefile->update_date)) {
                            $file->updated_at = $tempdate;
                        }
                    }
                    if (! $file->created_at || $file->created_at > Carbon::yesterday()) {
                        if ($tempdate = dateIsClean($cc_casefile->create_date)) {
                            $file->created_at = $tempdate;
                        }
                    }

                    $file->save();
                    echo 'Saved file '.$file->name."\n";
                }

                $case = WorkCase::where('old_cc_id', $cc_casefile->contact_issueID)
                                ->first();

                if ($case) {
                    $casefile = CaseFile::where('file_id', $file->id)
                                        ->where('case_id', $case->id)
                                        ->first();
                    if (! $casefile) {
                        $casefile = new CaseFile;
                        $casefile->team_id = $team->id;
                        $casefile->file_id = $file->id;
                        $casefile->case_id = $case->id;
                        $casefile->save();
                    }
                } else {
                    $person = Person::where('team_id', $team->id)
                                    ->where('old_cc_id', $cc_casefile->voterID)->first();

                    if (! $person) {
                        //dd($cc_casefile);
                        $ccvoter = $cc_casefile->ccVoter;
                        if (! $ccvoter) {
                            $ccvoter = CCVoterArchive::find($cc_casefile->voterID);
                        }
                        if (! $ccvoter) {
                            echo date('Y-m-d h:i:s')." Couldn't find Voter ID ".$cc_casefile->voterID."\n";

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

                    if (! $person->updated_at || $person->updated_at > Carbon::yesterday()) {
                        if ($tempdate = dateIsClean($cc_casefile->update_date)) {
                            $person->updated_at = $tempdate;
                        }
                    }
                    if (! $person->created_at || $person->created_at > Carbon::yesterday()) {
                        if ($tempdate = dateIsClean($cc_casefile->create_date)) {
                            $person->created_at = $tempdate;
                        }
                    }

                    $person->save();

                    $personfile = PersonFile::where('file_id', $file->id)
                                        ->where('person_id', $person->id)
                                        ->first();
                    if (! $personfile) {
                        $personfile = new PersonFile;
                        $personfile->team_id = $team->id;
                        $personfile->file_id = $file->id;
                        $personfile->person_id = $person->id;
                        $personfile->save();
                    }
                    // Likely a default case, attach directly to person
                }
            }
            //dd($cc_casefiles);

            $cc_groupfiles = CCFileGroup::where('campaignID', $campaign_id)->get();

            foreach ($cc_groupfiles as $cc_groupfile) {
                //dd($cc_file);
                $original_folder = '/home/capitcj5/public_html/secure/html/cms/';
                $local_folder = storage_path().'/mnt/cc_files/';
                $local_path = str_replace($original_folder, $local_folder, $cc_groupfile->doc_link);

                $teamfolder = $team->user_files_folder;

                $group = Group::where('old_cc_id', $cc_groupfile->categoryID)
                                ->first();

                if ($group) {
                    //dd($case);
                    //$padded_file_id = str_pad($this->id, 6, '0', STR_PAD_LEFT);
                    $new_path = $teamfolder.'/'.$cc_groupfile->file_name;

                    if (! file_exists($new_path)) {
                        if (file_exists($local_path)) {
                            $cp_command = 'cp "'.$local_path.'" "'.$new_path.'"';
                            echo "$cp_command\n";
                            shell_exec($cp_command);
                        } else {
                            'MISSING file '.$local_path."\n";
                        }
                    }

                    $file = WorkFile::where('team_id', $team->id)
                                    ->where('old_cc_id', $cc_groupfile->docID)
                                    ->first();
                    if (! $file) {
                        $file = new WorkFile;
                        $file->directory_id = $main_dir->id; //ADDED THIS
                        $file->team_id = $team->id;
                        $user = User::where('current_team_id', $team->id)
                                    ->where('username', '=', $cc_groupfile->create_login)
                                    ->first();
                        if (! $user) {
                            $user = User::where('current_team_id', $team->id)->first();
                            if (! $user) {
                                echo $cc_groupfile->create_login.' not found, campaign ID '.$cc_groupfile->campaignID."\n";
                                continue;
                            }
                        }
                        $file->user_id = $user->id;
                        $file->name = $cc_groupfile->file_name;
                        if ($cc_groupfile->short_desc) {
                            $file->description = $cc_groupfile->short_desc;
                        }

                        $file->path = $new_path;
                        $file->old_cc_id = $cc_groupfile->docID;

                        if (! $file->updated_at || $file->updated_at > Carbon::yesterday()) {
                            if ($tempdate = dateIsClean($cc_groupfile->update_date)) {
                                $file->updated_at = $tempdate;
                            }
                        }
                        if (! $file->created_at || $file->created_at > Carbon::yesterday()) {
                            if ($tempdate = dateIsClean($cc_groupfile->create_date)) {
                                $file->created_at = $tempdate;
                            }
                        }

                        $file->save();
                        echo 'Saved file '.$file->name."\n";
                    }

                    $groupfile = GroupFile::where('file_id', $file->id)
                                        ->where('group_id', $group->id)
                                        ->first();
                    if (! $groupfile) {
                        $groupfile = new GroupFile;
                        $groupfile->team_id = $team->id;
                        $groupfile->file_id = $file->id;
                        $groupfile->group_id = $group->id;
                        $groupfile->save();
                    }
                }
            }

            //dd($cc_casefiles);
        }
    }
}
