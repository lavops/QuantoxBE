<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Like;
use Illuminate\Http\Request;
use App\Tweet;

class ProfileController extends Controller
{
    public function getProfile() {
        $user = auth()->user();
        $tweets = Tweet::where('user_id', $user->id)->join('users','tweets.user_id','=','users.id')->select(
            'tweets.*',
            'users.username',
            'users.name',
            'users.imgURL'
        )->orderBy('tweets.updated_at', 'desc')->get();
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
