<?php

namespace App\Http\Resources;

use App\Helpers\OrderHelper;
use App\Models\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Product $resource
 */


class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $discounted_price = OrderHelper::getProductDiscountCalculation($this->id);

        if($this->discount_type === Product::FLAT) {
            $flat_discount_percent = ceil(100 * $this->discount / $this->price) . '%'; // flat rate discount percentage
        }

        return [
            "id"                        => $this->resource->id,
            "category_id"               => $this->resource->category_id,
            "shop_id"                   => $this->resource->shop_id,
            "product_name"              => $this->resource->product_name,
            "product_code"              => $this->resource->product_code,
            "product_qty"               => $this->resource->product_qty,
            "slug"                      => $this->resource->slug,
            "price"                     => $this->resource->price,
            "discount"                  => $this->resource->discount,
            "discounted_price"          => $discounted_price ?? 0,
            "discount_type"             => $this->resource->discount_type,
            "flat_discount_percent"     => $flat_discount_percent ?? '0%',
            "delivery_charge"           => $this->resource->delivery_charge,
            "inside_dhaka"              => $this->resource->inside_dhaka,
            "outside_dhaka"             => $this->resource->outside_dhaka,
            "short_description"         => $this->resource->short_description,
            "long_description"          => $this->resource->long_description,
            "status"                    => $this->resource->status,
            "sub_area_charge"           => $this->resource->sub_area_charge,
            "default_delivery_location" => $this->resource->default_delivery_location,
            "attributes"                => json_decode($this->resource->attributes),
            "variations"                => $this->resource->variations,
            "created_at"                => $this->resource->created_at,
            "main_image"                => $this->resource->main_image,
            "other_images"              => $this->resource->other_images,
            'relatedProducts'           => $this->resource->relatedProducts,
        ];
    }
}
