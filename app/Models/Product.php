<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    use Notifiable;

    protected $guarded = [];

    public const FREE = 'free';

    public const PAID = 'paid';

    public const FLAT = 'flat';

    public const PERCENT = 'percent';

    public const PRODUCTIMAGEPATH = 'media/product/';

    public const PRODUCTGALLERYIMAGEPATH = 'media/product-gallery-image/';

    public const PRODUCTVARIATIONIMAGEPATH = 'media/product-variation-image/';

    public function main_image(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent')
            ->where('type', 'product_main_image');
    }

    public function other_images(): MorphMany
    {
        return $this->morphMany(Media::class, 'parent')
            ->where('type', 'product_gallery_image');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}