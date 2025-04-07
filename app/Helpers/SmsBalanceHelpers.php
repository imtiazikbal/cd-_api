<?php

use App\Models\Shop;
use App\Notifications\SmsBalanceNotification;
use Illuminate\Support\Facades\Notification;

function smsBalanceAlert($shopId)
{
    $shop = Shop::find($shopId);

    if (!$shop) {
        return response()->json([
            'message' => 'Shop not found'
        ]);
    }

    $balance = $shop->sms_balance;

    if ($balance < 101) {
        Notification::send($shop, new SmsBalanceNotification($shop));
    }
}


function smsCostCalculation($request, $msg)
{
    $numbers = explode(',', $request->input('phone'));
    $numbersCount = collect($numbers)->count();
    $englishLanCheck = preg_match('/^[a-zA-Z0-9\s.,]*$/', $msg);

    if($englishLanCheck) {
        $smsLength = strlen($msg);
        $sms_count = ceil($smsLength / 160);
    } else {
        $smsLength = mb_strlen($msg);
        $sms_count = ceil($smsLength / 70);
    }

    $sms_cost = $sms_count * config('funnelliner.sms_charge');
    $totalSmsCost = $numbersCount * $sms_cost;

    return [
        'totalSmsCost' => $totalSmsCost,
        'numbers'      => $numbers,
        'sms_count'    => $sms_count,
        'sms_cost'     => $sms_cost,
        'smsLength'    => $smsLength,
    ];
}
