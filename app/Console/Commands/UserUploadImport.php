<?php

namespace App\Console\Commands;

use App\UserUpload;
use App\UserUploadData;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UserUploadImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:user_upload_import {--upload_id=} {--team_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes a user-uploaded file and puts it into voter_userdata table';

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
        $sleep = 0; //250000;

        $upload_id = $this->option('upload_id');
        $team_id = $this->option('team_id');

        $upload = UserUpload::find($upload_id);

        $full_path = storage_path().'/app/'.$upload->file;
        $delimiter = ',';

        $handle = fopen($full_path, 'r');
        $result = fgets($handle);
        $delimiter = ',';
        if (Str::contains($result, '|')) {
            $delimiter = '|';
        }

        $handle = fopen($full_path, 'r');
        $count = 0;
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $count++;
            if ($count == 1) {
                continue;
            }
            $voter = new UserUploadData;
            $voter->team_id = $team_id;
            $voter->upload_id = $upload->id;
            $voter->line = $count;
            $voter->data = $data;
            $voter->hash = md5(implode($delimiter, $data));
            $voter->save();

            $upload->imported_count++;
            $upload->save();
            usleep($sleep);
        }
        fclose($handle);
    }
}
