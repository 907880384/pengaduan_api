<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AssignedComplaintEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $assigned;
    public $receiveAssigned;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($assigned, $receiveAssigned)
    {
        $this->assigned = $assigned;
        $this->receiveAssigned = $receiveAssigned;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('assign-complaint');
    }

    public function broadcastWith()
    {
      return [
        'assigned' => $this->assigned,    
        'receiveAssigned' => $this->receiveAssigned,
      ];
    }
}
