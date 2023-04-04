<?php

/////////////////////////////////////////////////////////////////////////////////////
//
//                                   DOCS
//
/////////////////////////////////////////////////////////////////////////////////////

Route::group(['prefix' => 'docs', 'namespace' => 'Docs'], function () {
    Route::get('/{section?}/{subsection?}/{page?}', 'DocsController@index');
});

Auth::routes();

/////////////////////////////////////////////////////////////////////////////////////
//
//                                     MARKETING
//                                  NOT LOGGED IN YET
//
/////////////////////////////////////////////////////////////////////////////////////

Route::view('/shared-cases', 'marketing.shared-cases');


Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout'); //Log out to App-specific welcome page
Route::get('/campaign/login', 'Campaign\DashboardController@login');

Route::get('/', function () {
    return redirect('https://app.communityfluency.com');
});
Route::view('/videos', 'welcome-videos');
Route::view('/constituent', 'auth.login')->name('welcome-office');
Route::view('/campaigns', 'auth.login-campaign')->name('welcome-campaign');
Route::view('/universities', 'auth.login-u')->name('welcome-u');

Route::view('reports-and-exports', 'auth.login');
Route::view('mapping', 'auth.login');

Route::view('terms', 'stripe.terms');
Route::view('privacy', 'stripe.privacy');

Route::post('/sandbox/{app}', '\App\Http\Controllers\Auth\MockController@sandbox');
Route::get('/request-demo', 'HomeController@requestDemo');
Route::post('/request-demo/submit', 'HomeController@requestDemoSend');
Route::get('/request-demo/captcha', 'HomeController@captcha');
Route::view('/how-it-works', 'welcome.how-it-works');
Route::view('/who-we-are', 'welcome.who-we-are');
Route::view('/loyalty', 'welcome.loyalty');

Route::view('marketing/campaign-brochure', 'marketing.campaign-brochure');
Route::view('marketing/summary', 'marketing.summary');
Route::view('marketing/ma-summary', 'marketing.ma-summary');
Route::get('marketing/summary/print', 'PDFController@marketingSummary');



Route::get('/link/{token}', 'UsersController@checkToken');

Route::get('/billygoat/{bgid}', 'StripeController@lookupByBillygoat');
Route::get('/payments/{simple_payment}', 'StripeController@simplePaymentLink');
Route::get('/stripe/payment-options/{account_uuid}', 'StripeController@options');
Route::post('/stripe/review-payment/', 'StripeController@review');
Route::get('/stripe/success/{account_uuid}', 'StripeController@success');

Route::group(['middleware' => ['auth', 'database_set', 'activated', 'is_active']], function () {
    Route::view('/tos', 'welcome.tos');
    Route::post('/tos/agree', 'HomeController@acceptTerms');
    Route::get('/home', 'HomeController@whichDashboard');

    Route::get('/stripe/payment-options', 'StripeController@redirectAccountOptions');

    Route::get('/stripe/cancel', 'StripeController@cancel');
});

Route::group(['middleware' => ['auth', 'database_set']], function () {
    Route::get('/inactive', 'HomeController@inactive');
});

Route::get('/lists/{uuid}', 'Campaign\CampaignListsController@loginGuestByLink');



/////////////////////////////////////////////////////////////////////////////////////
//
//                                SHARED FEATURES
//
/////////////////////////////////////////////////////////////////////////////////////

// Route::group(['middleware' => ['auth', 'database_set', 'activated', 'is_active']], function () {
//     Route::post('basechat/rooms', 'BaseChatController@addRoom');
//     Route::post('basechat/rooms/{id}/save', 'BaseChatController@save');
//     Route::post('basechat/rooms/{id}/archive', 'BaseChatController@archive');

//     Route::post('basechat/rooms/{id}/send-message', 'BaseChatController@sendMessage');
//     Route::get('basechat/rooms/{id}', 'BaseChatController@loadRoom');
//     Route::get('basechat/rooms/{id}/mark-read', 'BaseChatController@markRoomAsRead');
//     // Route::get('basechat/check-unread', 'BaseChatController@checkUnread');
//     // Route::get('basechat/rooms/{id}/messages', 'BaseChatController@loadMessages');
//     Route::get('basechat/rooms/{id}/update', 'BaseChatController@updateChat');
// });

Route::group(['middleware' => ['auth', 'full_users_only', 'database_set', 'activated', 'is_active']], function () {
    Route::get('calendar/events/full', 'CalendarController@updateAllEvents');
    Route::get('events/{date}', 'CalendarController@eventsByDate');
    Route::get('/user/{id}/memory/{key}/{value}', 'UsersController@addMemory');

    Route::post('call-log', 'CallLogController@store');
    Route::post('call-log/search', 'CallLogController@searchAllLogs');
    Route::get('call-log/{id}/edit', 'CallLogController@edit');
    Route::get('call-log/{id}/connect', 'CallLogController@connect');
    Route::post('call-log/{id}/update', 'CallLogController@update');
    Route::post('call-log/{id}/update-connections', 'CallLogController@updateConnections');
    Route::post('call-log/{id}/delete', 'CallLogController@delete');
    Route::get('call-log_lookup/{value?}', 'CallLogController@lookUp');
    Route::get('call-log/search/{v}/{call}', 'CallLogController@search');
    Route::get('call-log/search_entities/{v}/{call}', 'CallLogController@searchEntities');
    Route::get('call-log/reports', 'CallLogController@report');
});

/////////////////////////////////////////////////////////////////////////////////////
//
//                                    TEAMWORK
//
/////////////////////////////////////////////////////////////////////////////////////

Route::group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function () {
    Route::get('/', 'TeamController@index')->name('teams.index');
    Route::get('create', 'TeamController@create')->name('teams.create');
    Route::post('teams', 'TeamController@store')->name('teams.store');
    Route::get('edit/{id}', 'TeamController@edit')->name('teams.edit');
    Route::put('edit/{id}', 'TeamController@update')->name('teams.update');
    Route::delete('destroy/{id}', 'TeamController@destroy')->name('teams.destroy');
    Route::get('switch/{id}', 'TeamController@switchTeam')->name('teams.switch');

    Route::get('members/{id}', 'TeamMemberController@show')->name('teams.members.show');
    Route::get('members/resend/{invite_id}', 'TeamMemberController@resendInvite')->name('teams.members.resend_invite');
    Route::post('members/{id}', 'TeamMemberController@invite')->name('teams.members.invite');
    Route::delete('members/{id}/{user_id}', 'TeamMemberController@destroy')->name('teams.members.destroy');

    Route::get('accept/{token}', 'AuthController@acceptInvite')->name('teams.accept_invite');
});

/////////////////////////////////////////////////////////////////////////////////////
//
//                                BASIC ROUTES
//
/////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware'    => ['auth', 'full_users_only', 'database_set', 'activated', 'is_active'],
            ], function () {

                Route::get('/{app_type}/imap', 'ImapController@index');

    /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/useruploads'], function () {
                    Route::get('/', 'UserUploadsController@index');
                    Route::post('/upload', 'UserUploadsController@uploadFile');
                    Route::get('/{id}/latest', 'UserUploadsController@latest');
                    Route::get('/{id}/import', 'UserUploadsController@import');
                    Route::get('/{id}/match', 'UserUploadsController@match');
                    Route::get('/{id}/integrate', 'UserUploadsController@integrate');
                    Route::get('/{id}/delete', 'UserUploadsController@delete');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}'], function () {
                    Route::get('/', 'DashboardController@dashboard');
                    Route::get('/dashboard/activity-map', 'DashboardController@activityMap');
                    Route::get('/dashboard_search/{value?}', 'PeopleController@searchDashboard');
                    Route::get('/paste', 'UserUploadsController@pasteAndLabelPage');
                });


                Route::get('office/special', 'SpecialPagesController@office');
                
                Route::get('campaign/special', 'SpecialPagesController@campaign');
                Route::get('campaign/special/household-members', 'SpecialPagesController@householdMembers');

                /////////////////////////////////////////////////////////////////////////////////

               
                      Route::get('{app_type}/organizations/{id}/contacts/{contact}/edit', 'EntitiesController@editContact');
                      Route::post('{app_type}/organizations/{id}/contacts/{contact}/update/{c?}', 'EntitiesController@updateContact');
                      Route::get('{app_type}/organizations/{id}/contacts/{contact}/delete', 'EntitiesController@deleteContact');
                      Route::post('{app_type}/organizations/{en}/add_contact', 'EntitiesController@addContactToEntity');

                      Route::get('{app_type}/organizations/', 'EntitiesController@index');
                      Route::get('{app_type}/organizations/new/{v?}', 'EntitiesController@new');
                      Route::get('{app_type}/organizations/service-learning-partnerships', 'EntitiesController@partnerships');
                      Route::get('{app_type}/organizations/type/{type}', 'EntitiesController@indexType');
                      Route::get('{app_type}/organizations/{id}', 'EntitiesController@show');
                      Route::get('{app_type}/organizations/{id}/edit', 'EntitiesController@edit');
                      Route::post('{app_type}/organizations/{id}/update', 'EntitiesController@update');
                      Route::get('{app_type}/organizations/search/{v?}', 'EntitiesController@search');
                      Route::post('{app_type}/organizations/save', 'EntitiesController@save');
                      Route::get('{app_type}/organizations/{id}/delete', 'EntitiesController@delete');
                      Route::post('{app_type}/organizations/{id}/edit-type', 'EntitiesController@editType');

                      Route::post('{app_type}/organizations/{id}/link_person', 'EntitiesController@linkPerson');
                      Route::post('{app_type}/organizations/{id}/update_person', 'EntitiesController@updateRelationship');
                      Route::post('{app_type}/organizations/{id}/delete_person', 'EntitiesController@deleteRelationship');

                      Route::get('{app_type}/organizations/{id}/person/{person}/edit', 'EntitiesController@linkPersonEdit');
                      Route::get('{app_type}/organizations/{id}/relationship/{pivot}/delete', 'EntitiesController@unlinkPerson');

                      Route::get('{app_type}/organizations/{id}/partnerships/new', 'PartnershipsController@new');
                      Route::post('{app_type}/organizations/{id}/partnerships/save', 'PartnershipsController@store');
                      Route::post('{app_type}/organizations/{id}/partnerships/{pshipid}/update/{c?}', 'PartnershipsController@update');
                      Route::post('{app_type}/organizations/{id}/partnerships', 'PartnershipsController@store');
                      Route::get('{app_type}/organizations/{id}/partnerships/{pshipid}/edit', 'PartnershipsController@edit');
                      Route::get('{app_type}/organizations/{id}/partnerships/search_programs/{v?}', 'PartnershipsController@searchPrograms');
                  
                

                Route::get('office/shared-cases', 'SharedCasesController@index');
                Route::get('office/shared-cases/enable', 'SharedCasesController@enable');

                Route::get('{app_type}/link-voters', 'VoterLinkController@index');

                Route::get('{app_type}/birthdays', 'BirthdaysController@voters');
                Route::get('{app_type}/birthdays/people', 'BirthdaysController@people');

                Route::group(['prefix' => '{app_type}/constituents'], function () {

                    Route::post('/searches/update', 'PeopleSearchesController@update');
                    Route::get('/searches/save', 'PeopleSearchesController@save');
                    Route::post('/searches/{id}/export/download', 'PeopleSearchesController@download');
                    Route::get('/searches/{id}/export', 'PeopleSearchesController@export');
                    Route::get('/searches/{id}/delete', 'PeopleSearchesController@delete');
                    Route::get('/searches/{id}', 'PeopleSearchesController@show');
                    Route::get('/searches', 'PeopleSearchesController@index');
                    Route::get('/linked', 'PeopleController@indexLinked');
                    Route::get('/', 'PeopleController@indexAll');
                    Route::get('/regulars', 'PeopleController@regulars');
                    Route::get('/new', 'PeopleController@new');
                    
                    Route::post('/{person_id}/add-group', 'PeopleController@addGroup');
                    Route::get('/list', 'PeopleController@list');
                    Route::get('/{id}', 'PeopleController@show');
                    Route::get('/{id}/set_primary_email/{email}', 'PeopleController@setPrimaryEmail');
                    Route::get('/{id}/set_primary_phone/{phone}', 'PeopleController@setPrimaryPhone');
                    Route::get('/{id}/district/{district_type}', 'PeopleController@districtEdit');
                    Route::post('/{id}/district/update', 'PeopleController@districtUpdate');
                    Route::get('/{id}/districtRevert/{district_type}', 'PeopleController@districtRevert');
                    Route::get('/{id}/merge', 'PeopleController@merge');
                    Route::get('/{id}/edit', 'PeopleController@edit');
                    Route::get('/{id}/delete', 'PeopleController@delete');
                    Route::post('/{id}/update', 'PeopleController@update');
                    Route::get('/{id}/sync_voter_address', 'PeopleController@syncVoterAddress');
                    Route::get('/{id}/business/edit', 'PeopleController@editBusiness');
                    Route::post('/{id}/business/update', 'PeopleController@updateBusiness');
                    Route::post('/save', 'PeopleController@save');
                    Route::get('/{id}/masteremail/{switch}', 'PeopleController@masterEmailList');
                    Route::get('/{id}/contacts/{contact}/edit', 'ContactsController@edit');
                    Route::post('/{id}/add_contact', 'ContactsController@addContactToPerson');
                    Route::get('/{id}/category/{catid}/new', 'GroupsController@newInstance');
                    Route::post('/{id}/instance/save/{close?}', 'GroupsController@saveInstance');
                    Route::post('/{id}/contacts/{contact}/update/{c?}', 'ContactsController@update');
                    Route::get('/{id}/contacts/{contact}/delete', 'ContactsController@delete');
                });

                Route::get('/office/merge-constituents', 'MergeConstituentsController@index');

                Route::group(['prefix' => '{app_type}/exports'], function () {
                    Route::get('/', 'ExportsController@index');
                    Route::get('/{id}', 'ExportsController@show');
                    Route::post('/download', 'ExportsController@download');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::get('{app_type}/lookup/{str?}', 'LookupController@main');
                Route::get('{app_type}/entities/{id}/lookup/{v?}', 'LookupController@linkToEntity');

                /////////////////////////////////////////////////////////////////////////////////

                Route::get('{app_type}/metrics/engagement', 'MetricsController@engagement');
                Route::get('{app_type}/metrics/cases', 'MetricsController@cases');
                Route::get('{app_type}/metrics/contacts', 'MetricsController@contacts');

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/contacts'], function () {
                    Route::get('/', 'ContactsController@index');
                    Route::get('/mine', 'ContactsController@myContacts');
                    Route::get('/{id}/convert_to_case/{person?}', 'ContactsController@convertToCase');
                    Route::post('/{id}/link_to_case', 'ContactsController@linkToCase');
                    Route::get('/{id}/connect/{person}', 'ContactsController@connectPerson');
                    Route::get('/{id}/connect_entity/{entity}', 'ContactsController@connectEntity');
                    Route::get('/lookup/{value?}', 'ContactsController@lookUp');
                    Route::get('/{id}/edit', 'ContactsController@editIndependently');
                    Route::post('/{id}/update/{close?}', 'ContactsController@updateIndependently');
                    Route::get('/{id}/delete', 'ContactsController@deleteIndependently');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/cases'], function () {
                    Route::get('/{id}/print', 'CasesController@print');
                    Route::get('/report', 'CaseReportController@web');
                    Route::get('/report-serial/{json}', 'CaseReportController@webSerial');

                    Route::get('/report/count', 'CaseReportController@count');
                    Route::get('/report/pdf', 'CaseReportController@pdf');

                    Route::get('/search/{scope?}/{status?}/{user?}/{v?}', 'CasesController@search');
                    Route::get('/new/{person_id?}', 'CasesController@new');
                    Route::get('/{id}', 'CasesController@show');
                    Route::get('/{id}/edit', 'CasesController@edit');
                    Route::get('/{id}/delete', 'CasesController@delete');
                    Route::post('/{id}/closingremarks/update', 'CasesController@closingRemarks');

                    Route::get('/', 'CasesController@index');
                    // Route::get('/list/{scope?}/{status?}/{user?}', 'CasesController@index');
                    Route::get('/export/{scope?}/{status?}', 'CasesController@export');
                    Route::post('/{id}/sync', 'CasesController@syncPeople');
                    Route::get('/{id}/linkperson/{person_id}', 'CasesController@linkPerson');
                    Route::post('/{id}/sync_hh', 'CasesController@syncHouseholds');
                    Route::get('/{id}/link_hh/{hh_id}', 'CasesController@linkHousehold');
                    Route::post('/save', 'CasesController@save');
                    Route::post('/{id}/save/{close?}', 'CasesController@update');
                    Route::get('/{id}/assign_user/{user_id}', 'CasesController@assignUser');
                    Route::get('/{id}/notify_user/{user_id}', 'CasesController@notifyUser');
                    Route::get('/{id}/markas/{status}', 'CasesController@status');
                    Route::post('/{id}/add_contact', 'CasesController@addContact');
                    Route::get('/{id}/contacts/{contact}/edit', 'CasesController@editContact');
                    Route::post('/{case}/contacts/{contact}/update/{cl?}', 'ContactsController@update');
                    Route::get('/{case}/contacts/{contact}/delete', 'ContactsController@delete');
                });

                Route::get('{app_type}/cases_undo/{id}/{previous_url}', 'CasesController@undo');
                Route::get('{app_type}/cases_searchpeople/{case_id}/{v?}', 'CasesController@searchPeople');
                Route::get('{app_type}/cases_searchhouseholds/{case_id}/{value?}', 'HouseholdsController@searchCasesHouseholds');

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/groups'], function () {
                    Route::get('/', 'GroupsController@index');
                    Route::get('/archived', 'GroupsController@indexArchived');
                    Route::get('/{id}', 'GroupsController@show');
                    Route::get('/{id}/export', 'GroupsController@export');
                    Route::get('/{id}/merge', 'GroupsController@mergeAsk');
                    Route::post('/merge_confirm', 'GroupsController@mergeConfirm');
                    Route::get('/{id}/position/{support}', 'GroupsController@showPosition');
                    Route::get('/{id}/delete', 'GroupsController@delete');
                    Route::get('/{id}/archive/{reverse?}', 'GroupsController@archive');
                    Route::get('/{id}/convert-to-legislation', 'GroupsController@convertToLegislation');
                    Route::get('/{id}/edit', 'GroupsController@edit');
                    Route::post('/{id}/update/{close?}', 'GroupsController@update');
                    Route::post('/new', 'GroupsController@new');
                    Route::get('/instance/{pivot_id}', 'GroupsController@showInstance');
                    Route::post('/instance/{pivot_id}/update/{close?}', 'GroupsController@updateInstance');
                    Route::get('/instance/{pivot_id}/delete', 'GroupsController@deleteInstance');
                    Route::get('{id}/searchpeople/{v?}', 'GroupsController@searchPeople');
                    Route::get('{id}/addperson/{person}', 'GroupsController@linkPerson');
                    Route::post('{id}/sync', 'GroupsController@syncPeople');
                    Route::post('{id}/searchinstance', 'GroupsController@searchInstance');
                    Route::get('{id}/person/{person}/getnotes', 'GroupsController@instanceNotes');
                    Route::post('/{id}/savenote', 'GroupsController@saveNote');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/bulkgroups'], function () {
                    Route::get('/add/{group}/{person}/{team}', 'GroupsController@bulkGroupsAdd');
                    Route::get('/remove/{group}/{person}/{team}', 'GroupsController@bulkGroupsRemove');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/followups'], function () {
                    Route::get('/pending', 'FollowupsController@index');
                    Route::get('/done', 'FollowupsController@indexDone');
                    Route::get('/done/{id}/{tof}', 'FollowupsController@followupDone');
                    Route::get('/count', 'FollowupsController@followupCount');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/emails'], function () {
                    Route::get('/master', 'PeopleController@masterEmailConfirm');
                    Route::post('/master/update', 'PeopleController@masterEmailUpdate');

                    Route::get('/', 'BulkEmailController@index');
                    Route::get('/queued-rows', 'BulkEmailController@indexQueuedRows');
                    Route::get('/queued', 'BulkEmailController@indexQueued');
                    Route::get('/completed', 'BulkEmailController@indexCompleted');
                    Route::get('/show_all_queue', 'BulkEmailController@indexAllQueue');
                    Route::get('/new', 'BulkEmailController@new');
                    Route::post('/save', 'BulkEmailController@store');
                    Route::get('/{id}/edit/', 'BulkEmailController@edit');
                    Route::get('/{id}/test/', 'BulkEmailController@testAsk');
                    Route::post('/{id}/testconfirm/', 'BulkEmailController@testConfirm');
                    Route::get('/{id}/queue/', 'BulkEmailController@queueAsk');
                    Route::get('/{id}/queueconfirm/', 'BulkEmailController@queueConfirm');
                    Route::get('/{id}/queueshow/', 'BulkEmailController@queueShow');
                    Route::get('/{id}/queuehalt/', 'BulkEmailController@queueHalt');
                    Route::get('/{id}/delete/', 'BulkEmailController@delete');
                    Route::post('/{id}/update/{thenwhat?}', 'BulkEmailController@update');
                    Route::get('/{id}/copy', 'BulkEmailController@copy');
                    Route::get('/lists/{id}', 'BulkEmailController@showList');
                    Route::get('/adminsend', 'BulkEmailController@adminSend');
                    Route::get('/update-recipients', 'BulkEmailController@updateRecipients');
                    Route::get('/view-recipients', 'BulkEmailController@viewRecipients');
                    Route::get('/{id}/print', 'BulkEmailController@showPrintable');

                    Route::get('/codes/{id}/delete', 'BulkEmailCodesController@delete');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/users'], function () {
                    Route::get('/{id}/jointeam/{teamid}', 'UsersController@joinTeam');
                    Route::get('/{id}/leaveteam/{teamid}', 'UsersController@leaveTeam');
                    Route::get('/{user_id}/edit', 'UsersController@edit');
                    Route::get('/new', 'UsersController@new');
                    Route::post('/save', 'UsersController@save');
                    Route::post('/{user_id}/update/{close?}', 'UsersController@update');
                    Route::get('/settings', 'UsersController@settings');
                    Route::get('/emaillinktouser/{id}', 'UsersController@emailLink');
                    Route::get('/team', 'UsersController@teamIndex');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/households'], function () {
                    Route::get('/',                 'HouseholdsController@indexAll');
                    Route::get('/{id}',             'HouseholdsController@show');
                    Route::get('/{id}/map',         'HouseholdsController@singlePointMap');
                    Route::get('/{id}/edit',        'HouseholdsController@edit');
                    Route::post('/{id}/update/{c?}',  'HouseholdsController@update');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/categories'], function () {
                    Route::post('/add', 'CategoriesController@save');
                    Route::get('/{id}/edit', 'CategoriesController@edit');
                    Route::get('/{id}/archive', 'CategoriesController@archive');
                    Route::post('/{id}/update/{close?}', 'CategoriesController@update');
                    Route::get('/{id}/delete', 'CategoriesController@delete');
                    Route::get('/{id}/groups_as_checkboxes', 'CategoriesController@groupsCheckboxes');
                    Route::get('/{id}/groups_as_radios/{m?}/{g?}', 'CategoriesController@groupsRadios');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/maps'], function () {
                    Route::get('/', 'MapsController@index');
                    Route::get('/voters', 'MapsController@voters');
                    Route::get('/groups', 'MapsController@groups');
                    Route::get('/json/activity', 'MapsController@jsonActivity');
                    Route::get('/json/groups', 'MapsController@jsonGroups');
                    Route::get('/json/voters', 'MapsController@jsonVoters');
                });

                Route::group(['prefix' => '{app_type}/files'], function () {
                    Route::get('/{id}/download', 'FilesController@download');
                    Route::get('/{id}/searchpeople/{v?}', 'FilesController@searchPeople');
                    Route::get('/', 'FilesController@index');

                    Route::get('/list/cases/{return?}', 'FilesController@indexCases');
                    Route::get('/list/directories/{return?}', 'FilesController@indexDirectories');
                    Route::get('/list/groups/{return?}', 'FilesController@indexGroups');
                    Route::get('/list/constituents/{return?}', 'FilesController@indexConstituents');
                    Route::get('/list/all/{return?}', 'FilesController@indexAll');

                    Route::get('/search/{scope?}/{v?}/{return?}', 'FilesController@search');
                    Route::post('/upload-image', 'FilesController@uploadImage');
                    Route::post('/upload/{options?}', 'FilesController@upload');

                    Route::get('/{id}/edit/{return?}', 'FilesController@edit');
                    Route::post('/{id}/update/{c?}/{return?}', 'FilesController@update');
                    Route::get('/{id}/delete/{return?}', 'FilesController@delete');
                    Route::get('/{id}/unlink_case/{case}/{return?}', 'FilesController@unlinkCase');
                    Route::get('/{id}/unlink_group/{group}/{return?}', 'FilesController@unlinkGroup');
                    Route::get('/{id}/unlink_person/{person}/{return?}', 'FilesController@unlinkPerson');

                    Route::post('/directories/add/', 'DirectoriesController@add');
                    Route::get('/directories/{dir}/move/{ids?}', 'DirectoriesController@moveInto');
                    Route::get('/directories/{dir}/edit', 'DirectoriesController@edit');
                    Route::post('/directories/{d}/update/{c?}', 'DirectoriesController@update');
                    Route::get('/directories/{dir}/delete', 'DirectoriesController@delete');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/jurisdictions'], function () {
                    Route::get('/',                         'JurisdictionsController@index');
                    Route::get('/{state}/{type}/{code}',    'JurisdictionsController@show');
                });

                /////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '{app_type}/thoughts'], function () {
                    Route::view('/', 'shared-features.comments.index');
                });

            });
