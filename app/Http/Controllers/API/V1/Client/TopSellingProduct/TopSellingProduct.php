<?php

namespace App\Http\Controllers\API\V1\Client\TopSellingProduct;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopSellingProduct extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $topsales = DB::table('orders')
            ->where('orders.shop_id', '=', $request->header('shop-id'))
            ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('products', function ($join) use ($request) {
                $join->on('products.id', '=', 'order_details.product_id')
                    ->where('products.shop_id', '=', $request->header('shop-id'))
                    ->whereNotNull('products.id'); // exclude products that don't exist in the products table
            })
            ->leftJoin('media', function ($join) {
                $join->on('products.id', '=', 'media.parent_id')->where('media.type', '=', 'product_main_image');
            })
            ->select(
                'products.id',
                'products.product_name',
                'products.product_qty',
                'order_details.product_id',
                DB::raw('SUM(order_details.product_qty) as total'),
                DB::raw("CONCAT('" . env('AWS_URL') . "/', media.name) AS product_main_image")
            )
            ->whereNotNull('products.id') // exclude products that don't exist in the products table
            ->where('products.shop_id', '=', $request->header('shop-id')) // exclude products that belong to a different shop
            ->groupBy('products.id', 'order_details.product_id', 'products.product_name', 'media.name', 'products.product_qty')
            ->orderBy('total', 'desc')
            ->limit(4)
            ->get();

        return $this->sendApiResponse($topsales);
    }

    public function customer_index(Request $request)
    {

        try {

            $shopID = $request->header('shop-id');
            $orderIds = [];
            $orders = Order::with('order_details')->where('order_status', 'Confirmed')->where('shop_id', $shopID)->get();

            if (!$orders) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Order not Found',
                ], 404);
            }

            foreach ($orders as $order) {
                $orderIds[] = $order->id;
            }


            $orderDetails = OrderDetails::select('product_id', 'product_qty')->whereIn('order_id', $orderIds)->get();

            if (!$orderDetails) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Order Details not found',
                ], 404);
            }

            $sumArray = [];

            foreach ($orderDetails as $order) {
                if (!isset($sumArray[$order->product_id])) {
                    $sumArray[$order->product_id] = 0;
                }

                if (isset($sumArray[$order->product_id])) {
                    $sumArray[$order->product_id] += $order->product_qty;
                }
            }

            $sellingProduct = [];

            foreach ($sumArray as $key => $qty) {
                $product = Product::with('main_image')->where('id', $key)->first();

                $other_images = Media::where('parent_id', $product->id)->where('type', 'product_other_image')->get();
                $product['other_images'] = $other_images;

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'msg'     => 'Product not Found',
                    ], 404);
                }

                // $sellingProduct[] = [
                //     'product' => $product,
                //     'total_sell' => $qty,
                //     'total_sell_amount' => ($product->price * $qty),
                //     'available_stock' => $product->product_qty,
                //     'added_on' => $product->created_at,
                // ];

                $sellingProduct[] = $product;
            }

            $topSellingProduct = $sellingProduct;

            return response()->json([
                'success' => true,
                'data'    => $topSellingProduct,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }
}
