<?php

namespace App\Http\Livewire\Comments;

use Livewire\Component;

use App\Comment;

use Auth;


class Endorsements extends Component
{

	public $endorsementFormVisible = false;
	public $endorsement_name;
	public $endorsement_title;
	public $endorsement_notes;

	protected $rules = [
        'endorsement_notes' => 'required|min:10',
        'endorsement_title' => 'required',
    ];


	public function addEndorsement()
	{
		$this->validate();

		$endorsement = new Comment;
		$endorsement->user_id 			= Auth::user()->id;
		$endorsement->team_id 			= Auth::user()->team->id;
		$endorsement->name 				= $this->endorsement_name;
		$endorsement->title 			= $this->endorsement_title;
		$endorsement->notes 			= $this->endorsement_notes;
		$endorsement->endorsement 		= true;
		$endorsement->state 			= strtoupper(Auth::user()->team->account->state);
		$endorsement->save();

		$this->endorsementFormVisible 	= false;
		$this->endorsement_name 		= null;
		$this->endorsement_title 		= null;
		$this->endorsement_notes 		= null;
	}

    public function render()
    {
    	$user_comment = Comment::where('endorsement', true)
    						   ->where('user_id', Auth::user()->id)
    						   ->first();

    	$comments = Comment::where('endorsement', true);

    	if ($user_comment) {
    		$comments = $comments->where('user_id', '!=', Auth::user()->id);
    	}

    	$comments = $comments->orderBy('created_at', 'desc')->get();

    	$user_has_endorsed = ($user_comment) ? true : false;

    	if ($user_has_endorsed) {
    		$comments = $comments->prepend($user_comment);	// Put theirs at the top
    	}

        return view('livewire.comments.endorsements', [
        												'comments' => $comments,
        												'user_has_endorsed' => $user_has_endorsed
        											  ]);
    }
}
