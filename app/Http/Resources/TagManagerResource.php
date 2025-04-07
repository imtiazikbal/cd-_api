<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagManagerResource extends JsonResource
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
            'shop_id'      => $this->shop_id,
            'domain'       => $this->domain,
            'fb_pixel'     => $this->fb_pixel,
            'other_script' => $this->otherScript
        ];
    }
}
