<?php

namespace App\Listeners;

use App\Events\SmsBalanceEvent;
use App\Models\Shop;
use App\Notifications\SmsBalanceNotification;
use Illuminate\Support\Facades\Notification;

class SmsBalanceEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SmsBalanceEvent  $event
     * @return void
     */
    public function handle(SmsBalanceEvent $event)
    {
        $shop = Shop::find($event->shopId);
        $balance = $shop->sms_balance;

        if ($balance < 101) {
            Notification::send($shop, new SmsBalanceNotification($shop));
        }
    }
}
