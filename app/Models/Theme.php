<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Theme extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function media(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent')->where('type', 'template');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'id', 'theme');
    }
}
