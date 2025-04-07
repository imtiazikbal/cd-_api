<?php

namespace App\Http\Resources;

use App\Helpers\OrderHelper;
use App\Models\Fraud;
use App\Models\Order;
use App\Models\User;
use App\Services\Courier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @property Order $resource
 */

class MerchantOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $phone = User::removeCode($this->resource->phone);
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
            'id'                                    => $this->resource->id,
            'order_no'                              => (int)$this->resource->order_no,
            'order_tracking_code'                   => $this->resource->tracking_code,
            'order_type'                            => $this->resource->order_type,
            'shop_id'                               => $this->resource->shop_id,
            'customer_name'                         => $this->resource->customer_name,
            'phone'                                 => User::removeCode($this->resource->phone),
            'address'                               => $this->resource->address,
            'order_status'                          => $this->resource->order_status,
            'cod'                                   => $this->resource->cod === 1,
            'grand_total'                           => $this->resource->pricing->grand_total + $this->resource->pricing->shipping_cost,
            'discounted_total'                      => discountedTotal($this->resource->pricing),
            'discount'                              => $this->resource->pricing->discount,
            'discount_type'                         => $this->resource->pricing->discount_type,
            'advanced'                              => $this->resource->pricing->advanced,
            'due'                                   => (int)$this->resource->pricing->due,
            'shipping_cost'                         => (int)$this->resource->pricing->shipping_cost,
            'delivery_location'                     => Str::ucfirst(Str::replace('_', ' ', $this->resource->delivery_location)),
            'courier_note'                          => OrderHelper::getNote($this->resource->id, 'courier'),
            'invoice_note'                          => OrderHelper::getNote($this->resource->id, 'invoice'),
            'order_note'                            => OrderHelper::getNote($this->resource->id, 'order'),
            'courier_entry'                         => $this->resource->config->courier_entry === true,
            'tracking_code'                         => $this->resource->courier->tracking_code,
            'consignment_id'                        => $this->resource->courier->consignment_id,
            'courier_status'                        => Courier::status($this->resource->courier->status) ?? $this->resource->courier->status,
            'courier_provider'                      => $this->resource->courier->provider,
            $this->resource->order_status . '_date' => OrderHelper::getOrderDate($this->resource->id, $this->resource->order_status),
            'created_at'    => $this->resource->created_at,
            'updated_at'    => $this->resource->updated_at,
            'order_details' => OrderDetailsResource::collection($this->resource->order_details),
            'order_attach_images' => $this->resource->order_attach_images,
            'fraud_info'    => $frauds_report
        ];
    }
}