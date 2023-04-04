<?php

namespace App\Console\Commands;

use App\Account;
use Illuminate\Console\Command;

class BillyGoat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:billygoat';

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
        if (config('app.env') == 'local') {
            $domain = config('app.billygoat_local');
        } else {
            $domain = config('app.billygoat_url');
        }
        $domain = config('app.billygoat_url');

        $url = $domain.'/api/'.config('app.billygoat_api_key').'/clients/paid';

        $result = @file_get_contents($url);

        if (! $result) {
            echo $url." Error\r\n";
        } else {
            $data = json_decode($result, true);

            foreach ($data as $client) {
                $cf_account = Account::where('billygoat_id', $client['id'])->first();

                if ($cf_account) {
                    echo 'Found: '.$cf_account->name."\r\n";

                    $cf_account->billygoat_data = $client;
                    $cf_account->save();
                // Do something with $client[outstanding_balance] and $client[paid_through_date]
                } else {
                    echo 'Not found: '.$client['business_name']."\r\n";
                }
            }
        }
    }
}
