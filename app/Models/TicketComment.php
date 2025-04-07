<?php

namespace App\Models;

use App\Models\Traits\HasPrimaryUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketComment extends Model
{
    use HasFactory;

    use HasPrimaryUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = ['id'];

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->toDayDateTimeString();
    }

    public function support_ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }
}
