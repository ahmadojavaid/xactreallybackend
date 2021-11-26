<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace'=>'Api'], function () {
	Route::post('sign-up', 'ApiController@signUp');
	Route::post('sign-in', 'ApiController@signIn');	

	Route::post('check/phone', 'ApiController@checkPhone');
	Route::post('change/password', 'ApiController@changePassword');

	Route::post('choose/role', 'ApiController@chooseRole');

	Route::get('privacy-policy', 'ApiController@privacyPolicy');
	Route::get('terms-conditions', 'ApiController@termsConditions');

	Route::get('jobs', 'ApiController@jobs');
	Route::get('job/{id}', 'ApiController@job');

	Route::post('create/job', 'ApiController@createJob');
	Route::post('update/job/{id}', 'ApiController@updateJob');
	Route::delete('delete/job/{id}', 'ApiController@deleteJob');

	Route::post('add/elevation', 'ApiController@addElevation');
	Route::post('update/elevation/{id}', 'ApiController@updateElevation');
	Route::delete('delete/elevation/{id}', 'ApiController@deleteElevation');
	Route::get('elevation/{id}', 'ApiController@getElevationbyId');

	Route::post('add/room', 'ApiController@addRoom');
	Route::post('update/room/{id}', 'ApiController@updateRoom');
	Route::delete('delete/room/{id}', 'ApiController@deleteRoom');
	Route::get('room/{id}', 'ApiController@getRoombyId');

	Route::get('get-access' , 'ApiController@getAccess');
	Route::post('add-job-access' , 'ApiController@addJobAccess');
	Route::get('accept-job-invitation/{job_access_id}' , 'ApiController@acceptJobInvitation');
	Route::get('cancel-job-invitation/{job_access_id}' , 'ApiController@cancelJobInvitation');
	Route::get('delete-job-invitation/{job_access_id}' , 'ApiController@deleteJobInvitation');

	Route::post('add-job-quote' , 'ApiController@addJobQuote');
	Route::post('update-job-quote/{id}' , 'ApiController@updateJobQuote');
	Route::get('get-job-quote-lines/{job_quote_id}' , 'ApiController@getJobQuoteLines');
	Route::post('add-job-quote-line' , 'ApiController@addJobQuoteLine');
	Route::get('get-job-quote-by-id/{job_quote_id}' , 'ApiController@getJobQuoteById');
	Route::get('get-job-quote-line-by-id/{job_quote_line_id}' , 'ApiController@getJobQuoteLineById');
	Route::delete('delete-job-quote-line-by-id/{job_quote_line_id}' , 'ApiController@deleteJobQuoteLineById');

	Route::group(['prefix'=>'user'], function () {
		Route::post('/update-profile', 'UserController@updateProfile');
		Route::get('/get-profile', 'UserController@getProfile');
		Route::get('/get-user-jobs', 'UserController@getUserJobs');
		Route::get('/search-users', 'UserController@searchUsers');
		Route::get('/get-user-job-quotes', 'UserController@getUserJobQuotes');
		Route::get('/get-user-sent-job-invitations', 'UserController@getUserSentJobInvitations');
		Route::get('/get-user-received-job-invitations', 'UserController@getUserReceivedJobInvitations');

	});
});