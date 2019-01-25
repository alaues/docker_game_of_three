<?php

namespace App\Events;

use App\Move;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MoveSubmittedNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $move;

    /**
     * Create a new event instance.
     *
     * @param Move $move
     */
    public function __construct(Move $move)
    {
        $move->player_name = $move->player->player_name;
        $this->move = $move;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('game');
    }

    public function broadcastAs()
    {
        return 'MoveSubmittedNotification';
    }
}
