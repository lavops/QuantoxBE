<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Like;
use App\Tweet;
use Illuminate\Http\Request;

class ProbaController extends Controller
{
    public function probaTweetProfile() {
        $me = auth()->user();

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

    public function probaTweetUser() {
        $me = auth()->user();
    }
}
