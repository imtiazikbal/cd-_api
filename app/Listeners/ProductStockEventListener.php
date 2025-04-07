<?php

namespace App\Listeners;

use App\Events\ProductStockEvent;
use App\Models\Product;
use App\Notifications\ProductStockNotification;
use Illuminate\Support\Facades\Notification;

class ProductStockEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductStockEvent  $event
     * @return void
     */
    public function handle(ProductStockEvent $event)
    {
        $product = Product::find($event->productId);

        $stock = $product->product_qty;

        if ($stock < 10) {
            Notification::send($product, new ProductStockNotification($product));
        } elseif ($stock < 5) {
            Notification::send($product, new ProductStockNotification($product));
        } elseif ($stock == 0) {
            Notification::send($product, new ProductStockNotification($product));
        }
    }
}
