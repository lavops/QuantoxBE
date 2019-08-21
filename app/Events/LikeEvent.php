<?php

namespace App\Events;

use App\Like;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LikeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $name;
    public $username;
    public $notify;

    public function __construct($id,$name,$username)
    {
        $this->name = $name;
        $this->username = $username;
        $notify = Like::join('tweets','likes.tweet_id','=','tweets.id')->
        join('users','likes.user_id','=','users.id')->
        where('likes.notify',true)->where('tweets.user_id',$id)->
        where('likes.user_id','!=',$id)->select(
            'likes.*',
            'users.username',
            'users.name'
        )->get();
    }

    public function broadcastOn()
    {
        return ['my-channel'];
    }

    public function broadcastAs()
    {
        return 'liked';
    }
}
