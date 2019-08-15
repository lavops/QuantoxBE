<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Tweet;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // delete user

    // create vec postoji u AuthController-u

    // addFriend

    public function addFriend(Request $request)
    {
        $user1 = auth()->user();
        $user2 = User::Where('id',$request->id)->first();

        $friend1 = new Friend();
        $friend1->user_id = $user1->id;
        $friend1->friend_id = $user2->id;

        $friend2 = new Friend();
        $friend2->user_id = $user2->id;
        $friend2->friend_id = $user1->id;

        $user1->friend()->save($friend1);
        $user2->friend()->save($friend2);

        return $this->getUser($user2->username);
    }

    public function deleteFriend(Request $request)
    {
        $user1 = auth()->user();
        $user2 = $user2 = User::Where('id',$request->id)->first();

        $friend1 = Friend::Where('user_id',$user1->id)->Where('friend_id',$user2->id)->first();
        $friend2 = Friend::Where('user_id',$user2->id)->Where('friend_id',$user1->id)->first();

        $user1->friend()->delete($friend1);
        $user2->friend()->delete($friend2);

        return $this->getUser($user2->username);
    }

    // get single user ( ovo se koristi za gledanje tudjih profila )
    public function getUser($username) {
        $yourInfo = auth()->user();

        $userInfo = User::Where('username',$username)->first();

        $friend = Friend::Where('user_id',$yourInfo->id)->Where('friend_id',$userInfo->id)->first();
        $friendsBool = null;
        if($userInfo->isPrivate == false || $friend != null)
        {
            if($friend != null)
                $friendsBool = true;
            else
                $friendsBool = false;

            $tweets = Tweet::Where('user_id',$userInfo->id)->orderBy('updated_at','desc')->get();
            return $this->sendFullUser($yourInfo,$userInfo,$tweets,$friendsBool);
        }

        return $this->sendUser($yourInfo,$userInfo );
    }
    //

    public function getUserWithID($id) {
        $yourInfo = auth()->user();

        $user = User::Where('id',$id)->first();

        return $user;
    }

    protected function sendUser($yourInfo,$userInfo)
    {
        return response()->json([
            'you' => $yourInfo,
            'user' => $userInfo
        ]);
    }

    protected function sendFullUser($yourInfo,$userInfo,$tweets,$friendsBool)
    {
        return response()->json([
            'you' => $yourInfo,
            'user' => $userInfo,
            'tweets' => $tweets,
            'friendsBool' => $friendsBool
        ]);
    }
}
