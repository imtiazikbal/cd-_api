<?php

namespace App\Http\Controllers\API\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingSettingStoreRequest;
use App\Models\ShippingSetting;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingSettingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $shippingSetting = ShippingSetting::query()->where('shop_id', $request->header('shop-id'))->first();

        if(empty($shippingSetting)) {
            return $this->sendApiResponse('', 'Shipping settings not found');
        }

        return $this->sendApiResponse($shippingSetting, 'Shipping settings show');
    }

    public function storeUpdate(ShippingSettingStoreRequest $request): JsonResponse
    {
        $exitShippingSetting = ShippingSetting::query()->where('shop_id', $request->header('shop-id'))->first();

        if(!$exitShippingSetting) {
            $shippingSetting = ShippingSetting::create($request->validated());
            $msg = 'Shipping settings store successfully';
        } else {
            $shippingSetting = $exitShippingSetting->update($request->only('inside', 'outside', 'subarea'));
            $msg = 'Shipping settings updated successfully';
        }

        return $this->sendApiResponse($shippingSetting, $msg);
    }

    public function statusUpdate(Request $request): JsonResponse
    {
        $shippingSetting = ShippingSetting::query()->where('shop_id', $request->header('shop-id'))->first();
        if (!$shippingSetting) {
            $shippingSetting = new ShippingSetting();
            $shippingSetting->shop_id = $request->header('shop-id');
        }
        match ($shippingSetting->status) {
            0       => $shippingSetting->status = $request->input('status'),
            1       => $shippingSetting->status = $request->input('status'),
            default => $shippingSetting->status
        };
        $shippingSetting->save();

        return $this->sendApiResponse($shippingSetting, 'Shipping settings status updated');
    }

    // Order permission status update
    public function updateOrderPermissionStatus(Request $request): JsonResponse
    {
        $shopId = $request->header('shop-id');

        $orderPermission = Shop::query()
        ->select('id', 'order_perm_status', 'shop_id')
        ->where('shop_id', $shopId)
        ->first();

        if($orderPermission) {
            $newStatus = match($orderPermission->order_perm_status) {
                0 => 1,
                1 => 0,
            };

            $orderPermission->order_perm_status = $newStatus;
            $orderPermission->update();

            return $this->sendApiResponse($orderPermission, 'Order permission status updated');
        }

        return $this->sendApiResponse('', 'Shop not found !');
    }

    // order permission status view
    public function orderPermissionIndex(Request $request): JsonResponse
    {
        $orderPermission = DB::table('shops')
        ->select('id', 'shop_id', 'order_perm_status')
        ->where('shop_id', $request->header('shop-id'))
        ->first();

        return $this->sendApiResponse($orderPermission, 'Order permission status');
    }

    // Order OTP permission status update
    public function updateOrderOTPPermissionStatus(Request $request): JsonResponse
    {
        $shopId = $request->header('shop-id');

        $orderOTPPermission = Shop::query()
        ->select('id', 'order_otp_perm', 'shop_id')
        ->where('shop_id', $shopId)
        ->first();

        if($orderOTPPermission) {
            $newStatus = match($orderOTPPermission->order_otp_perm) {
                0 => 1,
                1 => 0,
            };

            $orderOTPPermission->order_otp_perm = $newStatus;
            $orderOTPPermission->update();

            return $this->sendApiResponse($orderOTPPermission, 'Order OTP permission status updated');
        }

        return $this->sendApiResponse('', 'Shop not found !');
    }
}