<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Class Media
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property string $parent_type
 * @property string $type
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Filesystem $disk
 */
class Media extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $_disk;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::deleting(function ($image) {
            $needed = static::query()->where('name', $image->name)
                ->where('id', '!=', $image->id)->exists();

            if (!$needed) {
                $image->disk->delete($image->name);
            }
        });
    }

    public function getDiskAttribute(): Filesystem|FilesystemAdapter
    {
        if (!$this->_disk) {
            $this->_disk = Storage::disk(config('filesystems.s3'));
        }

        return $this->_disk;
    }

    public function getLocalAttribute(): string
    {
        return asset(Str::replaceFirst('public', 'storage', $this->name));
    }

    public function getUrlAttribute(): string
    {
        if (filter_var($this->name, FILTER_VALIDATE_URL)) {
            return $this->name;
        }

        return $this->disk->url($this->name) . '?v=' . $this->updated_at->getTimestamp();
    }

    public function parent(): MorphTo
    {
        return $this->morphTo();
    }

    public function __toString(): string
    {
        return $this->url;
    }

    public function toArray(): array|string
    {
        return $this->url;
    }

    public static function upload(Model $parent, UploadedFile $file, string $path, string $type = null): static
    {
        $instance = new static();
        $name = $instance->disk->putFile($path, $file);

        $instance->fill([
            'name'        => $name,
            'parent_id'   => $parent->id,
            'parent_type' => get_class($parent),
            'type'        => $type
        ])->save();

        return $instance;
    }

    public function replaceWith(UploadedFile $image, string $path): bool
    {
        Cache::forget($this->parent_type . $this->parent_id);
        $this->disk->delete($this->name);
        $this->name = $this->disk->putFile($path, $image);

        return $this->save();
    }
}
