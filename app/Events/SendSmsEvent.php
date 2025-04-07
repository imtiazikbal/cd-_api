<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendSmsEvent
{
    use Dispatchable;

    use InteractsWithSockets;

    use SerializesModels;

    public $shop;

    public $order;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     * @param Shop $shop
     */
    public function __construct(Order $order, Shop $shop)
    {
        $this->order = $order;
        $this->shop = $shop;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
