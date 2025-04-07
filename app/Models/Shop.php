<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Notifications\Notifiable;

/**
 * Class Shop
 * @package App\Models
 * @property string $shop_id
 */
class Shop extends Model
{
    use HasFactory;

    use Notifiable;

    protected $guarded = [];

    public const DOMAINNOTFOUND = 'Domain not found !';

    public function shop_logo(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent')->where('type', 'shop_logo');
    }

    public function shop_favicon(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent')->where('type', 'shop_favicon');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function banner(): HasMany
    {
        return $this->hasMany(Banner::class, 'shop_id', 'shop_id')
            ->orderBy('id', 'DESC')
            ->limit(3);
    }

    public function slider(): HasMany
    {
        return $this->hasMany(Slider::class, 'shop_id', 'shop_id')
            ->orderBy('id', 'DESC')
            ->limit(3);
    }

    public function addons_info(): HasMany
    {
        return $this->hasMany(MyAddons::class, 'shop_id', 'shop_id');
    }

    public function otherScript(): HasOne
    {
        return $this->hasOne(OtherScript::class, 'shop_id', 'shop_id')->select('shop_id', 'gtm_head', 'gtm_body', 'google_analytics');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shop_id', 'shop_id');
    }
}
