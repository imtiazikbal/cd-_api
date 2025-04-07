<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * Class PageResource
 * @package App\Http\Resources
 * @property Page $resource
 */
class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"                         => $this->id,
            "user_id"                    => $this->user_id,
            "shop_id"                    => $this->shop_id,
            "product_id"                 => $this->product_id,
            "title"                      => $this->title,
            "slug"                       => $this->slug,
            "page_content"               => $this->page_content,
            "descriptions"               => $this->descriptions,
            "video_link"                 => $this->video_link,
            "status"                     => $this->status,
            "logo"                       => $this->logo,
            "fb"                         => $this->fb,
            "twitter"                    => $this->twitter,
            "linkedin"                   => $this->linkedin,
            "instagram"                  => $this->instagram,
            "youtube"                    => $this->youtube,
            "address"                    => $this->address,
            "phone"                      => $this->phone,
            "email"                      => $this->email,
            "footer_text_color"          => $this->footer_text_color,
            "footer_link_color"          => $this->footer_link_color,
            "footer_b_color"             => $this->footer_b_color,
            "footer_heading_color"       => $this->footer_heading_color,
            "checkout_text_color"        => $this->checkout_text_color,
            "checkout_link_color"        => $this->checkout_link_color,
            "checkout_b_color"           => $this->checkout_b_color,
            "checkout_button_color"      => $this->checkout_button_color,
            "checkout_button_text_color" => $this->checkout_button_text_color,
            "created_at"                 => $this->created_at,
            "updated_at"                 => $this->updated_at,
            "order_title"                => $this->order_title,
            "checkout_button_text"       => $this->checkout_button_text,
            "note"                       => $this->note,
            "footer"                     => $this->footer,
            "themes"                     => new ThemeResource($this->whenLoaded('themes')),
            "product"                    => new ProductDetailsResource($this->whenLoaded('product'))
        ];
    }
}
