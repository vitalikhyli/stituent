<?php

/////////////////////////////////////////////////////////////////////////////////////
//
//                                ADMIN / DEV
//
/////////////////////////////////////////////////////////////////////////////////////

Route::group(['prefix'      => 'admin',
              'middleware'  => ['auth', 'database_set', 'activated'],
            ], function () {
                Route::get('mock/restore', '\App\Http\Controllers\Auth\MockController@restore');
            });

Route::group(['prefix'      => 'admin',
              'namespace' => 'Admin',
              'middleware'  => ['auth', 'database_set', 'activated', 'developer_only'],
            ], function () {
                Route::get('/home', 'DashboardController@dashboard');

                Route::get('/terms',                        'TermsController@index');
                Route::get('/terms/new',                    'TermsController@new');
                Route::get('/terms/{id}/edit',              'TermsController@edit');
                Route::post('/terms/{id}/update/{close?}',  'TermsController@update');

                Route::get('/prospects', 'AccountProspectsController@index');
                Route::get('/marketing', 'AccountProspectsController@marketingIndex');
                Route::get('/marketing/stats/{year?}/{month?}', 'AccountProspectsController@marketingStats');
                Route::post('/marketing', 'AccountProspectsController@manualAddCandidate');
                Route::get('/marketing/commands/{command}', 'AccountProspectsController@runCommand');
                Route::get('/marketing/link_candidate/{id}/{voter}', 'AccountProspectsController@linkCandidate');
                Route::get('/marketing/{id}/edit', 'AccountProspectsController@editCandidate');
                Route::post('/marketing/{id}/update/{close?}', 'AccountProspectsController@updateCandidate');

                Route::get('mock/{id}', '\App\Http\Controllers\Auth\MockController@loginAs');

                Route::get('/notices', 'NoticesController@index');
                Route::get('/notices/{id}/approve', 'NoticesController@approve');
                Route::get('/notices/{id}/unapprove', 'NoticesController@unapprove');
                Route::get('/notices/{id}/edit', 'NoticesController@edit');
                Route::get('/notices/{id}/archive', 'NoticesController@archive');
                Route::get('/notices/{id}/unarchive', 'NoticesController@unarchive');
                Route::get('/notices/new', 'NoticesController@new');
                Route::post('/notices/{id}/update/{close?}', 'NoticesController@update');

                Route::get('/uploads',                      'UploadController@index');
                Route::post('/uploads',                     'UploadController@uploadFile');
                Route::get('/uploads/{import_id}/edit',     'UploadController@edit');
                Route::get('/uploads/{import_id}/download', 'UploadController@download');
                Route::get('/uploads-standalone',           'UploadController@indexStandalone');
                Route::get('/uploads/paste',                'UploadController@paste');

                Route::get('/activity', 'ActivityController@main');

                Route::get('/phone-app', 'PhoneAppController@index');
                Route::post('/phone-app', 'PhoneAppController@store');

                Route::get('/queries', 'QueriesController@index');

                Route::get('/errors', 'ToDoController@errorLogs');
                Route::get('/arrayfix/{team_id?}', 'ToDoController@arrayFix');
                Route::get('/arraycheck/{person_id?}', 'ToDoController@arrayCheck');
                Route::get('/users/{id}/addpermissions', 'AccountsController@addPermissions');

                Route::get('/prospects', 'ProspectsController@index');
                Route::post('/prospects', 'ProspectsController@add');

                Route::get('/userlogs', 'UserLogsController@averageTime');
                Route::post('/userlogs/dates', 'UserLogsController@averageTimeDates');
                Route::get('/userlogs/clicks', 'UserLogsController@totalClicks');

                // Route::get('/', 'SlicesController@index');

                Route::group(['prefix' => '/slices'], function () {
                    Route::get('/',                     'SlicesController@index');
                    Route::post('/',                    'SlicesController@store');
                    Route::get('/{id}/edit',            'SlicesController@edit');
                    Route::post('/{id}/update/{c?}',    'SlicesController@update');
                    Route::get('/{id}/delete',           'SlicesController@delete');
                });

                Route::group(['prefix' => '/data'], function () {
                    Route::get('/', 'ImportsController@dataTableIndex');
                    Route::get('/startworker', 'WorkersController@startWorker');
                    Route::get('/stopworker', 'WorkersController@stopWorker');
                    Route::get('/{id}/list-slices', 'ImportsController@ajaxListSlices');
                    Route::get('/list-tables', 'ImportsController@ajaxListTables');
                });

                Route::group(['prefix' => '/upload'], function () {
                    Route::get('/', 'ImportsController@uploadIndex');
                    Route::post('/step/{step}', 'ImportsController@upload');
                });

                Route::get('/jobs', 'JobsController@jobsIndex');
                Route::post('/jobs/rollback/{job_id}', 'JobsController@rollback');

                Route::get('/workers', 'WorkersController@index');
                Route::get('/workers/{id}', 'WorkersController@show');

                Route::group(['prefix' => '/import'], function () {
                    Route::get('/{import_id}/edit', 'ImportsController@edit');
                    Route::post('/{import_id}/save/{close?}', 'ImportsController@save');
                    Route::get('/{import_id}/slice', 'ImportsController@slice');
                    Route::get('/{import_id}/deploy', 'ImportsController@deploy');
                    Route::get('/{import_id}/archive', 'ImportsController@archive');
                    Route::get('/{import_id}/copy', 'ImportsController@copy');
                    Route::get('/{import_id}/moveSlicePointers', 'ImportsController@moveSlicePointers');
                    Route::get('/{import_id}/repopulateSlices', 'ImportsController@repopulateSlices');
                    Route::post('/merge/', 'ImportsController@merge');
                });

                Route::get('/accounts', 'AccountsController@index');
                Route::get('/accounts/audit', 'AccountsController@audit');
                Route::get('/accounts/setup', 'AccountsController@setup');
                Route::get('/accounts/new', 'AccountsController@new');
                Route::post('/accounts/save', 'AccountsController@save');
                Route::get('/accounts/{id}/edit', 'AccountsController@edit');
                Route::post('/accounts/{id}/update/{close?}', 'AccountsController@update');
                Route::get('/accounts/{id}/users/{user_id}/edit', 'AccountsController@editUser');
                Route::post('/accounts/{id}/users/{user_id}/update/{clo?}', 'AccountsController@updateUser');
                Route::get('/accounts/{id}/teams/{user_id}/edit', 'AccountsController@editTeam');
                Route::post('/accounts/{id}/teams/{user_id}/update/{clo?}', 'AccountsController@updateTeam');
                // Route::get('/accounts/{id}/users',                       'AccountsController@users');

                Route::get('/streets', 'StreetsController@index');

                Route::get('/billygoat', 'AccountsController@billyGoatIndex');
                Route::get('/billygoat/{account_id}/create', 'AccountsController@createBillyGoatAccount');
                Route::post('/billygoat/{account_id}/link/', 'AccountsController@linkBillyGoatAccount');

                Route::get('/todo', 'ToDoController@todoGet');
                Route::post('/todo/save', 'ToDoController@todoSave');

                Route::get('/groups', 'GroupsController@index');
                Route::view('/plans', 'admin.plans.plan');
                Route::view('/checklist', 'admin.plans.pages');

                Route::get('/commands',                 'CommandsController@index');
                Route::get('/commands/{command}',       'CommandsController@run');

            });

Route::group([
            'prefix'      => 'admin',
            'middleware'  => ['auth', 'database_set', 'activated'],
            ], function () {
                Route::get('/team_col/{col}/{v}', 'UsersController@setTeamCol');
                Route::get('/change_team/{team_id}', 'UsersController@changeTeam');
            });
