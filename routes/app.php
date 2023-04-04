<?php

Route::get('app', 'App\WebController@main');
Route::post('app', 'App\WebController@register');
Route::get('app/register', 'App\WebController@register');

Route::post('app/email-code', 'App\WebController@emailCode');

Route::group(['middleware' => 'app'], function () {

	Route::get('app/local-data', 		'App\DataController@localData');

    Route::get('app/dashboard', 		'App\DashboardController@index');

    Route::get('app/search', 			'App\SearchController@index');
    Route::get('app/search/{id}',		'App\SearchController@show');
    Route::get('app/recents',			'App\SearchController@recents');

    Route::get('app/constituent/{id}', 	'App\ConstituentsController@show');
		
	Route::get('app/notes', 			'App\NotesController@index');
	Route::get('app/notes/{id}/edit', 	'App\NotesController@edit');
	Route::post('app/notes', 			'App\NotesController@store');
	Route::get('app/notes/types', 		'App\NotesController@types');
	Route::get('app/notes/{id}', 		'App\NotesController@show');
	Route::post('app/notes/{id}', 		'App\NotesController@update');

	Route::get('app/cases', 			'App\CasesController@index');
	Route::get('app/cases/{id}', 		'App\CasesController@show');

	Route::get('app/groups', 			'App\GroupsController@index');
	Route::get('app/groups/{id}', 		'App\GroupsController@show');

	Route::get('app/files', 			'App\FilesController@index');
	Route::get('app/files/{id}', 		'App\FilesController@show');

	Route::get('app/organizations', 	'App\OrganizationsController@index');
	Route::get('app/organizations/{id}','App\OrganizationsController@show');

	Route::get('app/map', 				'App\MapController@index');

	Route::get('app/disconnect', 		'App\WebController@disconnect');
});