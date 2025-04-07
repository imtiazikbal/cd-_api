<?php

namespace App\Http\Resources;

use App\Models\OtherScript;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class OtherScriptResource
 * @package App\Http\Resources
 * @property OtherScript $resource
 */

class OtherScriptResource extends JsonResource
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
            'id'               => $this->resource->id,
            'gtm_head'         => $this->resource->gtm_head,
            'gtm_body'         => $this->resource->gtm_body,
            'google_analytics' => $this->resource->google_analytics,
        ];
    }
}
