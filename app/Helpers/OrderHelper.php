<?php

namespace App\Helpers;

use App\Models\Media;
use App\Models\Order;
use App\Models\OrderDate;
use App\Models\OrderNote;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Model;



class OrderHelper {

public static function getNote($orderId, $type): ?string
{
    $note = OrderNote::query()->where('order_id', $orderId)->where('type', $type)->first();

    if ($note) {
        return $note->note;
    }

    return null;
}

public static function getOrderDate($orderId, $type): ?string
{
    $date = OrderDate::query()->where('order_id', $orderId)->where('type', $type)->first();

    if ($date) {
        return $date->date;
    }

    return null;
}

public static function getProductDiscountCalculation($id)
{
    $product = Product::query()->find($id);

    if ($product->discount_type === Product::PERCENT) {
        $discounted_amount = ceil($product->price * $product->discount / 100);

        return $product->price - $discounted_amount;
    }

    if ($product->discount_type === Product::FLAT) {
        return $product->price - $product->discount;
    }

    return $product->price;
}

public static function handleProductQtyCheck($request)
{
    $extraErrorMessages = [];
    collect($request->input('product_id'))->each(function($item, $key) use ($request, &$extraErrorMessages){
        $product = Product::query()->find($item);
        
        if($request->variant_id[$key] != 0) {
            $variant = ProductVariation::query()->find($request->variant_id[$key]);
            if($variant->quantity < $request->input('product_qty')[$key]) {
                $extraErrorMessages['errorMessages'][] = [
                    'variant' => 'This ' . $variant->variant . ' variant has not enough quantity'
                ];
            }
        } else {
            if($product->product_qty < $request->input('product_qty')[$key]){
                $extraErrorMessages['errorMessages'][] = [
                    'product' => 'This ' . $product->product_name . ' has not enough quantity'
                ];
            }
        }
        
    });

    return $extraErrorMessages;
}
    
public static function handleOrderUpdateProcess($request, $order)
{
    $shipping_cost = 0;
    if ($request->input('product_id') !== null) {

        foreach ($request->input('product_id') as $key => $item) {
            $product = Product::query()->find($item);

            if($request->variant_id[$key] != 0) {
                $variant = ProductVariation::query()->find($request->variant_id[$key]);
                $discounted_price = $variant->price;
                $variantId = $variant->id;
            } else {
                $discounted_price = self::getProductDiscountCalculation($product->id);
                $variantId = null;
            }

            if($order->shop->order_perm_status == 1){
                self::createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId);
            }else {
                if($request->variant_id[$key] != 0) {
                    if($variant->quantity >= $request->input('product_qty')[$key]) {
                        self::createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId);
                    }
                } else {
                    if($product->product_qty >= $request->input('product_qty')[$key]){
                        self::createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId);
                    }
                }
            }
            $shipping_cost += $request->input('shipping_cost')[$key];
        }
    }
    
    $order->pricing->update([
        'shipping_cost' => $shipping_cost,
    ]);
}

public static function createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId)
{
    $order->order_details()->create(
        [   
            'product_id'    => $item,
            'product_qty'   => $request->input('product_qty')[$key],
            'unit_price'    => $discounted_price,
            'shipping_cost' => $request->input('shipping_cost')[$key],
            'variant'       => $variantId
        ]
    );
}

public static function handleOrderAttachImageUpdate(Model $order, $request)
{
    if($order->shop->order_attach_img_perm){
        if($request->hasFile('order_attach_img')) {
            foreach ($order->order_attach_images as $image) {
                if ($image !== null) {
                    s3ImageDelete(Order::ORDERATTACHIMAGEPATH, $image->name, $request->header('id'));
                    Media::query()->where('parent_id', $order->id)
                        ->where(function ($q) {
                            $q->where('type', 'order_attach_img');
                        })
                        ->delete();
                }
            }

            $orderAttachImgs = $request->file('order_attach_img');
            foreach ($orderAttachImgs as $image) {
                $filePath = 'media/order-attach-img/' . $request->header('id');
                Media::upload($order, $image, $filePath, 'order_attach_img');
            }
        }
    }
}

}