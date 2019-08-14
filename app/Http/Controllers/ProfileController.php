<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tweet;

class ProfileController extends Controller
{
    public function getProfile() {
        $user = auth()->user();
        $tweets = Tweet::Where('user_id',$user->id)->orderBy('updated_at','desc')->get();
        return $this->sendProfileData($user,$tweets);
    }

    protected function sendProfileData($user,$tweets)
    {
        return response()->json([
            'user' => $user,
            'tweets' => $tweets
        ]);
    }

    public function getSettingsData() {
        $user = auth()->user();

        return response()->json([
            'name' => $user->name,
            'bio' => $user->bio
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

        $user->save();

        return $user;
    }

}
