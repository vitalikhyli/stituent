<?php

namespace App\Console\Commands;

use App\WorkFile;
use Illuminate\Console\Command;

class FixFilePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:fix_file_paths';

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
        $files = WorkFile::all();

        foreach ($files as $file) {
            $userpath = storage_path().'/app/'.config('app.user_upload_dir');
            $userpath_no_slash = substr($userpath, 1, strlen($userpath));
            $path = str_replace([$userpath, $userpath_no_slash], '', $file->path);
            $path = str_replace('//', '/', $path);
            $file->path = $path;
            //dd($path);
            $file->save();

            if (! file_exists($userpath.$path)) {
                echo 'Deleting '.$file->name."\n";
                $file->delete();
            } else {
                echo 'KEEPING '.$file->name."\n";
            }
        }
    }
}
