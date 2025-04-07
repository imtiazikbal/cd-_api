<?php

namespace App\Http\Resources;

use App\Http\Resources\FraudResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FraudReportResource extends JsonResource
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
            'order_placed' => (int)$this->resource->order_placed,
            'order_delivered' => (int)$this->resource->order_delivered,
            'order_returned' => (int)$this->resource->order_returned,
            'fraud_report' => (int)$this->resource->fraud_report,
            'fraud_processing' => (bool)$this->resource->fraud_processing,
            'frauds' => FraudResource::collection($this->resource->frauds),
        ];
    }
}
