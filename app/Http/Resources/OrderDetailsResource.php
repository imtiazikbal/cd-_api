<?php

namespace App\Http\Resources;

use App\Helpers\OrderHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $discounted_price = $this->product?->id ? OrderHelper::getProductDiscountCalculation($this->product->id) : 0;

        return [
            'id'            => $this->id,
            'product_id'    => optional($this->product)->id,
            'product'       => optional($this->product)->product_name,
            'product_code'  => optional($this->product)->product_code,
            'discount'      => optional($this->product)->discount,
            'discount_type' => optional($this->product)->discount_type,
            'media'         => $this->product?->main_image,
            'price'         => $discounted_price ?? null,
            'quantity'      => $this->product_qty,
            'shipping_cost' => $this->shipping_cost,
            'variant'       => $this->variant,
            'variations'    => $this->variation,
        ];
    }
}
