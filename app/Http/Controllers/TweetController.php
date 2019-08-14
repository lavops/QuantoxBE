<?php

namespace App\Http\Controllers;

use App\Http\Requests\TweetRequest;
use Illuminate\Http\Request;
use App\User;
use App\Tweet;

class TweetController extends Controller
{
    public function postTweet(TweetRequest $request) {
        $user = auth()->user();
        $tweet = new Tweet();
        $tweet->user_id = $user->id;
        $tweet->text = $request->text;
        $user->tweet()->save($tweet);

        return $tweet;
    }

    public function getTweets() {
        $user = auth()->user();
        $tweets = Tweet::where('user_id',$user->id)->orderBy('updated_at','desc')->get();
        return $tweets;
    }

    // Delete function

    // Update function

    // Get Single Tweet

}
