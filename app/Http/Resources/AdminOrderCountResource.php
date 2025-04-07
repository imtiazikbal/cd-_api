<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderCountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $shopId = $this->resource->shop['shop_id'];
        $orderCount = Order::query()
        ->where('shop_id', $shopId)
        ->whereBetween('created_at', [$request->startDate, $request->endDate])
        ->withTrashed()
        ->count();

        return [
            "id"             => $this->resource->id,
            "name"           => $this->resource->name,
            "address"        => $this->resource->address,
            "email"          => $this->resource->email,
            "phone"          => $this->resource->phone,
            "role"           => $this->resource->role,
            "about_us"       => $this->resource->about_us,
            "created_at"     => $this->resource->created_at,
            "updated_at"     => $this->resource->updated_at,
            "status"         => $this->resource->status,
            "payment_status" => $this->resource->payment_status,
            "next_due_date"  => $this->resource->next_due_date,
            "order_count"    => $orderCount,
            "shop"           => new AdminShopResource($this->resource->shop),
        ];
    }
}
