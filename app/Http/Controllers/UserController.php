<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Tweet;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function addFriend(Request $request)
    {
        $user1 = auth()->user();
        $user2 = User::Where('id',$request->id)->first();

        $friend1 = new Friend();
        $friend1->user_id = $user1->id;
        $friend1->friend_id = $user2->id;

        if($user2->isPrivate)
            $friend1->isRequested = true;
        else
            $friend1->isRequested = false;

        $user1->friend()->save($friend1);

        return $this->getUser($user2->username);
    }

    public function deleteFriend(Request $request)
    {
        $user1 = auth()->user();
        $user2 = $user2 = User::Where('id',$request->id)->first();

        $friend1 = Friend::Where('user_id',$user1->id)->Where('friend_id',$user2->id)->first();

        $user1->friend()->delete($friend1);

        return $this->getUser($user2->username);
    }

    public function getUser($username) {
        $me = auth()->user();
        $user = User::Where('username',$username)->first();
        $friend = Friend::Where('user_id',$me->id)->Where('friend_id',$user->id)->Where('isRequested',false)->first();
        $friendsBool = null;
        $isRequested = false;

        if($user->isPrivate == false || $friend != null)
        {
            if($friend != null)
                $friendsBool = true;
            else
                $friendsBool = false;

            $tweets = Tweet::Where('user_id',$user->id)->orderBy('updated_at','desc')->get();
            // Liked tweets
            $following = Friend::Where('user_id',$user->id)->Where('isRequested',false)->get();
            $followers = Friend::Where('friend_id',$user->id)->Where('isRequested',false)->get();
            return $this->sendUser($me,$user,$tweets,$following,$followers,$friendsBool,$isRequested);
        }
        else
        {
            if(Friend::Where('user_id',$me->id)->Where('friend_id',$user->id)->Where('isRequested',true)->first() != null) {
                $isRequested = true;
                $friendsBool = false;
            }
            else {
                $isRequested = false;
                $friendsBool = false;
            }

            $tweets = null;
            // Liked Tweets
            $following = Friend::Where('user_id',$user->id)->Where('isRequested',false)->get();
            $followers = Friend::Where('friend_id',$user->id)->Where('isRequested',false)->get();

            return $this->sendUser($me,$user,$tweets,$following,$followers,$friendsBool,$isRequested);
        }
    }

    public function getUserWithID($id) {
        $yourInfo = auth()->user();

        $user = User::Where('id',$id)->first();

        return $user;
    }

    protected function sendUser($me,$user,$tweets,$following,$followers,$friendsBool,$isRequested)
    {
        return response()->json([
            'me' => $me,
            'user' => $user,
            'tweets' => $tweets,
            'following' => $following,
            'followers' => $followers,
            'friendsBool' => $friendsBool,
            'isRequested' => $isRequested
        ]);
    }
}
