<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Slider extends Model
{
    use HasFactory;

    public const SLIDERIMAGEPATH = '/media/slider/';

    public function slider_image(): MorphMany
    {
        return $this->morphMany(Media::class, 'parent')->where('type', 'merchant_slider');
    }
}
