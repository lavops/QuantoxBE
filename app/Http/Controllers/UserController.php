<?php

namespace App\Http\Controllers;

use App\Tweet;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // delete user

    // create vec postoji u AuthController-u

    // get single user ( ovo se koristi za gledanje tudjih profila )
    public function getUser($username) {
        $yourInfo = auth()->user();

        $userInfo = User::Where('username',$username)->first();
        if($userInfo->isPrivate == false)
        {
            $tweets = Tweet::Where('user_id',$userInfo->id)->orderBy('updated_at','desc')->get();
            return $this->sendFullUser($yourInfo,$userInfo,$tweets);
        }

        return $this->sendUser($yourInfo,$userInfo);
    }
    //

    protected function sendUser($yourInfo,$userInfo)
    {
        return response()->json([
            'you' => $yourInfo,
            'user' => $userInfo
        ]);
    }

    protected function sendFullUser($yourInfo,$userInfo,$tweets)
    {
        return response()->json([
            'you' => $yourInfo,
            'user' => $userInfo,
            'tweets' => $tweets
        ]);
    }
}
