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
            $tweets = $this->probaTweetProfile($user);
            return $tweets;
        }
        else
            return $this->getTweets();
    }

    public function getTweets()
    {
        $user = auth()->user();

        $tweets = $this->probaTweetTimeline($user);

        return $tweets;
    }

    public function deleteTweet(Request $request) {
        $user = auth()->user();
        $tweet = Tweet::where('id',$request->id)->first();
        $tweet->delete();

        if($request->url == 'profile') {
            $tweets = $this->probaTweetProfile($user);
            return $tweets;
        }
        else
            return $this->getTweets();
    }

    public function probaTweetProfile($me) {

        $tweets = Tweet::Join('users','tweets.user_id','=','users.id')->
        Where('tweets.user_id',$me->id)->OrderBy('tweets.created_at','desc')->
        select(
            'tweets.*',
            'users.username',
            'users.name',
            'users.imgURL'
        )->get();

        foreach($tweets as $tweet) {

            $countLikes = Like::Where('tweet_id',$tweet->id)->count();
            $countComments = Comment::Where('tweet_id',$tweet->id)->count();

            if(Like::Where('tweet_id',$tweet->id)->Where('user_id',$me->id)->first() != null)
                $isLiked = true;
            else
                $isLiked = false;

            $comments = Comment::Join('users','comments.user_id','=','users.id')->Where('tweet_id',$tweet->id)->
            select(
                'comments.*',
                'users.username',
                'users.name',
                'users.imgURL'
            )->get();

            $tweet->comments = $comments;
            $tweet->countLikes = $countLikes;
            $tweet->countComments = $countComments;
            $tweet->isLiked = $isLiked;
        }

        return $tweets;
    }

    public function probaTweetTimeline($me) {

        $tweets = Tweet::join('users','tweets.user_id','=','users.id')->
            leftJoin('friends','users.id','=','friends.friend_id')->
            Where('friends.user_id',$me->id)->
            Where('friends.isRequested',false)->
            orWhere('users.id',$me->id)->select(
                'tweets.*',
                'users.username',
                'users.name',
                'users.imgURL'
            )->orderBy('updated_at','desc')->distinct('tweets.id')->get();

        foreach($tweets as $tweet) {

            $countLikes = Like::Where('tweet_id',$tweet->id)->count();
            $countComments = Comment::Where('tweet_id',$tweet->id)->count();

            if(Like::Where('tweet_id',$tweet->id)->Where('user_id',$me->id)->first() != null)
                $isLiked = true;
            else
                $isLiked = false;

            $comments = Comment::Join('users','comments.user_id','=','users.id')->Where('tweet_id',$tweet->id)->
            select(
                'comments.*',
                'users.username',
                'users.name',
                'users.imgURL'
            )->get();

            $tweet->comments = $comments;
            $tweet->countLikes = $countLikes;
            $tweet->countComments = $countComments;
            $tweet->isLiked = $isLiked;
        }

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

}
