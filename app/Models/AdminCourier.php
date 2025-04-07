<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AdminCourier extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the decrypted password attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getPasswordAttribute($value)
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            if (app()->environment('local')) {
                Log::error($e->getMessage());
            }
            return null;
        }
    }

    /**
     * Get the config attribute as object.
     *
     * @param  string|null  $value
     * @return object|null
     */
    public function getConfigAttribute($value)
    {
        if ($value === null) {
            return null;
        }

        return \json_decode($value);
    }
}