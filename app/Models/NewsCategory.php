<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\LogsActivity;
use Database\Factories\NewsCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'sort_order'])]
class NewsCategory extends Model
{
    /** @use HasFactory<NewsCategoryFactory> */
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
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

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
