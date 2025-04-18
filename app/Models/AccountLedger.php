<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountLedger extends Model
{
    use HasFactory;

    public const CASHIN = 'CashIn';

    public const CASHOUT = 'CashOut';

    protected $guarded = [];
}
