<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\TweetRequest;
use Illuminate\Http\Request;
use App\User;
use App\Tweet;

class TweetController extends Controller
{
    public function postTweet(TweetRequest $request)
    {
        $user = auth()->user();
        $tweet = new Tweet();
        $tweet->user_id = $user->id;
        $tweet->text = $request->text;

        $user->tweet()->save($tweet);

        return $tweet;
    }

    public function getTweets()
    {
        $user = auth()->user();
        $tweets = Tweet::where('user_id', $user->id)->orderBy('updated_at', 'desc')->get();
        return $tweets;
    }

    public function getComments($id)
    {
        $user = auth()->user();

        $comments = Comment::Where('tweet_id', $id)->get();

        return $comments;
    }

    public function postComment(CommentRequest $request)
    {
        $user = auth()->user();

        $tweet = Tweet::Where('tweet_id',$request->tweet_id)->first();

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->tweet_id = $request->tweet_id;
        $comment->text = $request->text;

        $tweet->comment()->save($comment);

        return $comment;
    }

    // Delete function

    // Update function

    // Get Single Tweet

}
