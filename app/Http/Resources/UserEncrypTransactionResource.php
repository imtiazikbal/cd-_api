<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserEncrypTransactionResource extends JsonResource
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
            'id'                 => $this->id,
            'payment_method'     => $this->gateway,
            'invoice_num'        => $this->invoice_num,
            'amount'             => $this->amount,
            'transaction_status' => $this->status,
        ];
    }
}
