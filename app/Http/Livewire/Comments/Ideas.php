<?php

namespace App\Http\Livewire\Comments;

use Livewire\Component;

use App\Comment;

use Auth;
use Carbon\Carbon;


class Ideas extends Component
{

	public $add_subject;
	public $add_notes;
	public $ideasFormVisible = false;
	public $closing_notes = [];

	protected $rules = [
        'add_notes' => 'required|min:10',
        'add_subject' => 'required|min:5',
    ];


	public function mount()
	{
		$closing_notes = [];
		foreach(Comment::whereNotNull('closing_notes')->get() as $comment) {
			$closing_notes[$comment->id] = $comment->closing_notes;
		}
		$this->closing_notes = $closing_notes;
	}

	public function softDeleteToggle($id)
	{
		$comment = Comment::withTrashed()->find($id);
		if ($comment) {
			if ($comment->deleted_at) {
				$comment->restore();
			} else {
				$comment->delete();
			}
		}
	}

	public function reopenComment($id)
	{
		$comment = Comment::withTrashed()->find($id);
		$comment->closed_at			= null;
		$comment->closing_notes 	= null;
		$comment->closed_by 		= null;
		$comment->save();

		$closing_notes = $this->closing_notes;
		$closing_notes[$comment->id] = null;
		$this->closing_notes = $closing_notes;
	}

	public function closeComment($id)
	{
		$comment = Comment::withTrashed()->find($id);
		$comment->closed_at			= ($comment->closed_at) ? $comment->closed_at : Carbon::now();
		$comment->closing_notes 	= $this->closing_notes[$id];
		$comment->closed_by 		= Auth::user()->id;
		$comment->save();
	}

	public function addIdea()
	{
		$this->validate();

		$comment = new Comment;
		$comment->user_id 		= Auth::user()->id;
		$comment->team_id 		= Auth::user()->team->id;
		$comment->idea 			= true;
		$comment->subject 		= $this->add_subject;
		$comment->notes 		= $this->add_notes;
		$comment->state 		= strtoupper(Auth::user()->team->account->state);
		$comment->save();

		$this->ideasFormVisible = false;
		$this->add_subject		= null;
		$this->add_notes		= null;
	}

	public function vote($id, $dir)
	{
		$opp = ($dir == 'up') ? 'down' : 'up';

		$comment = Comment::find($id);
		
		$current_opp_users = collect($comment->{ $opp.'_users' })->toArray();
		if (in_array(Auth::user()->id, $current_opp_users)) {
			$who = collect($comment->{ $opp.'_users' })->reject(function ($item) {
					    return $item == Auth::user()->id;
					});
			$comment->{ $opp.'_users' } = $who;
			$comment->$opp += -1;
		}

		$current_dir_users = collect($comment->{ $dir.'_users' })->toArray();
		if (!in_array(Auth::user()->id, $current_dir_users)) {

			$who = $comment->{ $dir.'_users' };
			$who[] = Auth::user()->id;
			$comment->{ $dir.'_users' } = $who;
			$comment->$dir += 1;

		} else {

			$who = collect($comment->{ $dir.'_users' })->reject(function ($item) {
				    return $item == Auth::user()->id;
				});
			$comment->{ $dir.'_users' } = $who;
			$comment->$dir += -1;
					
		}

		$comment->score = $comment->up - $comment->down;
		$comment->votes = $comment->up + $comment->down;
		$comment->save();
	}

    public function render()
    {
    	$base = Comment::where('idea', true)
    				   ->orderBy('score', 'desc')
    				   ->orderBy('votes', 'desc')
    				   ->orderBy('created_at', 'desc');

    	$comments 	= (clone $base)->whereNull('closed_at')->get();
    	
    	$closed 	= (clone $base)->whereNotNull('closed_at')->get();

    	$trashed 	= Comment::onlyTrashed()
    						 ->where('idea', true)
    						 ->orderBy('created_at', 'desc')
    						 ->get();

        return view('livewire.comments.ideas', [
        										'comments' 	=> $comments,
    											'closed'	=> $closed,
    											'trashed'	=> $trashed,
    										   ]);
    }
}
