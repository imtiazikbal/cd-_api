<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class WebsiteSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function website_shop_logo(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent')
            ->where('type', 'website_shop_logo');
    }
}
