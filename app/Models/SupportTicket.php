<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    use HasUuid;

    protected $guarded = [];

    public const OPENED = 'opened';

    public const PROCESSING = 'processing';

    public const SOLVED = 'solved';

    public const CLOSED = 'closed';

    public $appends = ['shop_id'];

    public static function listStatus(): array
    {
        return [
            self::OPENED     => 'opened',
            self::PROCESSING => 'processing',
            self::SOLVED     => 'solved',
            self::CLOSED     => 'closed'
        ];
    }

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->timezone('Asia/Dhaka')->isoFormat('Do MMMM YYYY, h:mm a');
    }

    public function getUpdatedAtAttribute($value): string
    {
        return Carbon::parse($value)->timezone('Asia/Dhaka')->isoFormat('Do MMMM YYYY, h:mm a');
    }

    public function getShopIdAttribute()
    {
        if ($this->relationLoaded('merchant')) {
            return Shop::query()->where('user_id', $this['user_id'])->pluck('shop_id')->first();
        }

        return null;
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id')->orderByDesc('created_at');
    }

    public function scopeMultiSearch($query, $request)
    {
        $query->where(function ($q) use ($request) {
            if ($request->date === 'today') {
                return $q->whereDate('created_at', Carbon::today());
            }

            if ($request->date === 'yesterday') {
                return $q->whereDate('created_at', Carbon::yesterday());
            }

            if ($request->date === 'weekly') {
                return $q->whereBetween('created_at', [Carbon::now()->startOfWeek(Carbon::SATURDAY), Carbon::now()->endOfWeek(Carbon::THURSDAY)]);
            }

            if ($request->date === 'monthly') {
                return $q->whereMonth('created_at', Carbon::now()->month);
            }

            if ($request->date === 'yearly') {
                return $q->whereYear('created_at', Carbon::now()->year);
            }

            if ($request->date === 'custom') {
                return $q->whereDate('created_at', '>=', Carbon::parse($request->start_date))
                    ->whereDate('created_at', '<=', Carbon::parse($request->end_date));
            }
        });
    }
}
