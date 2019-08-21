<?php

namespace App\Events;

use App\Tweet;
use App\User;
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

    public $likerUsername;
    public $likerName;
    public $ownerName;
    public $ownerUsername;

    public function __construct($like)
    {
        $liker = User::Where('id',$like->user_id)->first();
        $this->likerUsername = $liker->username;
        $this->likerName = $liker->name;

        $likedTweet = Tweet::Join('users','tweets.user_id','=','users.id')->Where('tweets.id',$like->tweet_id)->first();
        $this->ownerName = $likedTweet->name;
        $this->ownerUsername = $likedTweet->username;
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
