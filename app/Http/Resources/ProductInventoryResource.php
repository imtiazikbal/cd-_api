<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductInventoryResource extends JsonResource
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
            "id"           => $this->id,
            "shop_id"      => $this->shop_id,
            "product_name" => $this->product_name,
            "product_code" => $this->product_code,
            "product_qty"  => $this->product_qty,
            "price"        => $this->price,
            "main_image"   => $this->main_image,
            "variations"   => $this->variations,
            "created_at"   => $this->created_at,
            "updated_at"   => $this->updated_at,
        ];
    }
}
