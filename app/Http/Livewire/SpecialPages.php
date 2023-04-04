<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\SpecialPage;
use Auth;

class SpecialPages extends Component
{
	public $app;
	public $sort = 'top';

	public $new_name;
	public $new_description;
	public $new_anonymous = true;

	public $edit_id;
	public $edits;

    public function render()
    {
    	$special_pages = SpecialPage::where('app', $this->app)
    							    ->whereNull('live_link')
    								->get();

    	if ($this->sort == 'top') {
    		$special_pages = $special_pages->sortByDesc('star_count');
    	}
    	if ($this->sort == 'recent') {
    		$special_pages = $special_pages->sortByDesc('created_at');
    	}

    								//dd($special_pages);
    	$deleted = SpecialPage::onlyTrashed()
    						  ->whereNull('live_link')
    						  ->where('user_id', Auth::user()->id)
    						  ->get();

        return view('livewire.special-pages', compact('special_pages', 'deleted'));
    }
    public function sort($sort)
    {
    	$this->sort = $sort;
    }
    public function edit($sp_id)
    {
    	if ($sp_id == $this->edit_id) {
    		$this->edit_id = null;
    		return;
    	}
    	$sp = SpecialPage::find($sp_id);
    	$this->edit_id = $sp_id;
    	$this->edits = [
    		'name' => $sp->name,
    		'description' => $sp->description,
    	];
    }
    public function delete($sp_id)
    {
    	$sp = SpecialPage::find($sp_id);
    	$sp->delete();
    	$this->edits = null;
    	$this->edit_id = null;
    }
    public function restore($sp_id)
    {
    	$sp = SpecialPage::withTrashed()->find($sp_id);
    	$sp->restore();
    }
    public function save($sp_id)
    {
    	$sp = SpecialPage::find($sp_id);
    	$sp->name = $this->edits['name'];
    	$sp->description = $this->edits['description'];
    	$sp->save();
    	$this->edit_id = null;
    }
    public function cancel()
    {
    	$this->edit_id = null;
    }
    public function addNew()
    {
    	$new_page = new SpecialPage;
    	$new_page->app 			= $this->app;
    	$new_page->user_id 		= Auth::user()->id;
    	$new_page->team_id 		= Auth::user()->team_id;
    	$new_page->name    		= $this->new_name;
    	$new_page->description  = $this->new_description;
    	$new_page->anonymous    = true;
    	$new_page->save();

    	$new_page->addStar();

    	$this->new_name = null;
    	$this->new_description = null;
    	$this->new_anonymous = null;
    }
    public function toggleStar($sp_id)
    {
    	$sp = SpecialPage::find($sp_id);
    	if ($sp->starred) {
    		$sp->removeStar();
    	} else {
    		$sp->addStar();
    	}
    }
}

/* 
	$table->string('app')->nullable();
    $table->unsignedInteger('user_id')->nullable();
    $table->unsignedInteger('team_id')->nullable();
    $table->string('name')->nullable();
    $table->text('description')->nullable();
    $table->text('admin_comment')->nullable();
    $table->text('stars')->nullable();
    $table->string('live_link')->nullable();
*/