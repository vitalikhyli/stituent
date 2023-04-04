<?php

namespace App\Http\Controllers;

use App\BulkEmail;
use App\BulkEmailCode;
use App\BulkEmailQueue;
use App\Category;
use App\GroupPerson;
use App\Http\Controllers\Controller;
use App\PeopleList;
use App\Person;
use App\WorkCase;
use App\Search;
use App\Traits\ConstituentQueryTrait;
use App\WorkFile;
use Artisan;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;
use Storage;
use Validator;

class BulkEmailController extends Controller
{
    use ConstituentQueryTrait;

    public function showPrintable($app_type, $id)
    {
        $email = BulkEmail::find($id);

        return view('shared-features.bulkemail.show-print', compact('email'));
    }

    public function showList($app_type, $id)
    {
        $list = BulkEmailList::find($id);
        $emails = $list->emails()->sortBy('email');

        return view('shared-features.bulkemail.list', compact('emails', 'list'));
    }

    public function adminSend($app_type)
    {
        Artisan::call('cf:mail');

        return back();
    }

    public function new($app_type)
    {
        return view('shared-features.bulkemail.new');
    }

    public function store(Request $request, $app_type)
    {
        $email = new BulkEmail;
        if (request('code_id')) {
            $email->bulk_email_code_id = $code->id;
        }
        $email->team_id = Auth::user()->team->id;
        $email->user_id = Auth::user()->id;
        $email->name = request('name');
        $email->save();

        return redirect(Auth::user()->team->app_type.'/emails/'.$email->id.'/edit');
    }

    public function delete($app_type, $id)
    {
        $email = BulkEmail::find($id);

        $search = Search::find($email->search_id);
        if ($search) {
            $search->delete();
        }

        $email->delete();

        return redirect(Auth::user()->team->app_type.'/emails');
    }

    public function convertHTMLtoPlain($text)
    {
        $line = "\r";

        $text = html_entity_decode($text, ENT_QUOTES);

        $text = htmlspecialchars_decode($text);

        $text = str_replace('<br>', $line, $text);

        $text = str_replace('</p>', $line, $text);

        $text = str_replace('<hr>', $line.str_repeat('-', 75).$line.$line, $text);

        $text = str_replace('<li>', $line.'* ', $text);

        $text = strip_tags($text);

        return $text;
    }

    public function update(Request $request, $app_type, $id, $next = null)
    {
        $email = BulkEmail::find($id);

        if (Auth::user()->permissions->developer) {
            if (request('bulk_email_array')) {
                Auth::user()->addMemory('bulk_email_array', true);
            } else {
                Auth::user()->addMemory('bulk_email_array', false);
            }
        }

        

        if (! $email->queued) {
            if (request('code_new')) {
                $new_code = new BulkEmailCode;
                $new_code->name = request('code_new');
                $new_code->date = Carbon::now()->format('Y-m-d');
                $new_code->team_id = Auth::user()->team->id;
                $new_code->save();

                $the_code_id = $new_code->id;
            } else {
                $the_code_id = request('code_id');
            }

            $email->name = request('name');
            $email->sent_from = request('sent_from');
            $email->sent_from_email = request('sent_from_email');
            $email->bulk_email_code_id = $the_code_id;
            $email->subject = request('subject');
            $email->content = request('content');
            $email->old_tracker_code = request('old_tracker_code');
            $email->refresh_plain = (request('refresh_plain')) ? 1 : 0;

            $email->content_plain = $this->convertHTMLtoPlain(request('content'));

            $email->expected_count = $this->bulkEmailQuery($request->input())->count();

            $search = Search::find($email->search_id);
            if (! $search) {
                $search = new Search;
            }

            $search->form = $this->getQueryFormFields($request, 'bulkemail');
            $search->name = $email->name;
            $search->bulk_email = true;
            $search->team_id = Auth::user()->team->id;
            $search->user_id = Auth::user()->id;
            $search->save();

            $email->search_id = $search->id;
            $email->save();
        }

        $validate_array = $request->all();
        $validate_array['expected_count'] = $email->expected_count;

        switch ($next) {

            case 'test':
            $success_url = Auth::user()->team->app_type.'/emails/'.$id.'/test';

            return $this->validateEmailBeforeNext($validate_array, $success_url);
            break;

            case 'queue':
            $success_url = Auth::user()->team->app_type.'/emails/'.$id.'/queue';

            return $this->validateEmailBeforeNext($validate_array, $success_url);
            break;

            case 'close':
            return redirect(Auth::user()->team->app_type.'/emails');
            break;

            default:
            return redirect(Auth::user()->team->app_type.'/emails/'.$id.'/edit');
            break;

        }
    }

    public function validateEmailBeforeNext($validate_array, $success_url)
    {
        $validator = Validator::make($validate_array, [
                'email'             => ['email'],
                'expected_count'    => ['numeric', 'min:1'],
                'subject'           => ['required'],
                'sent_from'         => ['required'],
                'sent_from_email'   => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            return redirect($success_url);
        }
    }

    public function edit($app_type, $id)
    {
        $email = BulkEmail::find($id);
        if (!$email) {
            return redirect()->back();
        }

        $lists = PeopleList::where('team_id', Auth::user()->team->id)->get();

        $bulk_email_senders = BulkEmail::selectRaw('sent_from_email, sent_from, MAX(created_at) as created_at')
                                       ->where('team_id', Auth::user()->team->id)
                                       ->groupBy('sent_from_email', 'sent_from')
                                       ->orderby('sent_from')
                                       ->get()
                                       ->sortByDesc('created_at')
                                       ->take(10);
        if (request('debug')) {
            dd($bulk_email_senders);
        }

        $completed = BulkEmail::where('team_id', Auth::user()->team->id)
                              //->whereNotNull('completed_at')
                              ->where('id', '!=', $id)
                              ->orderBy('created_at', 'desc')
                              ->get();

        $available_types = WorkCase::StaffOrPrivateAndMine()
                                   ->select('type')
                                   ->where('team_id',Auth::user()->team->id)

                                   ->whereNotNull('type')
                                   ->groupBy('type')
                                   ->orderBy('type')
                                   ->get()
                                   ->pluck('type');

        $search = Search::find($email->search_id);

        $input = ($search) ? $search->form : null;

        $response = $this->bulkEmailQuery($input, 'GET_MISSING_EMAILS_TOO');
        $people = $response['people'];
        $missing_emails_count = count($response['missing_emails']);

        $municipalities = $this->getMunicipalities();
        $zips = $this->getZips();
        $categories = Auth::user()->team->categories;

        $categories = $categories->sortBy('name');

        $previous_tracker_codes = BulkEmail::where('team_id', Auth::user()->team->id)
                                           ->orderBy('created_at', 'desc')
                                           ->pluck('old_tracker_code')
                                           ->unique();

        $codes = BulkEmailCode::where('team_id', Auth::user()->team->id)
                              ->orderBy('name')
                              ->get();

        $pictures = $this->getPublicImages(Auth::user()->team->id);

        return view('shared-features.bulkemail.edit', compact('people', 'missing_emails_count', 'input', 'municipalities', 'zips', 'email', 'lists', 'categories', 'completed', 'codes', 'bulk_email_senders', 'pictures', 'previous_tracker_codes', 'available_types'));
    }

    public function getPublicImages($team_id)
    {
        $dir = 'user_uploads/team_'.str_pad($team_id, 5, '0', STR_PAD_LEFT);

        $public_storage = Storage::disk('public')->files($dir);

        $pictures = [];
        foreach ($public_storage as $thepicture) {
            $thepicture_array = explode('/', $thepicture);

            $file_name = $thepicture_array[2];

            $thepicture_filename_array = explode('-', $file_name);

            //dd($thepicture_filename_array);

            $time = $thepicture_filename_array[0];
            $time_formatted = new Carbon;
            try {
                $time_formatted->setTimestamp($time);
                $name = $thepicture_filename_array[1];

                $pictures[] = ['name'       => $name,
                               'file_name'  => $file_name,
                               'url'        => $thepicture,
                               'time'       => Carbon::parse($time_formatted)->format('Y-m-d H:i a'), ];
            } catch (\Exception $e) {
            }
        }

        return $pictures;
    }

    public function testAsk($app_type, $id)
    {
        $email = BulkEmail::find($id);

        if (! $email->queued) {
            $preview_html = $this->getPreviewHTML($email->content);

            return view('shared-features.bulkemail.test', compact('email', 'preview_html'));
        } else {
            return redirect(Auth::user()->team->app_type.'/emails/'.$email->id.'/queueshow');
        }
    }

    public function queueAsk($app_type, $id)
    {
        $email = BulkEmail::find($id);

        if (! $email->queued) {
            $preview_html = $this->getPreviewHTML($email->content);

            return view('shared-features.bulkemail.queue', compact('email', 'preview_html'));
        } else {
            return redirect(Auth::user()->team->app_type.'/emails/'.$email->id.'/queueshow');
        }
    }

    public function getPreviewHTML($preview_html)
    {
        $available = ['full_name',
                      'title',
                      'first_name',
                      'last_name', ];

        $person = new Person;
        $person->full_name = 'Frederick Johnson';
        $person->title = 'President';
        $person->name_title = 'President';
        $person->first_name = 'Frederick';
        $person->last_name = 'Johnson';

        foreach ($available as $field) {
            $preview_html = str_replace('{%'.$field.'%}', $person->$field, $preview_html);
            $preview_html = str_replace('{% '.$field.' %}', $person->$field, $preview_html);
        }

        return $preview_html;
    }

    public function testConfirm(Request $request, $app_type, $id)
    {
        $email = BulkEmail::find($id);

        if (! $email->queued) {
            $validate_array = $request->all();

            $validator = Validator::make($validate_array, [
                    'email' => ['email'],
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {
                DB::table('bulk_email_queue')->insert(
                    ['team_id'          => Auth::user()->team->id,
                     'bulk_email_id'    => $email->id,
                     'email'            => request('email'),
                     'person_id'        => request('person_id'),
                     'test'             => true,
                     'created_at'       => Carbon::now(),
                     'updated_at'       => Carbon::now(),
                    ]
                );

                Artisan::call('cf:mail --tests_only');

                session()->flash('test_success', true);

                return redirect('/'.Auth::user()->team->app_type.'/emails/'.$id.'/edit');
            }
        }
    }

    public function queueConfirm($app_type, $id)
    {
        $email = BulkEmail::find($id);

        if (! $email->queued) {
            $email->queued = true;
            $email->save();

            $search = Search::find($email->search_id);

            // $list = session('bulk_email_recipients');
            $list = $this->bulkEmailQuery($search->form, $limit = 'none');

            foreach ($list as $recipient) {
                $bulk_queue = new BulkEmailQueue;
                $bulk_queue->team_id = Auth::user()->team->id;
                $bulk_queue->bulk_email_id = $email->id;
                $bulk_queue->email = $recipient->primary_email;
                $bulk_queue->person_id = $recipient->id;

                if (filter_var($bulk_queue->email, FILTER_VALIDATE_EMAIL)) {
                    $bulk_queue->save();
                }
            }
        }

        return redirect(Auth::user()->team->app_type.'/emails');
    }

    public function queueShow($app_type, $id)
    {
        $email = BulkEmail::find($id);

        $queue = DB::table('bulk_email_queue')->where('bulk_email_id', $id)->get();

        return view('shared-features.bulkemail.queue-show', compact('email', 'queue'));
    }

    public function queueHalt($app_type, $id)
    {
        $email = BulkEmail::find($id);

        if (! $email->completed_at) {
            DB::table('bulk_email_queue')->where('bulk_email_id', $id)
                                         ->where('processing', false)
                                         ->where('sent', false)
                                         ->delete();

            $email->completed_at = now();

            $email->save();
        }

        return back();
    }

    public function copy($app_type, $id)
    {
        $email = BulkEmail::find($id);
        $new_email = $email->replicate();

        $search = Search::find($email->search_id);
        if ($search) {
            $new_search = $search->replicate();
            $new_search->save();
            $new_email->search_id = $new_search->id;
        }

        $new_email->name = 'COPY OF '.$email->name;
        $new_email->subject = $email->subject;
        $new_email->queued = false;
        $new_email->completed_at = null;

        $new_email->save();

        return back();
    }

    public function indexQueuedRows($app_type)
    {
        if (!Auth::user()->permissions->developer) return redirect()->back();

        $queued_rows = BulkEmailQueue::where('team_id', Auth::user()->team->id)
                                     ->orderByDesc('created_at')
                                     ->take(100)
                                     ->get();

        $queue = BulkEmail::where('team_id', Auth::user()->team->id)
                           ->where('queued', true)
                           ->whereNull('completed_at')
                           ->orderBy('updated_at', 'desc');
        $queue_total = $queue->count();

        $queue_completed = BulkEmail::where('team_id', Auth::user()->team->id)
                                    ->whereNotNull('completed_at')
                                    ->orderBy('updated_at', 'desc');
        $completed_total = $queue_completed->count();

        return view('shared-features.bulkemail.index-queued-rows', compact('queued_rows', 'queue_total','completed_total'));
    }

    public function indexQueued($app_type)
    {
        return $this->index($app_type, null, 'queued');
    }

    public function indexCompleted($app_type)
    {
        return $this->index($app_type, null, 'completed');
    }

    // public function indexAllQueue($app_type)
    // {
    //     return $this->index($app_type, 'show_the_whole_queue');
    // }

    public function index($app_type, $all_queue = null, $view = null)
    {
        $queue = BulkEmail::where('team_id', Auth::user()->team->id)
                           ->where('queued', true)
                           ->whereNull('completed_at')
                           ->orderBy('updated_at', 'desc');

        $queue_total = $queue->count();

        $queue = $queue->get();

        // $queue_max = 1;

        // if($all_queue) {
        //     $queue = $queue->get();
        //     $all_queue = true;
        // } else {
        //     $queue = $queue->take($queue_max)->get();
        //     $all_queue = false;
        // }

        $queue_completed = BulkEmail::where('team_id', Auth::user()->team->id)
                                    ->whereNotNull('completed_at')
                                    ->orderBy('updated_at', 'desc');

        $completed_total = $queue_completed->count();

        $queue_completed = $queue_completed->get();

        $emails = BulkEmail::where('team_id', Auth::user()->team->id)
                           ->whereNull('bulk_email_code_id')
                           ->where('name', 'not like', 'TEMPLATE%')
                           ->orderBy('created_at', 'desc')
                           ->get();

        $templates = BulkEmail::where('team_id', Auth::user()->team->id)
                           ->whereNull('bulk_email_code_id')
                           ->where('name', 'like', 'TEMPLATE%')
                           ->orderBy('created_at', 'desc')
                           ->get();

        $templates = $templates->each(function ($item) {
            $item['name'] = trim(substr($item['name'], 8));
        });

        $codes = BulkEmailCode::where('team_id', Auth::user()->team->id)
                              ->orderBy('name')
                              ->get();

        switch ($view) {

            case 'queued':
                $blade = '.bulkemail.index-queued';
                break;

            case 'completed':
                $blade = '.bulkemail.index-completed';
                break;

            default:
                $blade = 'bulkemail.index';
        }

        return view('shared-features.'.$blade, compact('templates',
                                                       'emails',
                                                       'codes',
                                                       'queue',
                                                       'queue_total',
                                                       'queue_completed',
                                                       'completed_total'));
    }

    public function anyRecipientInput(Request $request, $app_type)
    {
        $answer = false;
        foreach (request()->all() as $key => $value) {
            if ($value) {
                if (substr($key, 0, 15) == 'recipients_add_') {
                    $answer = true;
                    continue;
                }
            }
        }

        return $answer;
    }

    public function updateRecipients(Request $request, $app_type)
    {
        //dd($request->input());
        $response = $this->bulkEmailQuery($request->input(), 'GET_MISSING_EMAILS_TOO');
        $people = $response['people'];
        $missing_emails_count = count($response['missing_emails']);

        // $missing_emails_count = 0;
        // foreach ($people as $person) {
        //     if (!$person->email) {
        //         $missing_emails_count++;
        //     }
        // }

        return view('shared-features.bulkemail.recipients-list', compact('people', 'missing_emails_count'));
    }
}
