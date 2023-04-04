<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppRoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function time_to_test_url($url,$name)
    {
        echo 'http://communityfluency.test'.$url."?auth=laz\n";
        $response = $this->getJson($url.'?auth=laz');
        $data = json_decode($response->getContent(), true);
        $response->assertStatus(200);
        $this->assertTrue(is_array($data));
        $this->assertTrue(trim($response->getContent()) != '');
        //print_r($response->getContent());
    }

    public function test_index_routes()
    {
        if (!defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }
        $urls = [
            '/app/local-data',
            '/app/dashboard',
            '/app/notes',
            '/app/notes/types',
            '/app/cases',
            '/app/groups',
            '/app/organizations',
            '/app/map',
        ];

        foreach ($urls as $url) {
            $this->time_to_test_url($url,$url);
        }
    }


    public function test_specific_routes()
    {
        if (!defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }
        $urls = [
            '/app/notes/264530',
            '/app/cases/38417',
            '/app/groups/5248',
            '/app/constituent/MA_01AAA0703000',
            '/app/constituent/244440',
            '/app/organizations/334',
        ];

        foreach ($urls as $url) {
            $this->time_to_test_url($url,$url);
        }
    }
}

/*

    Route::get('app/local-data',        'App\DataController@localData');
    Route::get('app/dashboard',         'App\DashboardController@index');

    Route::get('app/search',            'App\SearchController@index');
    Route::get('app/search/{id}',       'App\SearchController@show');
    Route::get('app/recents',           'App\SearchController@recents');

    Route::get('app/constituent/{id}',  'App\ConstituentsController@show');
        
    Route::get('app/notes',             'App\NotesController@index');
    Route::get('app/notes/{id}/edit',   'App\NotesController@edit');
    Route::post('app/notes',            'App\NotesController@store');
    Route::get('app/notes/types',       'App\NotesController@types');
    Route::get('app/notes/{id}',        'App\NotesController@show');
    Route::post('app/notes/{id}',       'App\NotesController@update');

    Route::get('app/cases',             'App\CasesController@index');
    Route::get('app/cases/{id}',        'App\CasesController@show');

    Route::get('app/groups',            'App\GroupsController@index');
    Route::get('app/groups/{id}',       'App\GroupsController@show');

    Route::get('app/files',             'App\FilesController@index');
    Route::get('app/files/{id}',        'App\FilesController@show');

    Route::get('app/organizations',     'App\OrganizationsController@index');
    Route::get('app/organizations/{id}','App\OrganizationsController@show');

    Route::get('app/map',               'App\MapController@index');

    Route::get('app/disconnect',        'App\WebController@disconnect');

*/