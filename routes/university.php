<?php

/////////////////////////////////////////////////////////////////////////////////////
//
//                                    UNIVERSITY
//
/////////////////////////////////////////////////////////////////////////////////////

Route::group([
        'prefix' => '/u',
        'middleware' => ['auth', 'database_set', 'activated', 'appgate:u'],
        ], function () {

    // /////////////////////////////////////////////////////////////////////////////////

            Route::group(['prefix' => '/',
                  'namespace' => 'University', ], function () {
                      Route::get('/', 'DashboardController@dashboard');
                  });

            // /////////////////////////////////////////////////////////////////////////////////

            Route::group(['prefix' => '/entities',
                  'namespace' => 'University', ], function () {
                      Route::get('/{id}/contacts/{contact}/edit', 'EntitiesController@editContact');
                      Route::post('/{id}/contacts/{contact}/update/{c?}', 'EntitiesController@updateContact');
                      Route::get('/{id}/contacts/{contact}/delete', 'EntitiesController@deleteContact');
                      Route::post('/{en}/add_contact', 'EntitiesController@addContactToEntity');

                      Route::get('/', 'EntitiesController@index');
                      Route::get('/new/{v?}', 'EntitiesController@new');
                      Route::get('/service-learning-partnerships', 'EntitiesController@partnerships');
                      Route::get('/type/{type}', 'EntitiesController@indexType');
                      Route::get('/{id}', 'EntitiesController@show');
                      Route::get('/{id}/edit', 'EntitiesController@edit');
                      Route::post('/{id}/update', 'EntitiesController@update');
                      Route::get('/search/{v?}', 'EntitiesController@search');
                      Route::post('/save', 'EntitiesController@save');
                      Route::get('/{id}/delete', 'EntitiesController@delete');
                      Route::post('/{id}/edit-type', 'EntitiesController@editType');

                      Route::post('/{id}/link_person', 'EntitiesController@linkPerson');
                      Route::post('/{id}/update_person', 'EntitiesController@updateRelationship');
                      Route::post('/{id}/delete_person', 'EntitiesController@deleteRelationship');

                      Route::get('/{id}/person/{person}/edit', 'EntitiesController@linkPersonEdit');
                      Route::get('/{id}/relationship/{pivot}/delete', 'EntitiesController@unlinkPerson');

                      Route::get('/{id}/partnerships/new', 'PartnershipsController@new');
                      Route::post('/{id}/partnerships/save', 'PartnershipsController@store');
                      Route::post('/{id}/partnerships/{pshipid}/update/{c?}', 'PartnershipsController@update');
                      Route::post('/{id}/partnerships', 'PartnershipsController@store');
                      Route::get('/{id}/partnerships/{pshipid}/edit', 'PartnershipsController@edit');
                      Route::get('/{id}/partnerships/search_programs/{v?}', 'PartnershipsController@searchPrograms');
                  });

            // /////////////////////////////////////////////////////////////////////////////////

            Route::group(['prefix' => '/community-benefits',
                  'namespace' => 'University', ], function () {
                      Route::get('/{mode?}', 'CommunityBenefitsController@index');
                      Route::post('/', 'CommunityBenefitsController@store');
                      Route::get('/new/{mode?}/{fiscal_year?}', 'CommunityBenefitsController@create');
                      Route::get('/{id}/edit', 'CommunityBenefitsController@edit');
                      Route::post('/{id}/update/{close?}', 'CommunityBenefitsController@update');
                      Route::get('/{id}/delete', 'CommunityBenefitsController@delete');
                  });

            // /////////////////////////////////////////////////////////////////////////////////
        });
