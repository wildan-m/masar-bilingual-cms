<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * A `cover_image` path on the public disk. Replaced and orphaned files are
 * removed so the disk does not accumulate unused uploads.
 */
trait HasCoverImage
{
    public static function bootHasCoverImage(): void
    {
        static::updated(function (Model $model): void {
            $previous = $model->getOriginal('cover_image');

            if ($model->wasChanged('cover_image') && filled($previous)) {
                Storage::disk(static::coverImageDisk())->delete($previous);
            }
        });

        static::deleted(function (Model $model): void {
            if (filled($model->cover_image)) {
                Storage::disk(static::coverImageDisk())->delete($model->cover_image);
            }
        });
    }

    public static function coverImageDisk(): string
    {
        return 'public';
    }

    public function coverImageUrl(): ?string
    {
        return filled($this->cover_image)
            ? Storage::disk(static::coverImageDisk())->url($this->cover_image)
            : null;
    }
}
