<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\LikeEvent;
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
        if($request->url == 'profile') {
            $tweets = Tweet::where('user_id', $user->id)->join('users','tweets.user_id','=','users.id')->select(
                'tweets.*',
                'users.username',
                'users.name',
                'users.imgURL'
            )->orderBy('tweets.updated_at', 'desc')->get();
            return $tweets;
        }
        else
            return $this->getTweets();
    }

    public function deleteTweet(Request $request) {
        $user = auth()->user();
        $tweet = Tweet::where('id',$request->id)->first();
        $tweet->delete();

        if($request->url == 'profile') {
            $tweets = Tweet::where('user_id', $user->id)->join('users','tweets.user_id','=','users.id')->select(
                'tweets.*',
                'users.username',
                'users.name',
                'users.imgURL'
            )->orderBy('tweets.updated_at', 'desc')->get();
            return $tweets;
        }
        else
            return $this->getTweets();
    }

    public function getTweets()
    {
        $user = auth()->user();

        $tweets = Tweet::join('users','tweets.user_id','=','users.id')->
            leftJoin('friends','users.id','=','friends.friend_id')->
            Where('friends.user_id',$user->id)->
            Where('friends.isRequested',false)->
            orWhere('users.id',$user->id)->select(
            'tweets.*',
            'users.username',
            'users.name',
            'users.imgURL'
            )->orderBy('updated_at','desc')->distinct('tweets.id')->get();

        return $tweets;
    }

    public function likeTweet(Request $request) {

        $user = auth()->user();
        event(new LikeEvent('Lajk sam'));
        $tweet = Tweet::Where('id',$request->id)->first();

        $like = new Like();
        $like->tweet_id = $request->tweet_id;
        $like->user_id = $user->id;
        $like->notify = true;

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
