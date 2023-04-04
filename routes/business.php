<?php

/////////////////////////////////////////////////////////////////////////////////////
//
//                            MARKETING / SALES
//
/////////////////////////////////////////////////////////////////////////////////////

Route::group([
            'prefix'        => '/business',
            'namespace'     => 'Business',
            'middleware'    => ['auth', 'database_set', 'activated', 'appgate:business'],
            ], function () {
                Route::get('/', 'DashboardController@dashboard');

                Route::group(['prefix' => '/clients'], function () {
                    Route::get('/', 'SalesEntitiesController@indexClients');
                    Route::get('/import/{type}/{account?}', 'SalesEntitiesController@importAccount');
                });

                Route::group(['prefix' => '/prospects'], function () {
                    Route::get('/', 'SalesEntitiesController@index');
                    Route::post('/', 'SalesEntitiesController@save');
                    Route::get('/{id}/edit', 'SalesEntitiesController@edit');
                    Route::post('/{id}/update', 'SalesEntitiesController@update');
                    Route::post('/{id}/update/{close?}', 'SalesEntitiesController@update');
                    Route::get('/{id}', 'SalesEntitiesController@show');
                    Route::post('/{id}/add_contact', 'SalesEntitiesController@addContact');
                    Route::post('/{id}/add_person', 'SalesEntitiesController@addPerson');
                });

                Route::group(['prefix' => '/patterns'], function () {
                    Route::get('/', 'SalesPatternsController@index');
                    Route::get('/{id}', 'SalesPatternsController@show');
                    Route::get('/{id}/edit', 'SalesPatternsController@edit');
                    Route::post('/{id}/update', 'SalesPatternsController@update');
                    Route::post('/', 'SalesPatternsController@save');
                });

                Route::group(['prefix' => '/goals'], function () {
                    Route::get('/', 'SalesGoalsController@index');
                    Route::get('/{id}/delete', 'SalesGoalsController@delete');
                    Route::post('/', 'SalesGoalsController@save');
                });

                Route::group(['prefix' => '/salesteams'], function () {
                    Route::get('/', 'SalesTeamsController@index');
                    Route::post('/update', 'SalesTeamsController@update');
                    Route::post('/', 'SalesTeamsController@save');
                });
            });
