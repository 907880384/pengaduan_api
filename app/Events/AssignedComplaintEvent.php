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
    public $data;
    public $receiveData;
    public $mobileNotif;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data, $receiveData, $mobileNotif)
    {
        $this->data = $data;
        $this->receiveData = $receiveData;
        $this->mobileNotif = $mobileNotif;
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
        'data' => $this->data,    
        'receiveData' => $this->receiveData,
        'mobileNotif' => $this->mobileNotif,
      ];
    }
}
