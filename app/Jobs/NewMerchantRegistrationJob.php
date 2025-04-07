<?php

namespace App\Jobs;

use App\Events\TransactionEvent;
use App\Models\User;
use App\Notifications\AccountConfirmNotification;
use App\Notifications\PasswordUpdateNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewMerchantRegistrationJob implements ShouldQueue
{
    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    use SerializesModels;

    public $payment;

    public $additional_info;

    public $method;

    /**
     * Create a new job instance.
     *
     * @param array $payment
     * @param $additional_info
     * @param string $method
     */
    public function __construct($additional_info, array $payment, string $method)
    {
        $this->payment = $payment;
        $this->additional_info = $additional_info;
        $this->method = $method;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $user = User::query()->create([
            'name'              => $this->additional_info['name'],
            'phone'             => $this->additional_info['phone'],
            'email'             => $this->additional_info['email'],
            'role'              => User::MERCHANT,
            'password'          => '123456789',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'next_due_date'     => Carbon::today()->addDays(30),
            'payment_status'    => User::PAID,
        ]);

        $status = '';

        if (array_key_exists('transactionStatus', $this->payment) && $this->payment['transactionStatus'] === 'Completed') {
            $status = User::PAID;
        }

        if (array_key_exists('status', $this->payment) && $this->payment['status'] === 'Success') {
            $status = User::PAID;
        }

        if($this->method == 'Bkash') {
            $trxId = $this->payment['trxID'];
        } else {
            $trxId = $this->payment['issuerPaymentRefNo'];
        }

        TransactionEvent::dispatch($user->id, $this->payment, $status, $this->additional_info['order_type'], $this->method, $this->payment['amount'], $trxId);

        $user->notify(new AccountConfirmNotification($user));
        $user->notify(new PasswordUpdateNotification($user));
    }
}
