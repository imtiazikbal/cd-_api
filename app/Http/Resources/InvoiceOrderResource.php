<?php

namespace App\Http\Resources;

use App\Helpers\OrderHelper;
use App\Models\Order;
use App\Models\OrderDate;
use App\Services\Courier;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use JsonSerializable;

class InvoiceOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $status = $this->order_status ?? Order::PENDING;
        $date = OrderDate::query()->where('order_id', $this->resource->id)->where('type', $status)->first();

        if ($this->resource->pricing->discount_type === Order::PERCENT && $this->resource->pricing->discount_type > 0) {
            $discounted_total = ceil($this->resource->pricing->grand_total - ($this->resource->pricing->grand_total * ($this->resource->pricing->discount / 100)));
        } else {
            $discounted_total = $this->resource->pricing->grand_total - $this->resource->pricing->discount;
        }

        return [
            'id'                => $this->resource->id,
            'order_no'          => (int)$this->resource->order_no,
            'shop_id'           => $this->resource->shop_id,
            'customer_name'     => $this->resource->customer_name,
            'phone'             => $this->resource->phone,
            'address'           => $this->resource->address,
            'order_status'      => $this->resource->order_status,
            'cod'               => $this->resource->cod === 1,
            'grand_total'       => $this->resource->pricing->grand_total,
            'discounted_total'  => $discounted_total,
            'discount'          => $this->resource->pricing->discount,
            'discount_type'     => $this->resource->pricing->discount_type,
            'advanced'          => $this->resource->pricing->advanced,
            'due'               => (int)$this->resource->pricing->due,
            'shipping_cost'     => $this->resource->pricing->shipping_cost,
            'delivery_location' => Str::ucfirst(Str::replace('_', ' ', $this->resource->delivery_location)),
            'invoice_note'      => OrderHelper::getNote($this->resource->id, 'invoice'),
            'courier_entry'     => $this->resource->config->courier_entry == true,
            'tracking_code'     => $this->resource->courier->tracking_code,
            'courier_status'    => Courier::status($this->resource->courier->status),
            $status . '_date'   => $date ? $date->date : null,
            'created_at'        => $this->resource->created_at,
            'updated_at'        => $this->resource->updated_at,
            'order_details'     => OrderDetailsResource::collection($this->resource->order_details),
            'shop_info'         => new ShopInfoResource($this->resource->shop)
        ];
    }
}