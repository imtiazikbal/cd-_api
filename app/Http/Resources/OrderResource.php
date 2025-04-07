<?php

namespace App\Http\Resources;

use App\Models\Fraud;
use App\Models\Order;
use App\Models\OrderNote;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonSerializable;

/**
 * @property Order $resource
 */

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $status = $this->order_status ?? Order::PENDING;
        $note = OrderNote::query()->where('order_id', $this->resource->id)->where('type', $status)->first();
        $phone = $this->resource->phone;
        $frauds_report = Fraud::query()
            ->select(
                DB::raw('SUM(frauds.orders) as fraud_entry'),
                DB::raw('SUM(frauds.delivered) as fraud_delivery'),
                DB::raw('SUM(frauds.cancelled) as fraud_return'),
                DB::raw('COUNT(fraud_notes.id) as fraud_report'),
            )
            ->leftJoin('fraud_notes', 'frauds.id', '=', 'fraud_notes.fraud_id')
            ->where('number', $phone)->first();

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

        return [
            'id'                => $this->resource->id,
            'order_no'          => (int)$this->resource->order_no,
            'shop_id'           => $this->resource->shop_id,
            'customer_name'     => $this->resource->customer_name,
            'phone'             => $phone,
            'address'           => $this->resource->address,
            'order_status'      => $this->resource->order_status,
            'cod'               => $this->resource->cod === 1,
            'grand_total'       => $this->resource->pricing->grand_total,
            'advanced'          => $this->resource->pricing->advanced,
            'due'               => $this->resource->pricing->due,
            'shipping_cost'     => $this->resource->pricing->shipping_cost,
            'delivery_location' => Str::ucfirst(Str::replace('_', ' ', $this->resource->delivery_location)),
            'note'              => $note->note ?? null,
            'order_details'     => OrderDetailsResource::collection($this->resource->order_details),
            'otp_sent'          => $this->resource->otp_sent,
            'created_at'        => $this->resource->created_at,
            'fraud_info'        => $frauds_report
        ];
    }
}