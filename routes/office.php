<?php

/////////////////////////////////////////////////////////////////////////////////////
//
//                                     OFFICE
//
/////////////////////////////////////////////////////////////////////////////////////

// Route::middleware(['AppGate::System'])->group(function () {

    /////////////////////////////////////////////////////////////////////////////////
Route::group([
            'prefix'        => '/office',
            'namespace'     => 'Office',
            'middleware'    => ['auth', 'database_set', 'activated', 'appgate:office'],
            ], function () {

                Route::group(['prefix' => '/relationships'], function () {
                    Route::get('/search_entities/{value?}', 'RelationshipsController@searchEntities');
                    Route::get('/search_people/{value?}', 'RelationshipsController@searchPeople');
                    Route::get('/search_kinds/{type}/{value?}', 'RelationshipsController@searchKinds');

                    Route::get('/new-p2p/{id}', 'RelationshipsController@newPersonToPerson');
                    Route::get('/new-p2e/{id}', 'RelationshipsController@newPersonToEntity');
                    Route::get('/new-e2p/{id}', 'RelationshipsController@newEntityToPerson');
                    Route::get('/new-e2e/{id}', 'RelationshipsController@newEntityToEntity');

                    Route::get('/{id}/edit', 'RelationshipsController@edit');

                    Route::post('/{id}/save/{type}', 'RelationshipsController@save');
                    Route::post('/{id}/update/{close?}', 'RelationshipsController@update');

                    Route::get('/{id}/delete', 'RelationshipsController@delete');
                });

                /////////////////////////////////////////////////////////////////////////////////

                

                    // Route::get('/{entity}/contacts/{contact}/edit', 'EntitiesController@editContact');
        // Route::post('/{entity}/contacts/{contact}/update/{close?}', 'ContactsController@update');
        // Route::get('/{entity}/contacts/{contact}/delete', 'ContactsController@delete');
       

                /////////////////////////////////////////////////////////////////////////////////

                /////////////////////////////////////////////////////////////////////////////////

                /////////////////////////////////////////////////////////////////////////////////

                /////////////////////////////////////////////////////////////////////////////////

                // Route::get('/labels', 'SearchesController@labelsIndex');
                // Route::post('/labels', 'SearchesController@labelsShow');

                /////////////////////////////////////////////////////////////////////////////////

                /////////////////////////////////////////////////////////////////////////////////

                // IS THIS NEEDED?
                // Route::get('custom-lists/bulk-email', 'CustomListsController@bulkEmail');

                /////////////////////////////////////////////////////////////////////////////////

                Route::get('/history', 'DashboardController@metricsHistory');
                Route::view('/reports', 'office.metrics.reports');
            });

// });
