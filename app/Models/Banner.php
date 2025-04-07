<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Banner extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const BANNERIMAGEPATH = '/media/banner/';

    public function banner_image(): MorphMany
    {
        return $this->morphMany(Media::class, 'parent')->where('type', 'merchant_banner');
    }
}
