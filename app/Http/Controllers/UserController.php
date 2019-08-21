<?php

namespace App\Http\Controllers;

use App\Block;
use App\Comment;
use App\Friend;
use App\Like;
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

        $friend1->notify = true;

        $user1->friend()->save($friend1);

        return $this->getUser($user2->username);
    }

    public function deleteFriend(Request $request)
{
    $user1 = auth()->user();
    $user2 = User::Where('id',$request->id)->first();

    $friend1 = Friend::Where('user_id',$user1->id)->Where('friend_id',$user2->id)->first();

    $user1->friend()->delete($friend1);

    return $this->getUser($user2->username);
}

    public function getUser($username) {
        $me = auth()->user();
        $user = User::Where('username',$username)->first();
        $friend = Friend::Where('user_id',$me->id)->Where('friend_id',$user->id)->Where('isRequested',false)->first();

        $youBlockedMe = false;
        $meBlockedYou = false;
        $friendsBool = null;
        $isRequested = false;

        if(Block::Where('user_id',$me->id)->Where('friend_id',$user->id)->first() != null)
            $meBlockedYou = true;
        if(Block::Where('friend_id',$me->id)->Where('user_id',$user->id)->first())
            $youBlockedMe = true;

        if(($user->isPrivate == false || $friend != null) && !$youBlockedMe && !$meBlockedYou)
        {
            if($friend != null)
                $friendsBool = true;
            else
                $friendsBool = false;

            $tweets = $this->probaTweetUser($user);
            /*
             * Tweet::where('user_id', $user->id)->join('users','tweets.user_id','=','users.id')->select(
                'tweets.*',
                'users.username',
                'users.name',
                'users.imgURL'
            )->orderBy('tweets.updated_at', 'desc')->get();
             */
            // Liked tweets
            $following = Friend::Where('user_id',$user->id)->join('users','friends.friend_id','=','users.id')->Where('isRequested',false)->get();
            $followers = Friend::Where('friend_id',$user->id)->join('users','friends.user_id','=','users.id')->Where('isRequested',false)->get();
            return $this->sendUser($me,$user,$tweets,$following,$followers,$friendsBool,$isRequested,$meBlockedYou,$youBlockedMe);
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

            return $this->sendUser($me,$user,$tweets,$following,$followers,$friendsBool,$isRequested,$meBlockedYou,$youBlockedMe);
        }
    }

    public function probaTweetUser($me) {

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

    public function getUserWithID($id) {
        $yourInfo = auth()->user();

        $user = User::Where('id',$id)->first();

        return $user;
    }

    public function search($text) {
        $user = auth()->user();

        $data = User::Where('name','LIKE','%'.$text.'%')->orWhere('username','LIKE','%'.$text.'%')->get();

        return $data;
    }

    public function blockFriend(Request $request)
    {
        $user1 = auth()->user();
        $user2 = User::Where('id',$request->id)->first();

        $friend1 = Friend::Where('user_id', $user1->id)->where('friend_id',$user2->id)->first();
        if($friend1 != null) {
            $friend1->delete();
        }

        $friend2 = Friend::Where('user_id', $user2->id)->where('friend_id',$user1->id)->first();
        if($friend2 != null) {
            $friend2->delete();
        }

        $block = new Block();
        $block->user_id = $user1->id;
        $block->friend_id = $user2->id;

        $user1->block()->save($block);

        return $this->getUser($user2->username);
    }

    public function unblockFriend(Request $request)
    {
        $user1 = auth()->user();
        $user2 = $user2 = User::Where('id',$request->id)->first();

        $block = Friend::Where('user_id',$user1->id)->Where('friend_id',$user2->id)->first();

        $user1->block()->delete($block);

        return $this->getUser($user2->username);
    }

    public function searchNothing() {
        return [];
    }

    protected function sendUser($me,$user,$tweets,$following,$followers,$friendsBool,$isRequested,$meBlockedYou,$youBlockedMe)
    {
        return response()->json([
            'me' => $me,
            'user' => $user,
            'tweets' => $tweets,
            'following' => $following,
            'followers' => $followers,
            'friendsBool' => $friendsBool,
            'isRequested' => $isRequested,
            'meBlockedYou' => $meBlockedYou,
            'youBlockedMe' => $youBlockedMe
        ]);
    }
}
