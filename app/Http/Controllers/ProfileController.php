<?php

namespace App\Http\Controllers;

use App\Friend;
use Illuminate\Http\Request;
use App\Tweet;

class ProfileController extends Controller
{
    public function getProfile() {
        $user = auth()->user();
        $tweets = Tweet::Where('user_id',$user->id)->orderBy('updated_at','desc')->get();
        // Liked tweets
        $following = Friend::Where('user_id',$user->id)->Where('isRequested',false)->get();
        $followers = Friend::Where('friend_id',$user->id)->Where('isRequested',false)->get();
        return $this->sendProfile($user,$tweets,$following,$followers);
    }

    protected function sendProfile($user,$tweets,$following,$followers)
    {
        return response()->json([
            'user' => $user,
            'tweets' => $tweets,
            'following' => $following,
            'followers' => $followers
        ]);
    }

    public function getSettingsData() {
        $user = auth()->user();

        return response()->json([
            'name' => $user->name,
            'bio' => $user->bio,
            'isPrivate' => $user->isPrivate
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

        $user->isPrivate = $request->isPrivate;

        $user->save();

        return $user;
    }

}
