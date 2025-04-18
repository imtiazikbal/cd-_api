<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ShopInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name'    => $this->name ?? null,
            'address' => $this->address ?? null,
            'phone'   => $this->phone ?? null,
            'email'   => $this->email ?? null,
        ];
    }
}
