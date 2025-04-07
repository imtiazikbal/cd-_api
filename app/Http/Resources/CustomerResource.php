<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id"        => $this->id,
            "name"      => $this->name,
            "email"     => $this->email,
            "phone"     => $this->phone,
            "role"      => $this->role,
            "status"    => $this->status,
            "avatar"    => $this->avatar,
            'address'   => $this->customer_info->address,
            'user_type' => $this->customer_info->type
        ];
    }
}
