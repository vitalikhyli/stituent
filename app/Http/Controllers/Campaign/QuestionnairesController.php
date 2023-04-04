<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Question;
use App\Questionnaire;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class QuestionnairesController extends Controller
{
    public function answers($id)
    {
        $questionnaire = Questionnaire::find($id);

        return view('campaign.questionnaires.answers', compact('questionnaire'));
    }

    public function deleteQuestion(Request $request, $questionnaire_id, $question_id)
    {
        $question = Question::find($question_id);
        $this->authorize('basic', $question);
        $question->delete();
        $questionnaire = Questionnaire::find($question->questionnaire_id);

        return redirect('/'.Auth::user()->team->app_type.'/questionnaires/'.$questionnaire->id.'/edit');
    }

    public function updateQuestion(Request $request, $questionnaire_id)
    {
        //////// Arguments

        $question_id = request('question_id');
        $questionnaire_id = request('questionnaire_id');
        $the_order = request('the_order');
        $done = (request('done')) ? true : false;

        $question = Question::find($question_id);
        $this->authorize('basic', $question);

        //////// Logic to handle changing the order

        $question_currently_so_ordered = Question::where('questionnaire_id', $questionnaire_id)
                                                 ->where('id', '!=', $question->id)
                                                 ->where('the_order', $the_order)
                                                 ->first();

        if ($question_currently_so_ordered) {
            $questions_to_reorder = Question::where('questionnaire_id', $questionnaire_id)
                                            ->where('the_order', '>=', $the_order)
                                            ->where('id', '!=', $question->id)
                                            ->get();

            foreach ($questions_to_reorder as $reorder) {
                $reorder->the_order += 1;
                $reorder->save();
            }
        }

        //////// Save

        $question->question = request('question');
        $question->description = request('description');
        $question->answer = request('answer');
        $question->the_order = $the_order;
        $question->done = $done;
        $question->assigned_to = request('assigned_to');

        $question->save();

        //////// Return

        $questionnaire = Questionnaire::find($question->questionnaire_id);

        return redirect('/'.Auth::user()->team->app_type.'/questionnaires/'.$questionnaire->id.'/edit');
    }

    public function getAJAX($questionnaire_id, $question_id)
    {
        $question = Question::find($question_id);
        $this->authorize('basic', $question);

        return json_encode([
                'questionnaire_id' 	=> $questionnaire_id,
                'question_id' 		=> $question_id,
                'question'			=> $question->question,
                'description'		=> $question->description,
                'answer'			=> $question->answer,
                'the_order'         => $question->the_order,
                'done'              => ($question->done) ? true : false,
                'assigned_to'       => $question->assigned_to,
                ]);
    }

    public function index()
    {
        $questionnaires = Questionnaire::thisTeam()
                       ->withCount(['questions as questions_total',
                                    'questions as questions_done' => function ($q) {
                                        $q->where('done', true);
                                    }, ])
                        ->orderBy('due')
                        ->orderBy('name');

        $questionnaires_count = $questionnaires->count();
        $questionnaires = $questionnaires->get();

        // Highlight recently-created records
        $questionnaires->each(function ($item) {
            if (Carbon::now()->diffInSeconds($item['created_at']) <= 10) {
                return $item['is_new'] = true;
            }
        });

        // Calculate Percentage Done
        $questionnaires->each(function ($item) {
            if ($item['questions_total'] == 0) {
                return 0;
            } else {
                return $item['percent_done'] = round($item['questions_done'] / $item['questions_total'] * 100, 0);
            }
        });

        // Due Soon
        $questionnaires->each(function ($item) {
            $calc = Carbon::now()->diffInDays($item['due'], false) * 1;
            if ($calc >= 0 && $calc <= 7) {
                return $item['due_soon'] = true;
            }
        });

        // Past Due
        $questionnaires->each(function ($item) {
            $calc = Carbon::now()->diffInDays($item['due'], false) * 1;
            if ($calc < 0) {
                return $item['past_due'] = true;
            }
        });

        $questionnaires_todo = $questionnaires->where('done', '!=', true);
        $questionnaires_todo_count = $questionnaires_todo->count();

        $questionnaires_done = $questionnaires->where('done', true);
        $questionnaires_done_count = $questionnaires_done->count();

        return view('campaign.questionnaires.index', compact('questionnaires_count', 'questionnaires_todo', 'questionnaires_todo_count', 'questionnaires_done', 'questionnaires_done_count'));
    }

    public function store(Request $request)
    {
        $name = request('name');
        $due = Carbon::parse(request('due'))->toDateString();
        $questionnaire = Questionnaire::thisTeam()->where('name', $name)->first();

        $errors = [];
        if (! $name) {
            $errors = ['name'=>'Name cannot be blank.'];
        }
        if (strlen($name) > 100) {
            $errors = ['name'=>'Name cannot be more than 100 letters.'];
        }
        if ($questionnaire) {
            $errors = ['name'=>'Name already exists'];
        }

        if ($errors) {
            return redirect(Auth::user()->team->app_type.'/questionnaires')->withErrors($errors)->withInput();
        } else {
            $questionnaire = new Questionnaire;
            $questionnaire->team_id = Auth::user()->team->id;
            $questionnaire->user_id = Auth::user()->id;
            $questionnaire->name = $name;
            $questionnaire->due = $due;
            $questionnaire->save();

            return redirect(Auth::user()->team->app_type.'/questionnaires');
        }
    }

    public function edit($id)
    {
        $questionnaire = Questionnaire::find($id);

        return view('campaign.questionnaires.edit', compact('questionnaire'));
    }

    public function update(Request $request, $questionnaire_id, $close = null)
    {
        $questionnaire = Questionnaire::find($questionnaire_id);
        $this->authorize('basic', $questionnaire);

        $name = request('name');
        $due = Carbon::parse(request('due'))->toDateString();
        $in_use = Questionnaire::thisTeam()->where('name', $name)
                                   ->where('id', '!=', $questionnaire->id) //Only check other Questionnaires
                                   ->first();

        $errors = [];
        if (! $name) {
            $errors = ['name'=>'Questionnaire name cannot be blank.'];
        }
        if (strlen($name) > 100) {
            $errors = ['name'=>'Questionnaire cannot be more than 100 letters.'];
        }
        if ($in_use) {
            $errors = ['name'=>'Questionnaire already exists'];
        }

        if ($errors) {
            return redirect(Auth::user()->team->app_type.'/questionnaires/'.$questionnaire->id.'/edit')->withErrors($errors)->withInput();
        } else {
            $questionnaire->team_id = Auth::user()->team->id;
            $questionnaire->user_id = Auth::user()->id;
            $questionnaire->name = $name;
            $questionnaire->due = $due;
            $questionnaire->done = request('done') ? true : false;
            $questionnaire->user_id = request('user_id');
            $questionnaire->save();

            if (request('new_question')) {
                $question = new Question;
                $question->the_order = $questionnaire->questions->count() + 1;
                $question->question = request('new_question');
                $question->questionnaire_id = $questionnaire->id;
                $question->assigned_to = Auth::user()->id;
                $question->save();
            }

            if (! $close) {
                return redirect(Auth::user()->team->app_type.'/questionnaires/'.$questionnaire->id.'/edit');
            } else {
                return redirect(Auth::user()->team->app_type.'/questionnaires');
            }
        }
    }

    public function delete($questionnaire_id)
    {
        $questionnaire = Questionnaire::find($questionnaire_id);
        $this->authorize('basic', $questionnaire);

        $questions = $questionnaire->questions()->delete();

        $questionnaire->delete();

        return redirect(Auth::user()->team->app_type.'/questionnaires');
    }
}
