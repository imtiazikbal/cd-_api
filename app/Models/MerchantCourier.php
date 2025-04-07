<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantCourier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STEADFAST = 'steadfast';

    public const REDX = 'redx';

    public const PATHAO = 'pathao';

    public const PAPERFLY = 'paperfly';

    public const ECOURIER = 'ecourier';
}
