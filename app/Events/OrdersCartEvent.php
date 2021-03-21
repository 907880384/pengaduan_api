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

class OrdersCartEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $receivers;
    public $type;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data,  $receivers, $type)
    {
        $this->data = $data;
        $this->receivers = $receivers;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        switch ($this->type) {
            case 'AGREE_ORDER':
                return new Channel('agree-order-channel');
                
            case 'DISAGREE_ORDER':
                return new Channel('disagree-order-channel');
            default:
                return new Channel('add-order-channel'); 
        }
    }

    public function broadcastAs() {
        return 'CartOrderEvent';
    }

    public function broadcastWith() {
        return [
            'data' => $this->data,
            'receivers' => $this->receivers,
            'type' => $this->type
        ];
    }
}
