<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    use HasUuid;

    protected $guarded = [];

    public const FILEPATH = 'media/support-ticket/';

    public function getPathAttribute($value): ?string
    {
        return $value ? asset($value) : null;
    }
}
