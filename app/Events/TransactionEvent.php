<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionEvent
{
    use Dispatchable;

    use InteractsWithSockets;

    use SerializesModels;

    public int $user_id;

    public $payment;

    public string $status;

    public string $order_type;

    public string $payment_method;

    public string $amount;

    public string $trxId;

    public int|null $addons_id;

    /**
     * Create a new event instance.
     *
     * @param $user_id
     * @param $data
     * @param $status
     * @param $order_type
     * @param $payment_method
     * @param $addons_id
     */
    public function __construct($user_id, $payment, $status, $order_type, $payment_method, $amount, $trxId, $addons_id = null)
    {
        $this->user_id = $user_id;
        $this->payment = $payment;
        $this->status = $status;
        $this->order_type = $order_type;
        $this->payment_method = $payment_method;
        $this->amount = $amount;
        $this->trxId = $trxId;
        $this->addons_id = $addons_id;
    }
}
