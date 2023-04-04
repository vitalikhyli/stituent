<?php

namespace App\Jobs\DataImport;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportCSVLine implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $csvLine;
    protected $count;
    protected $header;
    protected $extra;
    protected $skip_first;
    protected $table_bench;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($csvLine, $header, $extra, $skip_first, $count, $table_bench)
    {
        $this->csvLine = $csvLine;
        $this->count = $count;
        $this->header = $header;
        $this->extra = $extra;
        $this->skip_first = $skip_first;
        $this->table_bench = $table_bench;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ((! $this->count) && ($this->skip_first)) {

                //Skip First Row
        } else {
            $sql_cols = '';
            $sql_vals = '';

            foreach ($this->header as $key => $value) {
                if ($value != 'do_not_import') {
                    if (substr($value, 0, 10) == '{DATETIME}') {
                        $value = trim(substr($value, 11, strlen($value)));
                        $sql_cols .= $value;
                        $sql_cols .= ', ';
                        $sql_vals .= '"'
                                      .Carbon::parse($this->csvLine[$key])->format('Y-m-d')
                                      .'"';
                        $sql_vals .= ', ';
                    } else {
                        $sql_cols .= $value;
                        $sql_cols .= ', ';
                        $sql_vals .= '"'.$this->csvLine[$key].'"';
                        $sql_vals .= ', ';
                    }
                }
            }

            foreach ($this->extra as $key => $value) {
                $sql_cols .= $key;
                $sql_cols .= ', ';
                $sql_vals .= '"'.$value.'"';
                $sql_vals .= ', ';
            }

            $sql_cols = substr($sql_cols, 0, strlen($sql_cols) - 2);
            $sql_vals = substr($sql_vals, 0, strlen($sql_vals) - 2);
            $sql_cols = '('.$sql_cols.')';
            $sql_vals = '('.$sql_vals.')';
            $sql = 'INSERT INTO '.$this->table_bench.' '.$sql_cols.' VALUES '.$sql_vals;

            DB::statement($sql);
        }
    }
}
