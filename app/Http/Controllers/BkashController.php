<?php

namespace App\Http\Controllers;

use App\Events\TransactionEvent;
use App\Jobs\NewMerchantRegistrationJob;
use App\Models\MyAddons;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Bkash;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

/**
 * @property Bkash $bkash
 */
class BkashController extends Controller
{
    public function __construct(Bkash $bkash)
    {
        $this->bkash = $bkash;
    }

    public function callback(Request $request): RedirectResponse
    {
        $location = $this->getLocation($request->data['order_type']);

        if ($request->status !== 'cancel') {
            $response = $this->bkash->executePayment($request->paymentID, $request->data['authToken']);
            $data = json_decode($response);

            if ($data->statusCode === '2023') {
                return Redirect::away(config('services.frontend_url.production') . $location . '?status=failed&statusMessage=' . $request->statusMessage);
            }

            match ($request->data['order_type']) {
                'sms'     => $this->handleSmsOrder($data, $request->data),
                'plugin'  => $this->handlePluginOrder($data, $request->data),
                'package' => $this->handlePackageOrder($request->data, $data),
                default   => false,
            };

            if (array_key_exists('name', $request->data)) {

                if ($request->status === 'success') {
                    $phoneHash = Crypt::encryptString($request->data['phone']);

                    return Redirect::away('https://app.funnelliner.com/shop-info?hash=' . $phoneHash);
                }

                if ($request->status === 'failure') {
                    return Redirect::away('https://app.funnelliner.com/payment-fail');
                }
            }
        }

        if (array_key_exists('name', $request->data)) {
            return Redirect::away('https://app.funnelliner.com/payment-fail');
        }
 
        return Redirect::away(config('services.frontend_url.production') . $location . '?status=' . $request->status ?? $request->statusMessage . '&trxid=' . $request->paymentID);
    }

    private function getLocation(string $orderType): string
    {
        return match ($orderType) {
            'plugin' => 'plug-in',
            'sms'    => 'bulk-sms',
            default  => 'billing',
        };
    }

    private function handleSmsOrder($payment, $requestData)
    {
        $payment = collect($payment)->toArray();

        if ($payment['statusCode'] === '0000' && array_key_exists('user_id', $requestData)) {

            TransactionEvent::dispatch(
                $requestData['user_id'],
                $payment,
                'paid',
                'sms',
                'Bkash',
                $payment['amount'],
                $payment['trxID'],
            );

            $shop = Shop::query()->where('user_id', intval($requestData['user_id']))->first();
            $shop->sms_balance += intval($payment['amount']);
            $shop->save();
        }

        return false;
    }

    private function handlePluginOrder($payment, $requestData)
    {
        $payment = collect($payment)->toArray();

        if ($payment['statusCode'] === '0000') {
            $shop = Shop::query()->where('user_id', $requestData['user_id'])->first();
            MyAddons::query()->create([
                'shop_id'   => $shop->shop_id,
                'addons_id' => $requestData['addons_id'],
                'status'    => false
            ]);
        }

        if ($payment['statusCode'] === '0000' && array_key_exists('user_id', $requestData)) {

            TransactionEvent::dispatch(
                $requestData['user_id'],
                $payment,
                'paid',
                'addons',
                'Bkash',
                $payment['amount'],
                $payment['trxID'],
                $requestData['addons_id']
            );
        }

        return false;
    }

    private function handlePackageOrder($requestData, $payment): bool
    {
        $payment = collect($payment)->toArray();

        if (($payment['statusCode'] === '0000') && array_key_exists('name', $requestData)) {
            NewMerchantRegistrationJob::dispatch($requestData, $payment, 'Bkash');
        }

        if (($payment['statusCode'] === '0000') && array_key_exists('user_id', $requestData) && array_key_exists('invoice_num', $requestData)) {

            $transaction = Transaction::query()->where('invoice_num', $requestData['invoice_num'])->first();
            $transaction->update([
                'trxid'       => $payment['trxID'],
                'response'    => json_encode($payment),
                'status'      => 'paid',
                'gateway'     => 'Bkash',
                'sub_gateway' => 'Bkash Payment',
            ]);

            $user = User::find($transaction->user_id);
            $user->status = 'active';
            $user->payment_status = 'paid';
            $user->next_due_date = Carbon::parse($transaction->due_date)->addDays(30);
            $user->update();
        }

        return false;
    }
}