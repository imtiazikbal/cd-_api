<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Class Category
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $shop_id
 * @property int $parent_id
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const FILEPATH = 'media/category/';

    /**
     * Main thumbnail Image for the category
     *
     * @return MorphOne
     */
    public function category_image(): MorphOne
    {
        return $this->morphOne(Media::class, 'parent');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subcategory(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
