<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Traits\sendApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;

    use DispatchesJobs;

    use ValidatesRequests;

    use sendApiResponse;

    public function __construct(Request $request)
    {
        if ($request->header('shop-id') && $request->header('shop-id') !== null) {
            $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

            if(!$shop) {
                abort(404, 'Invalid shop id');
            }

            return $request;
        }

    }

    protected function limit($default = 10): int
    {
        return (int) request()->input('limit', $default);
    }

    // Order no create based on shop no
    // protected function createOrderNoBasedOnShopNo(string $shopId): String
    // {
    //     $uniqueOrderNumber = 0;
    //     $orderCheck = Order::query()->where('shop_id', $shopId)->select('id', 'order_no')->orderByDesc('id')->first();

    //     if($orderCheck){
    //         $previousOrderNo = substr($orderCheck->order_no, 0, 6);
    //         if($shopId == $previousOrderNo){
    //             $getOrderNoAfterShopNo = intval(substr($orderCheck->order_no, 6)) + 1;
    //             $mergeShopNoOrderNo = $shopId . $getOrderNoAfterShopNo;
    //             $uniqueOrderNumber = $mergeShopNoOrderNo;
    //         }else{
    //             $uniqueOrderNumber = $shopId. 1;
    //         }
    //     }else{
    //         $uniqueOrderNumber = $shopId. 1;
    //     }

    //     return strval($uniqueOrderNumber);
    // }
    protected function createOrderNoBasedOnShopNo(string $shopId): string
    {
        $lastorder = Order::where('shop_id', $shopId)->orderByDesc('id')->first();
        $uniqueorderseq = 1;

        if ($lastorder) {
            $lastorderno = $lastorder->order_no;
            $lastordershopid = substr($lastorderno, 0, strlen($shopId));
            $lastorderseq = intval(substr($lastorderno, strlen($shopId)));

            if ($lastordershopid === $shopId) {
                $uniqueorderseq = $lastorderseq + 1;
            }
        }
        while (Order::where('order_no', $shopId . str_pad($uniqueorderseq, 4, '0', STR_PAD_LEFT))->exists()) {
            $uniqueorderseq++;
        }

        $uniqueOrderNumber = $shopId . str_pad($uniqueorderseq, 4, '0', STR_PAD_LEFT);

        return $uniqueOrderNumber;
    }
}
