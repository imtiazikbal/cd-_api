<?php

/** @noinspection PhpUnused */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

/**
 * @property string $email
 * @property string $name
 * @property string $phone
 * @property string $status
 * @property string $payment_status
 */
class User extends Authenticatable
{
    use HasApiTokens;

    use HasFactory;

    use Notifiable;

    public const ADMIN = 'admin';

    public const MERCHANT = 'merchant';

    public const CUSTOMER = 'customer';

    public const STAFF = 'staff';

    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_EXPIRED = 'expired';

    public const PAID = 'paid';

    public const UNPAID = 'unpaid';

    public const TIMEZONE = 'Asia/Dhaka';

    public const ORDERSMS = [
        'cancelled' => '1',
        'confirmed' => '1',
        'shipped'   => '1',
        'returned'  => '1',
        'delivered' => '1',
        'pending'   => '1',
        'hold_on'   => '1',
    ];

    protected $guarded = [];

    protected $appends = ['avatar'];

    public static function listStatus(): array
    {
        return [
            self::STATUS_ACTIVE   => 'active',
            self::STATUS_BLOCKED  => 'blocked',
            self::STATUS_INACTIVE => 'inactive',
            self::STATUS_EXPIRED  => 'expired',
        ];

    }

    public static function listPaymentStatus(): array
    {
        return [
            self::PAID   => 'paid',
            self::UNPAID => 'unpaid',
        ];
    }

    public static function normalizePhone($phone): string
    {
        if(Str::startsWith($phone, "+88")) {
            return $phone;
        }

        return '+88' . $phone;
    }

    public static function removeCode($phone): string
    {
        if(Str::startsWith($phone, "+88")) {
            return Str::remove('+88', $phone);
        }

        return $phone;
    }

    /**
     * return password as a hash
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * @param $value
     * @return string
     */
    public function getAvatarAttribute($value): string
    {
        return $value ?: asset('images/profile.png');

    }

    /**
     * @param $value
     * @return string
     */
    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->timezone('Asia/Dhaka')->isoFormat('Do MMMM YYYY, h:mm a');
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class)->with('orders');
    }

    public function merchantinfo(): HasOne
    {
        return $this->hasOne(MerchantInfo::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'category_user');
    }

    public function support_ticket(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function customer_info(): HasOne
    {
        return $this->hasOne(CustomerInfo::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function merchant_tokens(): HasMany
    {
        return $this->hasMany(MerchantToken::class, 'user_id', 'id')->orderByDesc('id');
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
}