<?php

namespace App\Http\Controllers\API\V1\Client\Stock\ProductReturn;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class ProductReturnController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = 10;

            if (request()->query('perPage')) {
                $perPage = request()->query('perPage');
            }

            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Merchant not Found',
                ], 404);
            }

            $orders = Order::with('order_details')->where('shop_id', $merchant->shop->id)
                ->where('order_status', 'returned')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data'    => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function update(OrderRequest $request)
    {
        try {

            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Merchant not Found',
                ], 404);
            }

            $order = Order::with('order_details')->where('id', $request->order_id)->where('shop_id', $merchant->shop->id)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Order not Found!',
                ], 404);
            }

            if ($order->order_status != 'returned') {
                return response()->json([
                    'success' => false,
                    'msg'     => 'return order not Found!',
                ], 404);
            }

            if ($order->order_status == 'returned') {
                $order->return_order_note = $request->return_order_note;
            }
            $order->save();

            return response()->json([
                'success' => true,
                'msg'     => 'Return product order note Update Successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }
}
