<?php

/////////////////////////////////////////////////////////////////////////////////////
//
//                                    OSTRICH
//
/////////////////////////////////////////////////////////////////////////////////////

Route::group(['prefix'          => '/ostrich',
              'namespace'       => 'Campaign\Ostrich',
             ], function () {

    Route::group(['middleware' => ['ostrich']], function () {
        Route::get('/dashboard',                    'HomeController@dashboard');
        Route::get('/walk',                         'HomeController@walk');
        Route::get('/phone',                        'HomeController@phone');
        Route::get('/logout',                       'AuthController@logout');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/login-user',                   'AuthController@loginUser');
    });

    Route::get('/',                             'AuthController@loginPage');
    Route::get('/login-by-link/{uuid}',         'AuthController@loginByLink');
    Route::post('/send-link',                   'AuthController@sendLink');    


});

Route::group(['prefix'          => '/campaign',
              'middleware'      => ['auth', 'database_set', 'activated'],
              'namespace'       => 'Campaign',
             ], function () {


    Route::group(['prefix' => '/volunteers'], function () {
        Route::get('/',                         'VolunteersController@index');
    });

    Route::group(['prefix' => '/volunteers-new'], function () {
        Route::get('/',                         'VolunteersController@indexNew');
    });

    Route::group(['prefix' => '/opportunities'], function () {
        Route::get('/',                         'OpportunitiesController@index');
        Route::get('/{type}/{id}',              'OpportunitiesController@show');
    });

});


/////////////////////////////////////////////////////////////////////////////////////
//
//                                    CAMPAIGN
//
/////////////////////////////////////////////////////////////////////////////////////

Route::get('web-forms/{id}', 'Campaign\WebFormController@iframe');
Route::post('web-forms/{id}', 'Campaign\WebFormController@signUp');

Route::group(['prefix'          => '/campaign',
              'middleware'      => ['auth', 'database_set', 'activated'],
              'namespace'		=> 'Campaign',
             ], function () {

                
    Route::group(['middleware' => ['full_users_only']], function () {

                 Route::get('/', 'DashboardController@dashboard');

                 Route::get('web-forms', 'WebFormController@index');
                 Route::post('web-forms', 'WebFormController@store');

                 Route::group(['prefix' => '/campaigns'], function () {
                     Route::get('/', 'CampaignsController@index');
                     Route::get('/{id}/edit', 'CampaignsController@edit');
                     Route::post('/', 'CampaignsController@store');
                     Route::post('/{id}/update/{close?}', 'CampaignsController@update');
                     Route::get('/{id}/delete', 'CampaignsController@delete');
                 });

                 Route::group(['prefix' => '/lists'], function () {
                     Route::get('/{id}/export', 'CampaignListsController@export');
                     Route::post('/export', 'CampaignListsController@exportForm');
                     Route::get('/', 'CampaignListsController@index');
                     Route::get('/new', 'CampaignListsController@new');
                     Route::post('/', 'CampaignListsController@store');
                     Route::post('/{id}/update/{close?}', 'CampaignListsController@update');
                     Route::get('/{id}', 'CampaignListsController@show');
                     Route::get('/{id}/edit', 'CampaignListsController@edit');
                     Route::get('/{id}/delete', 'CampaignListsController@delete');
                     Route::get('/{id}/map', 'CampaignListsController@map');
                     Route::get('/{id}/assign', 'CampaignListsController@assign');
                     Route::get('/{id}/print', 'CampaignListsController@print');

                 });

                 

                Route::group(['prefix' => '/phonebank'], function () {
                     Route::get('/{id}', 'CampaignListsController@showForGuests');
                });

                 Route::group(['prefix' => '/tags'], function () {
                     Route::get('/', 'TagsController@index');
                     Route::get('/{id}', 'TagsController@show');
                     Route::get('/{id}/edit', 'TagsController@edit');
                     Route::post('/', 'TagsController@store');
                     Route::post('/{id}/update/{close?}', 'TagsController@update');
                     Route::get('/{id}/delete', 'TagsController@delete');
                 });

                 Route::group(['prefix' => '/progress'], function () {
                     Route::get('/', 'ProgressController@index');
                 });

                 Route::group(['prefix' => '/actions'], function () {
                     Route::get('/', 'ActionsController@index');
                     Route::get('/regulars', 'ActionsController@regulars');
                     Route::get('/export', 'ActionsController@export');
                 });

                 Route::group(['prefix' => '/mapping'], function () {
                     Route::get('/', 'MappingController@index');
                 });

                 Route::group(['prefix' => '/questionnaires'], function () {
                     Route::get('/', 'QuestionnairesController@index');
                     Route::get('/{id}/edit', 'QuestionnairesController@edit');
                     Route::post('/', 'QuestionnairesController@store');
                     Route::post('/{id}/update/{close?}', 'QuestionnairesController@update');
                     Route::get('/{id}/delete', 'QuestionnairesController@delete');
                     Route::get('/{id}/answers', 'QuestionnairesController@answers');
                     Route::get('/{id}/questions/{q_id}/getAJAX', 'QuestionnairesController@getAJAX');
                     Route::post('/{id}/questions/update', 'QuestionnairesController@updateQuestion');
                     Route::get('/{id}/questions/{q_id}/delete', 'QuestionnairesController@deleteQuestion');
                 });

                 Route::group(['prefix' => '/donations'], function () {
                     Route::get('/', 'DonationsController@index');
                     Route::get('/{id}/edit', 'DonationsController@edit');
                     Route::post('/', 'DonationsController@store');
                     Route::post('/{id}/update/{close?}', 'DonationsController@update');
                     Route::get('/{id}/delete', 'DonationsController@delete');
                     Route::post('/filter', 'DonationsController@filter');
                     Route::post('/search', 'DonationsController@search');
                     Route::get('/export', 'DonationsController@export');
                 });

                 Route::group(['prefix' => '/events'], function () {
                     Route::get('/', 'CampaignEventsController@index');
                     Route::get('/{id}', 'CampaignEventsController@show');
                     Route::get('/{id}/edit', 'CampaignEventsController@edit');
                     Route::post('/', 'CampaignEventsController@store');
                     Route::post('/{id}/update/{close?}', 'CampaignEventsController@update');
                     Route::get('/{id}/delete', 'CampaignEventsController@delete');
                     Route::post('/filter', 'CampaignEventsController@filter');
                     Route::get('/{id}/guests', 'CampaignEventsController@guestsIndex');
                     Route::get('/{id}/add_invitation/{partic}', 'CampaignEventsController@addInvite');
                     Route::get('/{id}/remove_invitation/{partic}', 'CampaignEventsController@RemoveInvite');
                     Route::get('/{id}/guest_count/{participant}/{c}', 'CampaignEventsController@guestCount');
                     Route::get('/{id}/tof/{participant}/{field}', 'CampaignEventsController@toggleTOF');
                     Route::get('/{id}/total_guests', 'CampaignEventsController@totalGuests');
                 });

                

                 Route::group(['prefix' => '/participants'], function () {
                     Route::post('/', 'ParticipantsController@store');
                     Route::get('/', 'ParticipantsController@index');
                     Route::get('/new', 'ParticipantsController@new');
                     Route::get('/tagged/{tag_id}', 'ParticipantsController@showTag');
                     Route::get('/export', 'ParticipantsController@export');
                     Route::get('/{id}', 'ParticipantsController@show');
                     Route::get('/{id}/edit', 'ParticipantsController@edit');
                     Route::get('/{id}/add-action', 'ParticipantsController@addAction');
                     Route::post('/{id}/update/{close?}', 'ParticipantsController@update');
                     Route::get('/{id}/delete', 'ParticipantsController@delete');
                     Route::get('/lookup/{mode}/{v?}/{extra?}', 'ParticipantsController@lookup');
                     Route::get('/{id}/campaign/{c}/support/{level}', 'ParticipantsController@setSupport');
                     Route::get('/search/{change_query?}', 'ParticipantsController@index');
                     Route::get('/toggle_minimize', 'ParticipantsController@toggleMinimize');
                     Route::get('/toggle_search', 'ParticipantsController@toggleSearch');

                     Route::get('/{id}/tags/{tag}/toggle', 'ParticipantsController@toggleTag');
                 });

                 Route::group(['prefix' => '/voters'], function () {
                     Route::get('/', 'ParticipantsController@votersIndex');
                 });
        });

    Route::get('/phonebank/{id}', 'CampaignListsController@showForGuests');

});


