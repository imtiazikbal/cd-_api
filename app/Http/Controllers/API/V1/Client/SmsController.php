<?php

namespace App\Http\Controllers\API\V1\Client;

use App\Http\Controllers\Controller;

use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\StatusConfigRequest;
use App\Jobs\SendSmsJob;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Shop;

class SmsController extends Controller
{
    public function getCustomers(Request $request): JsonResponse
    {
        $list = Order::query()->where('shop_id', $request->header('shop-id'))
            ->select('customer_name', 'phone', 'order_status')
            ->get();

        if (!$list) {
            return $this->sendApiResponse('', 'Customers not found', 'NotFound');
        }

        return $this->sendApiResponse($list);
    }

    public function sendSms(SendSmsRequest $request): JsonResponse
    {
        $msg = $request->input('msg');
        $sendSmsInfo = smsCostCalculation($request, $msg);

        $shop = Shop::query()
        ->where('shop_id', $request->header('shop-id'))
        ->first();

        if($sendSmsInfo['totalSmsCost'] > $shop->sms_balance) {
            return $this->sendApiResponse('', 'Insufficient Balance');
        }

        foreach ($sendSmsInfo['numbers'] as $number) {
            $shop->sms_balance -= $sendSmsInfo['sms_cost'];
            $shop->sms_sent += $sendSmsInfo['sms_count'];
            $shop->save();
        }

        dispatch(new SendSmsJob($request->input('phone'), $msg));

        return $this->sendApiResponse('', 'SMS has been sent Successfully');
    }

    public function showOrderSms(): JsonResponse
    {
        $shop = Shop::query()->where('shop_id', request()->header('shop-id'))->first();
        $decode_shop = json_decode($shop->order_sms);

        return $this->sendApiResponse($decode_shop);
    }

    public function updateOrderSms(StatusConfigRequest $request): JsonResponse
    {
        $shop = Shop::query()
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        $decoded_order_sms = json_decode($shop->order_sms);
        $sms_type = $request->input('sms_status');

        $order_sms_Arr = [
            'cancelled' => $decoded_order_sms->cancelled,
            'confirmed' => $decoded_order_sms->confirmed,
            'shipped'   => $decoded_order_sms->shipped,
            'returned'  => $decoded_order_sms->returned,
            'delivered' => $decoded_order_sms->delivered,
            'pending'   => $decoded_order_sms->pending,
            'hold_on'   => $decoded_order_sms->hold_on,
        ];

        $order_sms_Arr[$sms_type] = $order_sms_Arr[$sms_type] === '1' ? '0' : '1';

        $shop->order_sms = json_encode($order_sms_Arr);
        $shop->save();
        $message = $order_sms_Arr[$sms_type] === 1 ? 'enabled' : 'disabled';

        return $this->sendApiResponse(json_decode($shop->order_sms), ucfirst($sms_type) . ' Order SMS status is currently ' . $message);
    }
}
