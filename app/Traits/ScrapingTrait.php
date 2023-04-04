<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;

 trait ScrapingTrait
 {
     public function file_get_contents_with_curl($url)
     {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch, CURLOPT_HEADER, false);
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_TIMEOUT, 5);
         $result = curl_exec($ch);
         curl_close($ch);

         return $result;

         // $curl = curl_init();
            // curl_setopt($curl, CURLOPT_URL, $url);
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($curl, CURLOPT_HEADER, false);
            // $data = curl_exec($curl);
            // curl_close($curl);
            // return $data;

            // $handle = curl_init();
            // curl_setopt($handle, CURLOPT_URL, $url);
            // curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            // $output = curl_exec($handle);
            // curl_close($handle);
            // return $output;
     }

     public function englishOrdinal($district)
     {
         $district_first_word = substr($district, 0, strpos($district, ' '));

         if (! $district_first_word) {
             $district_first_word = $district;
             $one_word_only = true;
         }

         $num = preg_replace('/[^0-9]/', '', $district_first_word);

         if (! $num) {
             return $district;
         } else {
             $ordinals_array = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth', 'Eleventh', 'Twelfth', 'Thirteenth', 'Fourteenth', 'Fifteenth', 'Sixteenth', 'Seventeenth', 'Eighteenth', 'Nineteenth', 'Twentieth',
                'Twenty-first',
                'Twenty-second',
                'Twenty-third',
                'Twenty-fourth',
                'Twenty-fifth',
                'Twenty-sixth',
                'Twenty-seventh',
                'Twenty-eighth',
                'Twenty-ninth',
                'Thirtieth',
                'Thirty-first',
                'Thirty-second',
                'Thirty-third',
                'Thirty-fourth',
                'Thirty-fifth',
                'Thirty-sixth',
                'Thirty-seventh',
                ];
             $district_without_first_word = substr($district, strpos($district, ' '));

             if (isset($one_word_only)) {
                 return trim($ordinals_array[$num - 1]);
             } else {
                 return trim($ordinals_array[$num - 1].$district_without_first_word);
             }
         }
     }

     public function daysSinceSaved($html)
     {
         preg_match_all('/'.'<!-- WEBPAGE SAVED ON (.+) -->'.'/', $html, $last_saved);
         if (count($last_saved[1]) > 0) {
             return Carbon::parse($last_saved[1][0])->diffInDays(Carbon::now());
         }
     }

     public function PDF2TextCleaner($text)
     {
         //Clear out excessive returns
         $text = str_replace("\n \n", '<br />', $text);
         $text = str_replace("\n", '', $text);

         $lines = explode('<br />', $text);

         $lines_new = [];

         foreach ($lines as $key => $theline) {
             $theline = trim($theline);
             if ($theline) {
                 $lines_new[] = $theline;
             }
         }

         $text = implode('<br />', $lines_new);

         return $text;
     }

     public function PDF2TextCleaner_BillText($text)
     {
         //Clear out excessive returns
         $text = str_replace("\n \n", '<br />', $text);
         $text = str_replace("\n", '', $text);

         $lines = explode('<br />', $text);

         // dd($lines);
         $text = null;

         // dd($lines);

         $previouslastnum = null;

         foreach ($lines as $key => $theline) {
             $theline = trim($theline);

             //Get Rid of Line NUmbers
             $words = explode(' ', $theline);
             $lastword = array_pop($words);

             if ($lastword) {
                 if (is_numeric($lastword)) {
                     if ($lastword - 1 == $previouslastnum) {
                         $theline = substr($theline, 0, strlen($theline) - strlen($lastword));
                         $previouslastnum = $lastword;
                     }
                 }
             }

             // if (is_numeric(trim($lastword))) $lastword."\r\n";

             $text .= '<div class="mb-2">'.$theline.'</div>';
         }

         return $text;
     }

     public function pullOutHREF($html)
     {
         // $ex = "/".'href=([^>]+)'."/";
         $ex = '/'.'href(?:\s*)=(?:\s*)([^>]+)'.'/';

         preg_match_all($ex, $html, $matches);
         if (isset($matches[1][0])) {
             $html = trim($matches[1][0]);
         } else {
             $html = null;
         }

         $html = str_replace('"', '', $html);
         $html = str_replace("'", '', $html);

         return $html;
     }

     public function removeLinkTags($html)
     {
         $ex = '/'.'["|\']>(.*)<\/a>'.'/';
         preg_match_all($ex, $html, $matches);
         if (isset($matches[1][0])) {
             $html = trim($matches[1][0]);
         } else {
             $html = null;
         }

         return $html;
     }

     public function getElementsByTag($html, $open, $close, $include_bookends = null)
     {
         $elements = [];
         $start = 0;
         $i = 0;

         $start_extra = ($include_bookends) ? 0 : strlen($open);
         $end_extra = ($include_bookends) ? strlen($close) : 0;

         while (($start = strpos($html, $open, $start)) !== false) {
             if (! is_array($close)) {
                 $end = strpos($html, $close, $start + strlen($open));
             } else {
                 foreach ($close as $try_this) {
                     $end = strpos($html, $try_this, $start + strlen($open));
                     if ($end !== false) {
                         break;
                     }
                 }
             }

             // echo $i++.' '.$open.' '.$start.' '.$end."\r\n";
             $elements[] = trim(substr($html, $start + $start_extra, $end - $start - $start_extra + $end_extra));

             $start = $end;
         }

         return $elements;
     }

     public function reduceHTML($html, $start_block, $end_block)
     {
         if (! $html) {
             return;
         }

         // String = value to look for; Array = backup values if first doesn't work
         if (! is_array($start_block)) {
             $start_point = strpos($html, $start_block) + strlen($start_block);
         } else {
             foreach ($start_block as $try_block) {
                 $start_point = strpos($html, $try_block) + strlen($try_block);
                 if ($start_point !== false) {
                     break;
                 }
             }
         }

         // String = value to look for; Array = backup values if first doesn't work
         try {
             if (! is_array($end_block)) {
                 $end_point = strpos($html, $end_block, $start_point);
             } else {
                 foreach ($end_block as $try_block) {
                     $end_point = strpos($html, $try_block, $start_point);
                     if ($end_point !== false) {
                         break;
                     }
                 }
             }
         } catch (\Exception $e) {
             dd($html, $end_block, $start_point);
         }

         if (($start_point == false) || ($end_point == false)) {
             dd('Error - reduceHTML()');
         }

         $str_length = $end_point - $start_point; //+ strlen($start_block.$end_block);
         $html = substr($html, $start_point, $str_length);
         $html = trim(preg_replace('/\s+/', ' ', $html));

         return $html;
     }

     public function downloadOrUseExistingThenGetContents($url, $path, $redownload = null)
     {
         if ((! file_exists($path)) || ($redownload)) {
             $html = $this->saveFileGetContents($url, $path);
         } else {
             // $html = file_get_contents($path);
             $html = file_get_contents($path);
         }

         return $html;
     }

     public function saveFileGetContents($url, $path, $pdf = null)
     {
         $this->waitForSeconds(2);

         $this->info('Downloading '.$url);

         try {

            // $html = file_get_contents($url);
             $html = $this->file_get_contents_with_curl($url);
         } catch (\Exception $e) {
             echo 'ERROR - Could not download '.$url.' (Exception caught)'."\r\n";

             return null;
         }

         if (Str::contains($html, 'Error 404')) {
             echo 'ERROR - Could not download '.$url.'(Error 404)'."\r\n";

             return null;
         }

         if (! $pdf) {
             $html = '<!-- WEBPAGE SAVED ON '.Carbon::now()->format('Y-m-d').' -->'."\r\n".$html;
         }

         file_put_contents($path, $html);

         $this->info('Download Complete');

         return $html;
     }

     public function waitForSeconds($seconds)
     {
         for ($i = 1; $i <= $seconds; $i++) {
             echo '   Waiting '.$i;
             echo "\r";
             sleep(1);
         }
         echo "\r\n";
     }

     public function getOrCreateStorageDir($path_dir)
     {
         $path_dir = storage_path().$path_dir;
         if (! file_exists($path_dir)) {
             mkdir($path_dir, 0777, true);
         }

         return $path_dir;
     }
 }
