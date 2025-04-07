<?php

namespace App\Jobs;

use App\Notifications\OrderNotification;
use DateTime;
use DateTimeZone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class OrderNotificationJob implements ShouldQueue
{
    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    use SerializesModels;

    private $request;

    private $order;

    private $shop;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $order, $shop)
    {
        $this->request = $request;
        $this->order = $order;
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dateOrder = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
        $orderTime = $dateOrder->format("M d Y h:i A");
        $notifyInfo = [
            'text'       => "Order ID: " . $this->order->id . " has been Placed ",
            'shop_id'    => $this->request->header('shop-id'),
            'order_time' => $orderTime,
            'type'       => 'order',
        ];

        Notification::send($this->shop, new OrderNotification($notifyInfo));
    }
}
