<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FraudResource extends JsonResource
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
            'id' => $this->resource->id,
            'number' => $this->resource->number,
            'courier' => $this->resource->courier,
            'orders' => $this->resource->orders,
            'delivered' => $this->resource->delivered,
            'cancelled' => $this->resource->cancelled,
            'fraud_report' => $this->resource->fraud_report,
        ];
    }
}
