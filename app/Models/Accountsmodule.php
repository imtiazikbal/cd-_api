<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accountsmodule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(AccountLedger::class, 'ledger_id', 'id');
    }

    public function payor(): BelongsTo
    {
        return $this->belongsTo(AccountPayor::class, 'payor_id', 'id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_id', 'id');
    }
}
