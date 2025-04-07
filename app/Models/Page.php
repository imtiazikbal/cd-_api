<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const DATANOTFOUND = 'Page not Found';

    protected $casts = [
        'user_id'    => 'integer',
        'shop_id'    => 'integer',
        'video_link' => 'string',
        'status'     => 'integer',
        'product_id' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($page) {
            $page->slug = Str::slug($page->title);
            $shopId = request()->header('shop-id');

            $originalSlug = $page->slug;
            $slugConflict = true;
            $attempts = 0;

            while ($slugConflict && $attempts < 10) {
                $existingPage = self::query()
                    ->where('shop_id', $shopId)
                    ->where('slug', $page->slug)
                    ->first();

                if (!$existingPage) {
                    $slugConflict = false;
                } else {
                    $page->slug = $originalSlug . '-' . Str::random(5);
                    $attempts++;
                }
            }
        });
    }

    public function themes(): HasOne
    {
        return $this->hasOne(Theme::class, 'id', 'theme');
    }

    public function page_reviews(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent')->where('type', 'page_reviews');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function Footer(): BelongsTo
    {
        return $this->belongsTo(ActiveTheme::class, 'id', 'page_id');
    }

    public function activeTheme(): HasOne
    {
        return $this->hasOne(ActiveTheme::class, 'page_id', 'id')->with('theme');
    }

    public function activeFooter(): BelongsTo
    {
        return $this->belongsTo(ActiveTheme::class, 'id', 'page_id')->with('Footer', 'Checkout');
    }

    public function getLogoAttribute($value): string
    {
        return env('AWS_URL') . '/' . $value;
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'shop_id', 'shop_id')->select('id', 'domain', 'domain_request', 'domain_status', 'shop_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->with('merchant_tokens');
    }
}
