<?php

namespace App\Traits;

use Auth;
use Carbon\Carbon;
use Schema;

use App\UserUpload;
use App\Import;
use App\Team;


trait LivewirePasteTrait
{
    public $state;

    public $process;
    public $chunk;

    public $start;
    public $stop;
    public $elapsed;
    public $count;
    public $time_remaining;

    public $model;              // Model can be: Upload, Import, etc.
    public $model_class;
    public $model_options;
    public $model_new_name;
    public $selected_model;

    public $initial_results_categories;
    public $results = [];

    public $filters = [];
    public $new_filter_column;
    public $new_filter_type;
    public $new_filter_value;

    public $textarea;
    public $remaining;
    public $lines;

    public $visible_chunk = [];
    public $delimiter;

    public $template;
    public $map = [];

    public $error;
    public $available_states;
    

    //------------------------------------------------------------------------------------

    public function traitMount()
    {
        $this->state = session('team_state');

        $this->available_states = Team::all()->pluck('data_folder_id')
                                             ->unique()
                                             ->reject(function ($state) {
                                                return !Schema::connection('voters')->hasTable('x_voters_'.$state.'_master');
                                             });
    }

    public function buildMap($fields)
    {
        foreach($fields as $field) {
            $this->map[$field] = null;
        }
    }

    public function buildVisibleChunk()
    {
        if (!$this->process) {
            $first_chunk = collect(explode("\n", $this->textarea))->take(10);
            $arr = [];
            foreach ($first_chunk as $line) {
                $arr[] = explode($this->delimiter, $line);
            }
            $this->visible_chunk = $arr;
        }
    }

    public function clearMapAndVisibleChunk()
    {
        $this->remaining = null;
        $this->visible_chunk = [];

        foreach($this->map as $key => $value) {
            $this->map[$key] = null;
        }
    }

    public function guessMapIndexes($template = null)
    {            
        if ($template) {
            foreach ($this->template as $key => $field) {
                $this->map[$field] = $key;
            }
        }

        if (!$this->map['voter_id'] && $this->map['voter_id'] != 'ignore') {
            if ($this->visible_chunk) {
                $likely = [];
                foreach ($this->visible_chunk as $line_num => $line) {
                    if ($line_num < 1) {
                        continue;
                    }
                    foreach ($line as $index => $val) {
                        if ($this->validVoterID($val)) {
                            if (isset($likely[$index])) {
                                $likely[$index] += 1;
                            } else {
                                $likely[$index] = 1;
                            }
                            
                        }
                    }
                }
                arsort($likely);
                $this->map['voter_id'] = array_key_first($likely);
            }
        }

        if (!$this->map['email'] && $this->map['email'] != 'ignore') {
            if ($this->visible_chunk) {
                $likely = [];
                foreach ($this->visible_chunk as $line_num => $line) {
                    foreach ($line as $index => $val) {
                        if (filter_var(trim($val), FILTER_VALIDATE_EMAIL)) {
                            if (isset($likely[$index])) {
                                $likely[$index] += 1;
                            } else {
                                $likely[$index] = 1;
                            }
                            
                        }
                    }
                }
                arsort($likely);
                $this->map['email'] = array_key_first($likely);
            }
        }
        if (!$this->map['phone'] && $this->map['phone'] != 'ignore') {
            if ($this->visible_chunk) {
                $likely = [];
                foreach ($this->visible_chunk as $line_num => $line) {
                    foreach ($line as $index => $val) {
                        $val = preg_replace('/[^0-9]/', '', $val);
                        if (strlen($val) == 10) {
                            if (isset($likely[$index])) {
                                $likely[$index] += 1;
                            } else {
                                $likely[$index] = 1;
                            }
                            
                        }
                    }
                }
                arsort($likely);
                $this->map['phone'] = array_key_first($likely);
            }
        }
    }

    //------------------------------------------------------------------------------------

    public function start()
    {
        $this->process = true;
        $this->start = now();
        $this->count = 0;
    }

    public function halt()
    {
        $this->process = false;
        $this->stop = now();
        $this->time_remaining = null;
    }

    public function calculateElapsed()
    {
        if ($this->process) {
            $this->elapsed = Carbon::parse($this->start)->diffInSeconds(Carbon::now());
            if ($this->elapsed > 0) {
                $rate = $this->count/$this->elapsed;
                $this->time_remaining = $this->lines / $rate;
            }
        } else {
            $this->elapsed = Carbon::parse($this->start)->diffInSeconds($this->stop);
        }

    }

    public function haltIfNoLinesRemain()
    {
        if ($this->lines <= 0) {
            $this->halt();
        }
    }

    public function calculateNumberOfLinesRemaining()
    {
        if ($this->remaining) {
            $this->lines = $this->remaining->count();
        } else {
            $this->lines = 0;
        }
    }

    public function setInitialResultsCategoriesIfUnset()
    {
        if ($this->model && !isset($this->results[$this->model->id])) {
            $this->results[$this->model->id]  = $this->initial_results_categories;
        }
    }

    //------------------------------------------------------------------------------------

    public function addFilter()
    {
        $filter_arr = [
            'column' => $this->new_filter_column,
            'type' => $this->new_filter_type, 
            'value' => $this->new_filter_value,
        ];
        $this->filters[] = $filter_arr;
        $this->new_filter_column = null;
        $this->new_filter_type = null; 
        $this->new_filter_value = null;
    }

    public function deleteFilter($f)
    {
        unset($this->filters[$f]);
    }

    public function unselectModel()
    {
        $this->model = null;
        $this->model_new_name = null;
        $this->selected_upload = null;
        $this->process = false;
        $this->error = false;
    }

    //------------------------------------------------------------------------------------

    public function validVoterID($val)
    {
        $val = trim($val);
        if (strlen($val) != 12) return false;
        return preg_match('/'.'([0-9]{2}[A-Z]{3}[0-9]{7})'.'/i', $val); // e.g. 03GBE1918000
    }

    //------------------------------------------------------------------------------------

    // public function detectDelimiter($file_path)
    // {
    //     $file = new \SplFileObject($file_path, 'r');

    //     $delimiter = ',';
    //     $english = ["\t" => 'TAB',
    //                 ';'  => 'SEMICOLON',
    //                 '|'  => 'BAR',
    //                 ','  => 'COMMA'];

    //     $possibilities = ["\t", ';', '|', ','];
    //     $data_1 = [];
    //     $data_2 = [];

    //     foreach ($possibilities as $d) {
    //         $data_1 = $file->fgetcsv($d);
    //         if (count($data_1) > count($data_2)) {
    //             $delimiter = count($data_1) > count($data_2) ? $d : $delimiter;
    //             $data_2 = $data_1;
    //         }
    //         $file->rewind();
    //     }

    //     if (!$delimiter) {
    //         dd('Error - could not figure out delimiter');
    //     } else {
    //         $this->info('Delimiter detected: --> '.$english[$delimiter].' <--');
    //     }
        
    //     return $delimiter;
    // }

    //------------------------------------------------------------------------------------

    public function processChunk()
    {
        $this->textarea = trim($this->textarea);

        $this->setInitialResultsCategoriesIfUnset();

        if (!$this->textarea) {

            $this->clearMapAndVisibleChunk();

        } else {

            $this->remaining  = collect(explode("\n", $this->textarea));

            $this->buildVisibleChunk();

        }

        if (!$this->process) {

            $this->guessMapIndexes($this->template);

        }

        if ($this->process) {

            $lines_to_process       = $this->remaining->slice(0, $this->chunk);
            $successfully_processed = 0;
            $this->error            = false;

            foreach($lines_to_process as $line) {

                try {

                    $this->processLine($line);  // processLine() is in the Component
                    $this->count++;
                    $successfully_processed++;

                } catch (\Exception $e) {

                    $this->error = 'Error: '.$e->getMessage().' '.$line;
                    break;
                    
                }

            }

            $this->remaining    = $this->remaining->slice($successfully_processed);
            $this->textarea = implode("\n", $this->remaining->toArray());

        }

        $this->calculateNumberOfLinesRemaining();

        $this->haltIfNoLinesRemain();

        $this->calculateElapsed();



        if ($this->error) {
            $this->halt();
        }

    }

    public function runFiltersOnColumns($cols)
    {
        foreach ($this->filters as $filter_array) {

            $pass = true;

            $filter_column  = $filter_array['column'];
            $filter_type    = $filter_array['type'];
            $filter_value   = $filter_array['value'];

            if ($filter_type == 'matches') {
                if ($cols[$filter_column] != $filter_value) {
                    $pass = false;
                }
            }

            if (!$pass) {
                return false;
            }
        }

        return true;
    }

    public function resultsIncrement($index, $success)
    {
        $this->results[$this->model->id][$index] = $this->results[$this->model->id][$index] + $success;
    }

}