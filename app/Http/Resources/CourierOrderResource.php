<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property Order $resource
 * @package App\Http\Resources
 */
class CourierOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->resource->id,
            'order_no'      => (int)$this->resource->order_no,
            'shop_id'       => $this->resource->shop_id,
            'customer_name' => $this->resource->customer_name,
            'phone'         => $this->resource->phone,
            'address'       => $this->resource->address,
            'order_status'  => $this->resource->order_status,
            'cod'           => $this->resource->cod === 1,
            'grand_total'   => $this->resource->pricing->grand_total,
            'advanced'      => $this->resource->pricing->advanced,
            'due'           => (int)$this->resource->pricing->due,
            'shipping_cost' => $this->resource->pricing->shipping_cost,
        ];
    }
}
