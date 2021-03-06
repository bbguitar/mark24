<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	if (!Auth::guest()) {
        // updates the user timestamp to check if the user is online/active
        $user = User::find(Auth::user()->id);
        $user->online_timestamp = time();
        $user->save();
    }
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('/');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

Route::filter('logged-in', function() {
    if (Auth::check()) return Redirect::to('home');
});

Route::filter('super-admin', function()
{
    if (Auth::guest()) {
        return Redirect::to('/');
    } else if(!Auth::guest() && Auth::user()->flag != 0) {
        return View::make('templates.fourohfour');
    }
});

Route::filter('are-you-a-teacher', function() {
    if (Auth::guest()) {
        return Redirect::to('/');
    } else if(!Auth::guest() && Auth::user()->account_type != 1) {
        return View::make('templates.fourohfour');
    }
});

Route::filter('are-you-a-student', function() {
    if (Auth::guest()) {
        return Redirect::to('/');
    } else if(!Auth::guest() && Auth::user()->account_type != 2) {
        return View::make('templates.fourohfour');
    }
});

Route::filter('you-are-super-admin', function() {
   if (Auth::guest()) {
        return Redirect::to('/');
    } else if(!Auth::guest() && Auth::user()->flag == 0) {
        return View::make('templates.fourohfour');
    }
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
