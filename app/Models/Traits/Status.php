<?php

namespace App\Models\Traits;

class Status
{
    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public static function listStatus(): array
    {
        return [
            self::STATUS_ACTIVE   => 'active',
            self::STATUS_BLOCKED  => 'blocked',
            self::STATUS_INACTIVE => 'inactive',
        ];

    }
}
