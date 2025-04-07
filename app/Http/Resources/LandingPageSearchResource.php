<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageSearchResource extends JsonResource
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
            'id'         => $this->id,
            'theme_name' => $this->theme_name,
            'type'       => $this->type,
            'name'       => $this->name,
            'url'        => $this->url,
            'status'     => $this->status,
            'media'      => $this->media,
        ];
    }
}
