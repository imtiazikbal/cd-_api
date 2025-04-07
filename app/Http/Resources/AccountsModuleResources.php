<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountsModuleResources extends JsonResource
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
            "amount"       => $this->amount,
            "ledger_id"    => $this->ledger_id,
            "ledgerName"   => $this->ledger->name ?? null,
            "payor_id"     => $this->payor_id,
            "payorName"    => $this->payor->name ?? null,
            "payment_id"   => $this->payment_id,
            "payment_type" => $this->payment->name ?? null,
            "description"  => $this->description,
            "date"         => $this->date,
            "time"         => $this->time,
            "status"       => $this->status,
            "bill_no"      => $this->bill_no,
            "created_at"   => $this->created_at,
            "balance"      => $this->balance,
        ];
    }
}
