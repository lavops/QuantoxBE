<?php

namespace App\Repository;

use App\Comment;
use App\Like;
use App\Tweet;
use Carbon\Carbon;

class Tweets{

    const CACHE_KEY = 'TWEETS';

    public function all($user) {
        $key = "timeline.{$user->username}";
        $cacheKey = $this->getCacheKey($key);

        return cache()->remember($cacheKey,Carbon::now()->addSeconds(30),function() use($user){
            return $this->probaTweetTimeline($user);
        });
    }

    public function get() {

    }

    public function getCacheKey($key) {
        $key = strtoupper($key);

        return self::CACHE_KEY .".$key";
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


}
