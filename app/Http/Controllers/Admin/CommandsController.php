<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Artisan;


class CommandsController extends Controller
{
    public function index($messages = null, $running = null)
    {
    	$commands = [
    					'cf:election_year_counts --limit=50 --nulls',
    				];

    	$reload = request('reload') ? true : false;

    	return view('admin.commands.index', compact('commands', 'reload', 'messages', 'running'));
    }

    public function run($command)
    {
    	$command = base64_decode($command);

    	$messages = [];

    	try {

    		Artisan::call($command);

    		$messages[] = 'Successfully ran: '.$command;

    	} catch (\Exception $e) {

    		$messages[] = $e->getMessage();
    	}

    	return $this->index($messages, $command);
    }

}
