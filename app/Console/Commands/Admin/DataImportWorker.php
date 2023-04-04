<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin\DataWorker;
use App\User;
use Illuminate\Console\Command;

class DataImportWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'st:work {--c|continue}';
    protected $signature = 'st:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Background process for data imports';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->run = true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (config('app.env') == 'LOCAL') {
            // PHP 7.0 and before can handle asynchronous signals with ticks
            declare(ticks=1);

            // PHP 7.1 and later can handle asynchronous signals natively
            pcntl_async_signals(true);

            pcntl_signal(SIGINT, [$this, 'shutdown']); // Call $this->shutdown() on SIGINT
            pcntl_signal(SIGTERM, [$this, 'shutdown']); // Call $this->shutdown() on SIGTERM
        }

        $this->info('Worker started -- use Cntrl-C to stop');

        $worker = new DataWorker;
        $worker->save();
        $id = $worker->id;

        try {
            while ($this->run) {

                //need this because in while loop
                $worker = DataWorker::withTrashed()->where('id', $id)->first();

                $jobs_remaining = $worker->work();

                if (($jobs_remaining <= 0) || ($worker->trashed())) {
                    $this->run = false;
                } elseif ($jobs_remaining == 'INTERRUPT') {
                    $worker->markInterrupted();
                    die();
                }
            }
            $worker->delete();
            $this->info('Worker stopped');
        } catch (\Exception $e) {
            $worker->log .= "\n ".$e->getMessage();
            $this->info('Worker error: '.$e->getMessage());
        }

        User::find(1)->addMemory('worker', 0);
    }

    public function shutdown()
    {
        $this->info('Gracefully stopping worker...');
        // When set to false, worker will finish current item and stop.
        $this->run = false;
    }
}
