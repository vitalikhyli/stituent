<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Term;


class TermsController extends Controller
{
    public function index()
    {
    	$terms = Term::orderBy('effective_at', 'desc')->get();
    	return view('admin.terms.index', ['terms' => $terms]);
    }

    public function new()
    {
    	$term = new Term;
    	$term->save();

    	return view('admin.terms.edit', ['term' => $term]);
    }

    public function edit($id)
    {
    	$term = Term::find($id);
    	return view('admin.terms.edit', ['term' => $term]);
    }

    public function update(Request $request, $id, $close = null)
    {
        if ($close == 'close-no-update') {
            return redirect('/admin/terms');
        }

    	$term = Term::find($id);

    	if ($term->signers->count() < 1) {

	    	$term->title 		= request('title');
	    	$term->text 		= request('text');
	    	$term->effective_at = request('effective_at');
	    	$term->publish 		= (request('publish')) ? true : false;

	    	$term->save();

	    }

    	if ($close == 'close') {
            return redirect('/admin/terms');
        }
        
    	return redirect('/admin/terms/'.$term->id.'/edit');
    }

}
