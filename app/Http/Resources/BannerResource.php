<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 *  @property Banner $resource
 */
class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $imagePath = $this->resource->image;
        $s3URL = substr($imagePath, 0, 30);

        if($s3URL == 'https://cdn-s3.funnelliner.com') {
            $image = $this->resource->image;
        } else {
            $image = env('AWS_URL') . $this->resource->image;
        }

        return [
            "id"      => $this->resource->id,
            "link"    => $this->resource->link,
            "user_id" => $this->resource->user_id,
            "shop_id" => $this->resource->shop_id,
            "image"   => $image . '?v=' . $this->resource->updated_at->getTimeStamp(),
        ];
    }
}
