<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FraudNoteResource extends JsonResource
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
            'id' => $this->resource->id,
            'phone' => $this->mask_number($this->resource->phone),
            'name' => $this->resource->name,
            'note' => $this->resource->note,
            'mark_at' => $this->resource->mark_at ?? $this->resource->created_at,
        ];
    }

    private function mask_number(string $number = null)
    {
        if ($number && \strlen($number) > 10) {
            $number = substr($number, 0, 4) . '***' . substr($number, -4);
        }
        return $number;
    }
}
