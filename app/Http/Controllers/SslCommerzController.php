<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\SSLCommerz;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use PDF;

class SslCommerzController extends MerchantBaseController
{
    protected $dateFormat;

    public function __construct()
    {
        $this->dateFormat = 'd M Y';
    }

    public function callback(Request $request, $status): RedirectResponse
    {
        if ($status !== 'success') {
            return Redirect::away(env('FRONTEND_URL') . 'subscription');
        }

        $data = SSLCommerz::validate($request->input('val_id'));


        // $subscription = Subscription::query()->create([
        //  'user_id' => $request->input('user_id'),
        // 'invoice_num' => $invoice_num,
        // 'amount' => $data->amount,
        // 'gateway' => 'ssl',
        // 'sub_gateway' => $data->card_type,
        // 'gateway_trxid' => $data->tran_id,
        // 'api_response' => json_encode($data->toArray()),
        // 'next_due_date' => Carbon::now()->addDays(30)
        // ]);
        $subscriptions = Subscription::where('user_id', $request->user_id)->latest()->first();

        if ($subscriptions) {
            $expire_date = Carbon::parse($subscriptions->next_due_date)->format($this->dateFormat);
        } else {
            $expire_date = Carbon::now();
        }
        $subscription = Subscription::where(['user_id' => $request->user_id, 'id' => $request->id])->update([
            'amount'        => $data->amount,
            'gateway'       => 'ssl',
            'sub_gateway'   => $data->card_type,
            'gateway_trxid' => $data->tran_id,
            'api_response'  => json_encode($data->toArray()),
            'next_due_date' => Carbon::now()->addDays(30),
            'status'        => ($data->status == 'VALID') ? 'Paid' : "Unpaid"
        ]);

        if ($data->status === 'VALID') {
            $subscription->next_due_date = $expire_date->addDays(30);
            $subscription->save();

            $user = User::query()->find($request->input('user_id'));
            $user->payment_status = User::PAID;
            $user->status = User::STATUS_ACTIVE;
            $user->save();
        }
        // start
        $expire_date = Carbon::parse($subscription->next_due_date)->format($this->dateFormat);
        $start_date = Carbon::parse($subscription->created_at)->format($this->dateFormat);
        // mail message
        $email = $subscription->user->email;
        $messageData = [
            'name'        => $subscription->user->name,
            'email'       => $email,
            'start_date'  => $start_date,
            'expire_date' => $expire_date,
            'invoice_num' => $subscription->invoice_num,
            'amount'      => $subscription->amount
        ];

        if ($subscription->gateway == 'ssl') {
            $payment_type = 'SslCommerz';
        } elseif ($subscription->gateway == 'bKash') {
            $payment_type = 'bKash';
        } elseif ($subscription->gateway == 'nagad') {
            $payment_type = 'Nagad';
        }

        // payment image defined for pdf
        $payment_status_check = $subscription->status;

        if ($payment_status_check == 'success') {
            $payment_status = 'https://web.funnelliner.com/upload/paid.png';
        } else {
            $payment_status = 'https://web.funnelliner.com/upload/unpaid.png';
        }
        $pdf = PDF::loadView('panel.pdf.subscription_pdf', compact('subscription', 'create_date', 'expire_date', 'today_date', 'payment_type', 'payment_status'));

        // send email
        Mail::send('panel.email.subscription', $messageData, function ($message) use ($email, $pdf) {
            $message->to($email)->subject('Subscription')
                ->attchData($pdf->output(), 'subscription.pdf');
        });
        // end

        // if ($subscription) {
        //     $user = User::query()->find($request->input('user_id'));
        //     $user->payment_status = User::PAID;
        //     $user->status = User::STATUS_ACTIVE;
        //     $user->save();
        // }

        return Redirect::away(env('FRONTEND_URL') . 'subscription?trxid=' . $data->tran_id);
    }
}
