<?php

Route::get('workers', function(){
	echo 'Hello from the workers package!';
});

Route::get('add/{a}/{b}', 'FluencySoftware/Workers/WorkersController@add');
Route::get('subtract/{a}/{b}', 'FluencySoftware/Workers/WorkersController@subtract');