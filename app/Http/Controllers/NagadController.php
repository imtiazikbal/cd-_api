<?php

namespace App\Http\Controllers;

use App\Events\TransactionEvent;
use App\Jobs\NewMerchantRegistrationJob;
use App\Models\MyAddons;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Nagad;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

/**
 * @property Nagad $nagad
 */
class NagadController extends Controller
{
    public function __construct(Nagad $nagad)
    {
        $this->nagad = $nagad;
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->status !== 'Aborted') {
            $data = $this->nagad->verifyPayment($request->payment_ref_id);

            $additional_info = json_decode($data->additionalMerchantInfo);
            $location = $this->getLocation($additional_info->order_type);
            $data = collect($data)->toArray();

            match ($additional_info->order_type) {
                'sms'     => $this->handleSmsOrder($data, $additional_info),
                'plugin'  => $this->handlePluginOrder($data, $additional_info),
                'package' => $this->handlePackageOrder($data, $additional_info),
                default   => false,
            };

            $extra_data = collect($additional_info)->toArray();

            if (array_key_exists('name', $extra_data)) {
                if ($data['status'] === 'Success') {
                    $phoneHash = Crypt::encryptString($additional_info->phone);

                    return Redirect::away('https://app.funnelliner.com/shop-info?hash=' . $phoneHash);
                }

                if ($data['status'] === 'Failed') {
                    return Redirect::away('https://app.funnelliner.com/payment-fail');
                }
            }

            return Redirect::away(config('services.frontend_url.production') . $location . '?status=' . $data['status'] . '&trxid=' . $data['orderId']);
        }

        return Redirect::away('https://app.funnelliner.com/payment-fail');
    }

    private function getLocation($orderType): string
    {
        return match ($orderType) {
            'plugin' => 'plug-in',
            'sms'    => 'bulk-sms',
            default  => 'billing',
        };
    }

    private function handleSmsOrder($payment, $additional_info)
    {

        $additional_info = collect($additional_info)->toArray();

        if ($payment['statusCode'] === '000') {
            $shop = Shop::query()->where('user_id', intval($additional_info['user_id']))->first();
            $shop->sms_balance += intval($payment['amount']);
            $shop->save();
        }

        if (($payment['statusCode'] === '000') && array_key_exists('user_id', $additional_info) && $additional_info['invoice_num'] == null) {

            TransactionEvent::dispatch(
                $additional_info['user_id'],
                $payment,
                'paid',
                'sms',
                'Nagad',
                $payment['amount'],
                $payment['issuerPaymentRefNo'],
            );
        }

        return false;
    }

    private function handlePluginOrder($payment, $additional_info)
    {

        if ($payment['statusCode'] === '000') {
            $shop = Shop::query()->where('user_id', $additional_info->user_id)->first();
            MyAddons::query()->create([
                'shop_id'   => $shop->shop_id,
                'addons_id' => $additional_info->addons_id,
                'status'    => false
            ]);

            TransactionEvent::dispatch(
                $additional_info->user_id,
                $payment,
                'paid',
                'addons',
                'Nagad',
                $payment['amount'],
                $payment['issuerPaymentRefNo'],
                $additional_info->addons_id
            );
        }

        return false;
    }

    private function handlePackageOrder($payment, $additional_info)
    {
        $additional_info = collect($additional_info)->toArray();

        if (($payment['statusCode'] === '000') && array_key_exists('name', $additional_info)) {
            NewMerchantRegistrationJob::dispatchSync($additional_info, $payment, 'Nagad');
        }

        if (($payment['statusCode'] === '000') && array_key_exists('user_id', $additional_info) && array_key_exists('invoice_num', $additional_info)) {

            $transaction = Transaction::query()
                ->where('invoice_num', $additional_info['invoice_num'])
                ->where('type', 'package')
                ->first();

            $transaction->update([
                'trxid'       => $payment['issuerPaymentRefNo'],
                'response'    => json_encode($payment),
                'status'      => 'paid',
                'gateway'     => 'Nagad',
                'sub_gateway' => 'Nagad Payment',
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
