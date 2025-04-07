<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PasswordUpdateResource extends JsonResource
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
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'address'        => $this->address,
            'order_number'   => $this->transactions[0]->invoice_num,
            'payment_method' => $this->transactions[0]->gateway,
            'payment_status' => $this->transactions[0]->status,
            'type'           => $this->transactions[0]->type,
            'total'          => $this->transactions[0]->amount,
            'transaction_id' => $this->transactions[0]->id,
            "currency"       => "BDT",
        ];
    }
}
