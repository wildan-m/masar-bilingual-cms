<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\LogsActivity;
use Database\Factories\SectorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Market sector used to group projects and to qualify incoming RFPs.
 */
#[Fillable(['name', 'slug', 'sort_order'])]
class Sector extends Model
{
    /** @use HasFactory<SectorFactory> */
    use HasFactory, HasLocalizedAttributes, LogsActivity;

    /**
     * @var list<string>
     */
    protected array $localized = ['name', 'slug'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Find a sector by its slug in the locale, or in English as a fallback.
     */
    public static function findBySlug(?string $slug, string $locale): ?self
    {
        if (blank($slug)) {
            return null;
        }

        return static::query()
            ->where(fn (Builder $query) => $query
                ->where("slug->{$locale}", $slug)
                ->orWhere('slug->en', $slug))
            ->first();
    }
}
