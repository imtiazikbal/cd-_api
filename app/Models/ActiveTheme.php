<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveTheme extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'theme_id', 'parent_id')->where('type', 'template');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function Footer(): BelongsTo
    {
        return $this->belongsTo(Footer::class, 'footer_id', 'id');
    }

    public function Checkout(): BelongsTo
    {
        return $this->belongsTo(CheckFormDesign::class, 'checkout_form_id', 'id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'shop_id', 'shop_id');
    }
}
