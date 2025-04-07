<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Addons extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const ADDONSIMAGEPATH = 'media/addons/';

    public const NOTFOUNDMSG = 'Addons Not Found';

    public function addons_image(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent')->where('type', 'addons');
    }
}
