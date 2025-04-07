<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $order_status
 * @property string $order_type
 * @property string $order_no
 * @property string $shop_id
 * @property string $customer_name
 * @property string $phone
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property array $order_details
 * @property string $delivery_location
 * @property object $pricing
 * @property object $config
 * @property object $courier
 * @property bool $cod
 */
class Order extends Model
{
    use HasFactory;

    use Notifiable;

    use SoftDeletes;

    protected $guarded = [];

    public const PENDING = 'pending';

    public const FOLLOWUP = 'follow_up';

    public const CANCELLED = 'cancelled';

    public const CONFIRMED = 'confirmed';

    public const RETURNED = 'returned';

    public const SHIPPED = 'shipped';

    public const DELIVERED = 'delivered';

    public const HOLDON = 'hold_on';
    public const TRASH = 'trash';
    
    public const AMOUNT = 'amount';

    public const PERCENT = 'percent';

    public const UNVERIFIED = 'unverified';
    public const ALL = 'all'; // this is for all order get.
    public const ALLFILTER = 'all_order_filter'; // this for custom filter from  all order.

    public const Zero = '0%';

    public const Negative = '-100%';
    public const ORDERATTACHIMAGEPATH = 'media/order-attach-img/';

    /**
     * @param QueryBuilder $query
     * @param string $shop_id
     * @param string $start_date
     * @param string $end_date
     * @return mixed
     */
    public function scopeShopWiseOrderCount(QueryBuilder $query, string $shop_id, string $start_date, $end_date): mixed
    {
        if($end_date == "") {
            $end_date = Carbon::now()->toDateString();
        }

        if (Carbon::today() >= Carbon::parse($end_date)) {
            return $query->where('shop_id', $shop_id)
                ->whereBetween('created_at', [$end_date, Carbon::now()])
                ->withTrashed()
                ->count();
        }

        if(Carbon::today() <= Carbon::parse($end_date)) {
            return $query->where('shop_id', $shop_id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->withTrashed()
                ->count();
        }
    }

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetails::class)->with('product', 'variation');
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(OrderPricing::class);
    }

    public function courier(): HasOne
    {
        return $this->hasOne(OrderCourier::class);
    }

    public function config(): HasOne
    {
        return $this->hasOne(OrderConfig::class);
    }

    public function status(): HasMany
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function order_dates(): HasMany
    {
        return $this->hasMany(OrderDate::class);
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'shop_id', 'shop_id');
    }

    public function order_attach_images(): HasMany
    {
        return $this->hasMany(Media::class, 'parent_id', 'id',)->where('type', 'order_attach_img');
    }
}