<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MyAddons extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'my_addons';

    public function addons(): HasMany
    {
        return $this->hasMany(Addons::class)->with('addons_image');
    }

    public function addons_image_details(): HasOne
    {
        return $this->hasOne(Media::class, 'parent_id', 'addons_id')->where('type', 'addons');
    }

    public function addons_details(): HasOne
    {
        return $this->hasOne(Addons::class, 'id', 'addons_id');
    }
}
