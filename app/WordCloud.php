<?php

namespace App;

use App\Contact;
use Auth;
use Illuminate\Database\Eloquent\Model;

class WordCloud extends Model
{
    public function getWordCloud()
    {
        $wordcloud = Contact::where('team_id', Auth::user()->team->id)
                                      ->where(function ($q) {
                                          $q->orwhere('private', 0);
                                          $q->orwhere(function ($w) {
                                              $w->where('private', 1);
                                              $w->where('user_id', Auth::user()->id);
                                          });
                                      })
                                      ->orderBy('created_at')
                                      ->take(50)
                                      ->get();

        //Everything in a single string
        $thecloud = '';
        foreach ($wordcloud as $item) {
            $thecloud .= $item->notes.' '.$item->subject.' ';
        }
        $thecloud = str_word_count($thecloud, 1);

        //Remove short words
        foreach ($thecloud as $key=>&$value) {
            if (strlen($value) < 3) {
                unset($thecloud[$key]);
            }
        }

        //Remove others
        $remove_these = ['the', 'this', 'then', 'there', 'from', 'for', 'to', 'as', 'near', 'about', 'with', 'every', 'they', 'them', 'called', 'emailed'];
        $thecloud = array_diff($thecloud, $remove_these);

        //Calculate
        $thecloud = array_count_values($thecloud);

        //Turn into array
        $thecloud = json_decode(json_encode($thecloud), false);

        //Organize
        $thecloud = collect($thecloud)->sort()->reverse()->take(35)->chunk(5);

        return $thecloud;
    }
}
