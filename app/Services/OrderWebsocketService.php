<?php

namespace App\Services;

use App\Events\OrderWebsocketEvent;
use App\Helpers\OrderHelper;
use App\Models\Fraud;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderWebsocketService {
    
    public function orderCreateSocket($order)
    {
        $frauds_report = Fraud::query()
            ->select(
                DB::raw('SUM(frauds.orders) as fraud_entry'),
                DB::raw('SUM(frauds.delivered) as fraud_delivery'),
                DB::raw('SUM(frauds.cancelled) as fraud_return'),
                DB::raw('COUNT(fraud_notes.id) as fraud_report'),
            )
            ->leftJoin('fraud_notes', 'frauds.id', '=', 'fraud_notes.fraud_id')
            ->where('number', $order->phone)->first();

        if ($frauds_report == null || $frauds_report->fraud_entry == null) {
            $frauds_report = (object) [
                'fraud_entry' => 0,
                'fraud_delivery' => 0,
                'fraud_return' => 0,
                'fraud_report' => 0,
                'fraud_processing' => true,
            ];
        } else {
            $frauds_report->fraud_processing = false;
        }
        
        $transformData =  [
            "id"                    => $order->id,
            "order_no"              => (int) $order->order_no,
            "order_tracking_code"   => $order->order_tracking_code,
            "order_type"            => $order->order_type,
            "shop_id"               => $order->shop_id,
            "customer_name"         => $order->customer_name,
            "phone"                 => $order->phone,
            "address"               => $order->address,
            "order_status"          => $order->order_status,
            "cod"                   => $order->cod === 1,
            "grand_total"           => $order->pricing->grand_total + $order->pricing->shipping_cost,
            "discounted_total"      => discountedTotal($order->pricing),
            "discount"              => $order->pricing->discount,
            "discount_type"         => $order->pricing->discount_type,
            "advanced"              => (int) $order->pricing->advanced,
            "due"                   => (int) $order->pricing->due,
            "shipping_cost"         => $order->pricing->shipping_cost,
            "delivery_location"     => Str::ucfirst(Str::replace('_', ' ', $order->delivery_location)),
            "courier_note"          => OrderHelper::getNote($order->id, 'courier'),
            "invoice_note"          => OrderHelper::getNote($order->id, 'invoice'),
            "order_note"            => OrderHelper::getNote($order->id, 'order'),
            "courier_entry"         => $order->config->courier_entry === true,
            "tracking_code"         => $order->courier->tracking_code,
            "consignment_id"        => $order->courier->consignment_id,
            "courier_status"        => Courier::status($order->courier->status) ?? $order->courier->status,
            "courier_provider"      => $order->courier->provider,
            $order->order_status . '_date' => OrderHelper::getOrderDate($order->id, $order->order_status),
            "created_at"            => $order->created_at,
            "updated_at"            => $order->created_at,
            'fraud_info'            => $frauds_report
        ];

        $finalResponse= array_merge($transformData, $this->orderDetailsDataTransform($order?->order_details));
        OrderWebsocketEvent::dispatch($finalResponse);
    }

    private function orderDetailsDataTransform($order_details) {
        $newOrderDetailsArr = [];
        foreach($order_details as $index => $detail){
            $newOrderDetailsArr[$index] = [
                "id"            => $detail->id,
                "product_id"    => optional($detail->product)->id,
                "product"       => optional($detail->product)->product_name,
                "product_code"  => optional($detail->product)->product_code,
                "discount"      => optional($detail->product)->discount,
                "discount_type" => optional($detail->product)->discount_type,
                "media"         => optional($detail->product)->main_image?->name,
                "price"         => OrderHelper::getProductDiscountCalculation($detail->product_id), // Assuming you have this function
                "quantity"      => $detail->product_qty,
                "shipping_cost" => $detail->shipping_cost,
                "variant"       => optional($detail->product)->variant,
                "variations"    => optional($detail->product)->variation,
            ];
        }
        $keyAddForOrderDetails = ['order_details' => $newOrderDetailsArr];

        return $keyAddForOrderDetails;
    }
}