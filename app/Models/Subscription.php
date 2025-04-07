<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const PAID = 'Paid';

    public const UNPAID = 'Unpaid';

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->isoFormat('Do MMMM YYYY, h:mm a');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
