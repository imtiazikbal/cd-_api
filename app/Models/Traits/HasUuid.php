<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            data_set($model, 'uuid', Str::uuid()->toString());
        });
    }
}
