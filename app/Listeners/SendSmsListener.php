<?php

namespace App\Listeners;

use App\Services\Sms;

class SendSmsListener
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
     * @param object $event
     * @return void
     */
    public function handle(object $event)
    {
        if (($event->shop->sms_balance) > 0.30) {
            $event->shop->sms_balance -= 0.30;
            ++$event->shop->sms_sent;
            $event->shop->save();

            $sms = new Sms();
            $sms->orderConfirmSms($event->order->phone, $event->order, $event->shop);
        }
    }
}
