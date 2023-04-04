<?php

namespace App\Traits\Admin;

use Carbon\Carbon;
use Illuminate\Support\Str;

use DB;
use Schema;
use Illuminate\Database\Schema\Blueprint;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


 trait FileProcessingTrait
 {
    public $bar_len = 36;

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // PROMPTS / SELECTIONS
    //

    public function basketOfItems($prompt, $which_choices) {

        $done_option = "GO!";

        $which_choices[count($which_choices) - 1] = end($which_choices)."\n";

        $which_choices = $this->rekeyStartingAtOne($which_choices);

        $which_choices[0] = $done_option;

        $basket        = [];
        $choose_commands        = true;
        $current_command_list   = null;

        while ($choose_commands = true) {

            if ($basket) {
                $current_command_list = "\n  * ADDED: ".implode("\n  * ADDED: ", $basket);
            }

            $which = $this->choice($prompt.$current_command_list."\n", $which_choices);

            $which_key = array_search($which, $which_choices);

            if ($which !== $done_option) {
                $basket[$which_key] = $which;
                ksort($basket);
            }

            unset($which_choices[$which_key]);

            if (count($which_choices) == 1)     break;
            if ($which == $done_option)         break;
            
        }

        return $basket;

    }

    public function selectFilePath($storage_subdir, $what_for)
    {
        $folder_path = storage_path().$storage_subdir;

        if (!file_exists($folder_path)) {
            mkdir($folder_path);
            echo "Created Folder $folder_path, but no file exists.";
            exit;
        }

        $file_list = collect(scandir(storage_path().$storage_subdir, true));
        
        $file_list = $file_list->reject(function ($item) {
            if ($item == '.') return true;
            if ($item == '..') return true;
        })->sort();
    
        if ($file_list->count() <= 0) {
            dd('There are no files in this directory yet.');
            return;
        }

        $file_list = collect($this->rekeyStartingAtOne($file_list->toArray()));

        $selected = false;

        while ($selected == false) {

            $file_name = $this->choice('What file to use for '.$what_for.'?'."\n"
                        .' Scanning /storage'.$storage_subdir, $file_list->toArray());

            $file_path = storage_path().$storage_subdir.'/'.$file_name;

            $confirm = $this->confirm('CONFIRM: Use "'.$file_name.'?"', true);

            if ($confirm) $selected = true;

        }

        return $file_path;
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // FILE PROCESSING
    //

    public function mapColumnNames($row, $map)
    {
        // Map the Column Names of the m_checkstatus(conn, identifier)ap array
        // onto the given Row instead of Key #s

        $csv = [];
        foreach($row as $key => $value) {
            $value = ($value == '') ? null : $value;
            if (isset($map[$key])) {
                $csv[$map[$key]] = $value;
            } else {
                $csv[$key] = $value;
            }
        }

        return $csv;
    }

    public function englishColumnNames($row, $firstrow)
    {
        // Map the English Column Names of the first row
        // onto the given Row instead of Key #s

        $csv = [];
        foreach($row as $key => $value) {
            $csv[$firstrow[$key]] = $value;
        }

        return $csv;
    }

    public function trimEachColumn($row)
    {
        return collect($row)->map(function ($item) {
                                                return trim($item);
                                            })
                            ->toArray();
    }

    public function createErrorLog($name)
    {
        $log = new Logger($name);
        $log_name = $name.'_'.Carbon::now()->format('Y-m-d_h-i').'.log';
        $log->pushHandler(new StreamHandler(storage_path().'/logs/master-errors/'.$log_name));
        $this->info('New Log: '.$log_name);
        return $log;
    }

    public function openHandleAndGoThrough($file_path, $state_function, $log)
    {   
        $handle             = fopen($file_path, "r");
        $row_num            = 0;
        $error_count        = 0;

        while (($raw_string = fgets($handle)) !== false) {

            $row_num++;
            if ($row_num == 1) continue;
            $row = str_getcsv($raw_string, $this->delimiter);

            try {

                echo $this->progress($row_num)."\r";

                $this->forEachRow($state_function, 
                                  $row, 
                                  $row_num); // This goes back to State command

            } catch (\Exception $e) {

                $this->error($e);
                $log->error($e->getMessage());
                $error_count++;

            }

        }

        fclose($handle);

        return ['last_row_num'  => $row_num,
                'error_count'   => $error_count];
    }

    public function massInsert($file_path, $state_function, $log, $chunksize)
    {   
        $handle             = fopen($file_path, "r");
        $row_num            = 0;
        $error_count        = 0;

        $collection = [];
        $class_name = null;

        while (($raw_string = fgets($handle)) !== false) {

            $row_num++;
            if ($row_num == 1) continue;
            $row = str_getcsv($raw_string, $this->delimiter);

            try {

                echo $this->progress($row_num)."\r";

                $model = $this->forEachRow($state_function, 
                                                  $row, 
                                                  $row_num); // This goes back to State command

                if (!$class_name) {
                    $class_name = get_class($model);
                }
                
                $modelArray = $model->toArray();
                if ($state_function == 'voters') {
                    unset($modelArray['voter_id']);
                }
                foreach ($modelArray as $key => $val) {
                    if (is_array($val)) {
                        $modelArray[$key] = json_encode($val);
                    }
                }
                //dd($model, $modelArray);
                $collection[] = $modelArray;
                //dd($collection);

                if ($row_num % $chunksize == 0) {
                    if (count($collection) > 0) {

                        $class_name::insert($collection);
                        //dd($collection, $class_name);
                        $collection = [];
                    }

                }
            } catch (\Exception $e) {
                foreach ($collection as $row) {
                    try {
                        $class_name::insert($row);
                    } catch (\Exception $e) {
                        foreach ($row as $col => $val) {
                            //$row[$col] = str_replace('É', 'E', $val);
                        }
                        //$class_name::insert($row);
                        $this->error($e);
                        $log->error("Row #: ".$row_num." - ".$e->getMessage());
                        $error_count++;
                    }
                }
                $collection = [];
                $this->error($e);
                $log->error($e->getMessage());
                $error_count++;

            }

            

        }

        fclose($handle);

        return ['last_row_num'  => $row_num,
                'error_count'   => $error_count];
    }

    public function progress($current)
    {
        if ($this->start_time) {

            $seconds = Carbon::now()->diffInSeconds($this->start_time);

            if ($seconds >= 86400) {
                $days = floor($seconds/86400);
                $new_seconds = $seconds - $days * 86400;
                $elapsed = $days.'d '.gmdate('H:i:s', $new_seconds);
            } else  {
                $elapsed = gmdate('H:i:s', $seconds);
            }

        } else {
            
            $elapsed = null;

        }

        if ($this->bar_len > 0 && $this->expected_num_rows > 0) {

            $per = $current/$this->expected_num_rows;
            $done_len = round($per * $this->bar_len);
            $bar = str_pad(str_repeat('*', $done_len), $this->bar_len, '_');

            if ($elapsed && $per > 0) {

                $remains = round((1 - $per)/$per * $seconds);

                if ($seconds >= 86400) {
                    $days = floor($seconds/86400);
                    $new_seconds = $seconds - $days * 86400;
                    $remains = $days.'d '.gmdate('H:i:s', $new_seconds);
                } else  {
                    $remains = gmdate('H:i:s', $remains);
                }
                
            } else {

                $remains = null;

            }

            return str_pad(
                    $bar.' '.number_format($per * 100).'% : '
                    .number_format($current).' of '.number_format($this->expected_num_rows)
                    .' '.$this->r1.$remains.$this->color_reset
                    .' '.$this->c1.$elapsed.$this->color_reset
                    ,
                    80, " ");
        } else {

            if (!$current) {
                return 'Process took: '.$this->c1.$elapsed.$this->color_reset;
            } else {
                return number_format($current).' -> '.$this->c1.$elapsed.$this->color_reset;
            }

        }

    }


    public function getFirstRow($file_path)
    {
        $file = new \SplFileObject($file_path, 'r');
        $firstrow = $file->fgetcsv($this->delimiter);
        if (!$firstrow)     dd('Error - could not get first row');
        return $this->trimEachColumn($firstrow);
    }

    public function detectDelimiter($file_path)
    {
        $file = new \SplFileObject($file_path, 'r');

        $delimiter = ',';
        $english = ["\t" => 'TAB',
                    ';'  => 'SEMICOLON',
                    '|'  => 'BAR',
                    ','  => 'COMMA'];

        $possibilities = ["\t", ';', '|', ','];
        $data_1 = [];
        $data_2 = [];

        foreach ($possibilities as $d) {
            $data_1 = $file->fgetcsv($d);
            if (count($data_1) > count($data_2)) {
                $delimiter = count($data_1) > count($data_2) ? $d : $delimiter;
                $data_2 = $data_1;
            }
            $file->rewind();
        }

        if (!$delimiter) {
            dd('Error - could not figure out delimiter');
        } else {
            $this->info('Delimiter detected: --> '.$english[$delimiter].' <--');
        }
        
        return $delimiter;
    }

    public function expectedNumRows($file_path)
    {
        echo 'Counting...';
        $expected_num_rows = 0;
        $handle = fopen($file_path, "r");
        while(!feof($handle)){
          $line_for_counting = fgets($handle);
          $expected_num_rows++;
        }
        fclose($handle);
        return $expected_num_rows;
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // DATABASE
    //

    public function checkHasTableAndNotEmpty($table)
    {
        if (
            !Schema::connection('voters')->hasTable($table) || 
            !DB::connection('voters')->table($table)->first()) {
            
            return false;

        } else {

            return true;
        }
    }

    public function truncateIfExists($table)
    {
        if (Schema::connection('voters')->hasTable($table)) {
            DB::connection('voters')->table($table)->truncate();
        }
    }   


    ////////////////////////////////////////////////////////////////////////////////////
    //
    // MISC
    //

    public function fullStateName($state)
    {
        $states = [
                    'MA' => 'Massachusetts',
                    'RI' => 'Rhode Island'
                  ];

        return (isset($states[$state])) ? $states[$state] : null;
    }

    public function rekeyStartingAtOne($list)
    {
        $rekeyed    = [];
        $key        = 1;

        foreach($list as $item) {
            $rekeyed[$key++] = $item;
        }

        return $rekeyed;
    }

    public function rekeyStartingAtZero($list)
    {
        $rekeyed    = [];
        $key        = 0;

        foreach($list as $item) {
            $rekeyed[$key++] = $item;
        }

        return $rekeyed;
    }

    public function correctZip($zip)
    {
        if (strlen($zip) == 4) return '0'.$zip;
        return $zip;
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // DISPLAY
    //

    public $c1 = "\e[44m";    // Console color blue
    public $r1 = "\e[41m";    // Red
    public $p1 = "\e[45m";    // Yellow
    public $color_reset = "\e[0m"; 

    public function showCommunityFluencyWordMark()
    {
        //https://www.patorjk.com/software/taag/#p=display&f=Ogre&t=Community%0AFluency
        
        echo "\n".str_repeat('=', 70)."\n";
        echo "   ____ _____   __  __           _            
  / ___|  ___| |  \/  | __ _ ___| |_ ___ _ __ 
 | |   | |_    | |\/| |/ _` / __| __/ _ \ '__|
 | |___|  _|   | |  | | (_| \__ \ ||  __/ |   
  \____|_|     |_|  |_|\__,_|___/\__\___|_|   ";
//         echo "
//    ___                                      _ _         
//   / __\___  _ __ ___  _ __ ___  _   _ _ __ (_) |_ _   _ 
//  / /  / _ \| '_ ` _ \| '_ ` _ \| | | | '_ \| | __| | | |
// / /__| (_) | | | | | | | | | | | |_| | | | | | |_| |_| |
// \____/\___/|_| |_| |_|_| |_| |_|\__,_|_| |_|_|\__|\__, |
//                                                   |___/ 
//    ___ _                                                
//   / __\ |_   _  ___ _ __   ___ _   _                    
//  / _\ | | | | |/ _ \ '_ \ / __| | | |                   
// / /   | | |_| |  __/ | | | (__| |_| |                   
// \/    |_|\__,_|\___|_| |_|\___|\__, | "."(c) ".Carbon::now()->format('Y')."
//                                |___/                    
//         ";
        echo "\n".str_repeat('=', 70)."\n";
    }

    public function basicLine()
    {
        echo "\n".str_repeat('=', 70)."\n";
    }

    public function echoDone()
    {
        echo "\n".str_repeat('=', 70)."\n";
        echo "DONE";
        echo "\n".str_repeat('=', 70)."\n";
    }

    public function tableTimestampReadable($timestamp, $color = null)
    {
        $format = Carbon::createFromTimestamp($timestamp)->format('n/j/y @ g:i A');
        $humans = Carbon::createFromTimestamp($timestamp)->diffForHumans();
        $humans = str_replace('utes', '', $humans); // Minutes

        if ($color == 'red') return $this->r1.$format.$this->color_reset."\t".$humans;
        return $this->c1.$format.$this->color_reset."\t".$humans;
    }

    public function condescendingNickname()
    {
        $names = ['Chief', 'Big Guy', 'Champ', 'Tough Guy', 'Ace', 'Smart Guy'];
        $index = rand(1,count($names));
        return $names[$index - 1];
    }

    public function blueLineMessage($string)
    {
        $max = 70;
        $left = round(($max - strlen($string)) / 2);
        $right = $max - $left - strlen($string);
        echo $this->c1.str_repeat(' ', $left).$string.str_repeat(' ', $right).$this->color_reset."\n";
    }

    public function redLineMessage($string)
    {
        $max = 70;
        $left = round(($max - strlen($string)) / 2);
        $right = $max - $left - strlen($string);
        echo $this->r1.str_repeat(' ', $left).$string.str_repeat(' ', $right).$this->color_reset."\n";
    }

    public function bannerMessage($msgs)
    {
        $output = "\n".str_repeat('=', 70)."\n\n";
        foreach($msgs as $msg) {
             $output .= "    ".$msg."\n";
        }
        $output .= "\n".str_repeat('=', 70)."\n";
        echo $output;
    }
 }
