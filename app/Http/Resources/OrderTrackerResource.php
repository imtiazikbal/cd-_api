<?php

namespace App\Http\Resources;

use App\Http\Resources\TrackingTimelineResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->resource->id,
            // Customer Info & COD Section
            'order_tracking_code' => $this->resource->tracking_code,
            'order_no'            => (int)$this->resource->order_no,
            'due'                 => (int)$this->resource->pricing->due,
            'customer_name'       => $this->resource->customer_name,
            'phone'               => User::removeCode($this->resource->phone),
            'address'             => $this->resource->address,

            // Product Info Section
            'product_names'       => $this->resource->order_details->pluck('product.product_name')->flatten()->toArray(),
            'quantity'            => $this->resource->order_details->sum('product_qty'),
            'grand_total'         => $this->resource->pricing->grand_total + $this->resource->pricing->shipping_cost,
            'advanced'            => $this->resource->pricing->advanced,
            'discount'            => $this->resource->pricing->discount,
            'discount_type'       => $this->resource->pricing->discount_type,
            'shipping_cost'       => (int)$this->resource->pricing->shipping_cost,
            'courier_provider'    => $this->resource->courier->provider,

            // Details - Seller Info Section
            'shop_name'            => $this->resource->shop->name,

            // Details - Rider Info Section
            'rider_name'            => $this->resource->rider_info->rider_name ?? null,
            'rider_number'       => $this->resource->rider_info->rider_number ?? null,

            // Tracking Details Section
            'tracker_timeline'       => TrackingTimelineResource::collection($this->resource->order_timeline),
        ];
    }
}
