<?php

namespace App\Events\Order;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\ItemRequestState;

class StateOrderItemExceedLimitEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item_request_state;

    public function __construct(ItemRequestState $item_request_state)
    {
        $this->item_request_state = $item_request_state;
    }
}
