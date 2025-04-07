<?php

namespace App\Listeners;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionListener
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
        if($event->order_type == 'package') {
            $due_date = Carbon::today()->addDays(30);
        }
        Transaction::query()->create([
            'user_id'     => $event->user_id,
            'invoice_num' => Str::random(8),
            'addons_id'   => $event->addons_id,
            'trxid'       => $event->trxId,
            'type'        => $event->order_type,
            'amount'      => $event->amount,
            'response'    => json_encode($event->payment),
            'status'      => $event->status,
            'gateway'     => $event->payment_method,
            'sub_gateway' => $event->payment_method . ' Payment',
            'due_date'    => $due_date ?? null,
        ]);
    }
}
