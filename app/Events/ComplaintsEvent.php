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

class ComplaintsEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data;
    public $roleName;
    public $mobileNotif;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data, $roleName, $mobileNotif)
    {
        $this->data = $data;
        $this->roleName = $roleName;
        $this->mobileNotif = $mobileNotif;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
      return new Channel('complaint-channel');
    }

    public function broadcastAs() {
      return 'ComplaintEvent';
    }

    public function broadcastWith()
    {
      return [
        'data' => $this->data,    
        'roleName' => $this->roleName,
        'mobileNotif' => $this->mobileNotif
      ];
    }
}
