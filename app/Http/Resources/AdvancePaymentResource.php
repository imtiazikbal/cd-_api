<?php

namespace App\Http\Resources;

use App\Models\WebsiteSetting;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property WebsiteSetting $resource
 */
class AdvancePaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'advanced_payment' => $this->resource->advanced_payment === 1,
        ];
    }
}
