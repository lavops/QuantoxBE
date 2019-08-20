<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Like;
use Illuminate\Http\Request;
use App\Tweet;
use App\Comment;

class ProfileController extends Controller
{
    public function getProfile() {
        $user = auth()->user();

        $tweets = $this->probaTweetProfile($user);
        $liked = Like::join('tweets','likes.tweet_id','=','tweets.id')->Where('likes.user_id',$user->id)->
        join('users','tweets.user_id','=','users.id')->select(
            'tweets.*',
            'users.username',
            'users.name',
            'users.imgURL'
        )->get();
        $following = Friend::Where('user_id',$user->id)->join('users','friends.friend_id','=','users.id')->Where('isRequested',false)->get();
        $followers = Friend::Where('friend_id',$user->id)->join('users','friends.user_id','=','users.id')->Where('isRequested',false)->get();
        return $this->sendProfile($user,$tweets,$following,$followers,$liked);
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

    protected function sendProfile($user,$tweets,$following,$followers,$liked)
    {
        return response()->json([
            'user' => $user,
            'tweets' => $tweets,
            'following' => $following,
            'followers' => $followers,
            'liked' => $liked
        ]);
    }

    public function getSettingsData() {
        $user = auth()->user();

        return response()->json([
            'name' => $user->name,
            'bio' => $user->bio,
            'isPrivate' => $user->isPrivate,
            'imgURL' => $user->imgURL
        ]);
    }

    public function updateProfile(Request $request) {
        $user = auth()->user();

        if ( $request->name != null)
            $user->name = $request->name;

        if ( $request->bio != null)
            $user->bio = $request->bio;

        if ($request->password != null && $request->password_confirmation && $request->password == $request->password_confirmation)
            $user->password = bcrypt($request->password);

        $user->imgURL = $request->image;
        $user->isPrivate = $request->isPrivate;

        $user->save();

        return $user;
    }

}
