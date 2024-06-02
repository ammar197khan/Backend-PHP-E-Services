<?php

namespace App\Events\Order;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StateOrderItemEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order_tech_request_id;

    public function __construct($order_tech_request_id)
    {
        $this->order_tech_request_id = $order_tech_request_id;
    }
}
