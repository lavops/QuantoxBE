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
    route::post('likeTweet','TweetController@likeTweet');
    route::post('unlikeTweet','TweetController@unlikeTweet');
    route::get('getLikes/{id}','TweetController@getLikes');
    route::post('deleteTweet','TweetController@deleteTweet');

    Route::get('tweet/{id}', 'CommentController@getComments');
    Route::post('postComment', 'CommentController@postComment');
    Route::post('deleteComment','CommentController@deleteComment');

    Route::get('user/{username}','UserController@getUser');
    Route::post('addFriend','UserController@addFriend');
    Route::post('deleteFriend','UserController@deleteFriend');
    Route::get('userID/{id}','UserController@getUserWithID');
    Route::get('search/{text}','UserController@search');
    Route::get('search/{text?}','UserController@searchNothing');
    Route::post('blockFriend','UserController@blockFriend');
    Route::post('unblockFriend','UserController@unblockFriend');

    Route::get('getFollowNotify','NotifyController@getFollow');
    Route::get('getLikeNotify','NotifyController@getLike');
    Route::post('dismissLike','NotifyController@dismissLike');
    Route::post('dismissFollow','NotifyController@dismissFollow');
    Route::post('acceptFollow','NotifyController@acceptFollow');
    Route::post('declineFollow','NotifyController@declineFollow');

});
