<?php

namespace App\Http\Resources;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class AdminMerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if($this->resource->shop !== null) {
            $shopId = $this->resource->shop['shop_id'];
            $start_date = Carbon::parse($this->resource->next_due_date)->subDays(30)->toDateString();
            $end_date = $this->resource->next_due_date;

            $orderCount = Order::shopWiseOrderCount($shopId, $start_date, $end_date);
        }

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
            "order_count"    => $orderCount ?? 0,
            "shop"           => new AdminShopResource($this->resource->shop),
        ];
    }
}
