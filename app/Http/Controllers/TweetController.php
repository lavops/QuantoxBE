<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\TweetRequest;
use App\Like;
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
        $tweets = Tweet::join('users','tweets.user_id','=','users.id')->select(
            'tweets.*',
            'users.username',
            'users.name')->get();
        return $tweets;
    }

    public function getComments($id)
    {
        $user = auth()->user();

        $comments = Comment::join('users','comments.user_id','=','users.id')->select(
            'comments.*',
            'users.username',
            'users.name'
        )->Where('tweet_id', $id)->get();

        return $comments;
    }

    public function postComment(CommentRequest $request)
    {
        $user = auth()->user();

        $tweet = Tweet::Where('id',$request->tweet_id)->first();

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->tweet_id = $request->tweet_id;
        $comment->text = $request->text;

        $tweet->comment()->save($comment);

        return $comment;
    }

    public function likeTweet(Request $request) {
        $user = auth()->user();
        //dd($request);
        $tweet = Tweet::Where('id',$request->id)->first();

        $like = new Like();
        $like->tweet_id = $request->tweet_id;
        $like->user_id = $user->id;

        $tweet->like()->save($like);

        return $this->getLikes($tweet->id);
    }

    public function unlikeTweet(Request $request) {
        $user = auth()->user();

        $tweet = Tweet::Where('id',$request->id)->first();

        $like = Like::Where('tweet_id',$tweet->id)->first();

        $tweet->like()->delete($like);

        return $this->getLikes($tweet->id);
    }

    public function getLikes($id) {
        $user = auth()->user();

        $isLiked = false;

        $likes = Like::join('users','likes.user_id','=','users.id')->select(
            'likes.*',
            'users.username',
            'users.name'
        )->Where('tweet_id', $id)->get();
        //dd($likes->where('username',$user.username)->first());
        if($likes->where('username',$user->username)->first() != null)
            $isLiked = true;
        return response()->json([
            'likes' => $likes,
            'isLiked' =>$isLiked
        ]);
    }



    // Delete function

    // Update function

    // Get Single Tweet

}
