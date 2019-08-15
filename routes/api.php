<?php

Route::group([

    'middleware' => 'api',

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('signup','AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

    Route::get('profile','ProfileController@getProfile');
    Route::post('profile','ProfileController@setProfile');
    Route::get('settingsData','ProfileController@getSettingsData');
    Route::post('updateProfile','ProfileController@updateProfile');

    Route::post('postTweet','TweetController@postTweet');
    Route::get('getTweets','TweetController@getTweets');

    Route::get('user/{username}','UserController@getUser');
    Route::post('addFriend','UserController@addFriend');
    Route::post('deleteFriend','UserController@deleteFriend');

});
