<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class MerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'domain'         => $this->shop->domain,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'role'           => $this->role,
            'shop_id'        => $this->shop->shop_id,
            'avatar'         => $this->avatar,
            'status'         => $this->status,
            'payment_status' => $this->payment_status,
            'phone_verified' => $this->phone_verified_at !== null,
            'created_at'     => $this->created_at,
            'next_due_date'  => $this->next_due_date
        ];
    }
}
