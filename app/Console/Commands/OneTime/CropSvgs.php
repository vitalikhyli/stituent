<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;

class CropSvgs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:crop_svgs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crops space around town and city municipality svgs';

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
     * @return int
     */
    public function handle()
    {

        $root = 'https://communityfluency.com/images/municipalities/original/';
        $path = public_path().'/images/municipalities/';

        $images = glob(public_path().'/images/municipalities/original/*.svg');
        
        foreach ($images as $image) {
            
            $url = $root.basename($image);
            $town = ucwords(str_replace(['-', '.svg'], [' ', ''], basename($image)));
            //dd($url);
            $command = 'curl -X POST -H "Content-Type: application/json" -d "{\"url\": \"'.$url.'\", \"title\": \"'.$town.'\"}" https://autocrop.cncf.io/autocrop';

            //echo $command;
            //dd();
            $output = shell_exec($command);

            $results = json_decode($output);
            if ($results->success == true) {
                file_put_contents($path.basename($image), $results->result);
            } else {
                dd($results, 'NO');
            }
        }

        return Command::SUCCESS;
    }
}
