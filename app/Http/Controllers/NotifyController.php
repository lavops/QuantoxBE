<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Like;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    public function getFollow() {
        $user = auth()->user();

        $notify = Friend::join('users','friends.user_id','=','users.id')->select(
            'friends.*',
            'users.username',
            'users.name'
        )->Where('friend_id',$user->id)->Where('notify',true)->get();

        return $notify;
    }

    public function getLike() {
        $user = auth()->user();

        $notify = null;

        return $notify;
    }

    public function acceptFollow(Request $request) {
        $user = auth()->user();

        $friend = Friend::Where('id',$request->id)->first();
        $friend->isRequested = false;
        $friend->notify = false;
        $friend->update();

        return $this->getFollow();
    }

    public function declineFollow(Request $request) {
        $user = auth()->user();

        $friend = Friend::Where('id',$request->id)->first();
        $friend->delete();

        return $this->getFollow();
    }

    public function dismissFollow(Request $request) {
        $user = auth()->user();

        $friend = Friend::Where('id',$request->id)->first();
        $friend->notify = false;
        $friend->update();

        return $this->getFollow();
    }

    public function dismissLike(Request $request) {
        $user = auth()->user();

        $like = Like::Where('id',$request->id)->first();
        $like->notify = false;
        $like->update();

        return $this->getLike();
    }
}