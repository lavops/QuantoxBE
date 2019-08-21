<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FollowEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $followerUsername;
    public $followerName;
    public $followedUsername;
    public $followedName;
    public $isRequested;

    public function __construct($friend)
    {
        $follower = User::Where('id',$friend->user_id)->first();
        $followed = User::Where('id',$friend->friend_id)->first();

        $this->followerName = $follower->name;
        $this->followerUsername = $follower->username;

        $this->followedName = $followed->name;
        $this->followedUsername = $followed->username;

        $this->isRequested = $friend->isRequested;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['my-channel'];
    }

    public function broadcastAs()
    {
        return 'followed';
    }
}
