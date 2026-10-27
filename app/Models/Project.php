<?php

namespace App\Models;

use App\Enums\ClientType;
use App\Models\Concerns\HasCoverImage;
use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use App\Models\Concerns\HasSeoFields;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Publishable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A portfolio project.
 */
#[Fillable([
    'title', 'slug', 'location', 'summary', 'body', 'meta_title', 'meta_description',
    'sector_id', 'client_type', 'completion_year', 'cover_image', 'is_featured', 'is_published',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasCoverImage, HasFactory, HasLocalizedAttributes, HasLocalizedSlug, HasSeoFields, LogsActivity, Publishable;

    /**
     * @var list<string>
     */
    protected array $localized = ['title', 'slug', 'location', 'summary', 'body', 'meta_title', 'meta_description'];

    /**
     * @var list<string>
     */
    protected array $requiredTranslations = ['title', 'slug', 'location', 'summary', 'body'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'client_type' => ClientType::class,
            'completion_year' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public static function publicRouteName(): string
    {
        return 'site.projects.show';
    }

    protected function seoTitleAttribute(): string
    {
        return 'title';
    }

    protected function seoSummaryAttribute(): string
    {
        return 'summary';
    }

    /**
     * @return BelongsTo<Sector, $this>
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * @return BelongsToMany<Discipline, $this>
     */
    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class)->withTimestamps();
    }

    public function isOngoing(): bool
    {
        return $this->completion_year === null || $this->completion_year > now()->year;
    }

    /**
     * Most recent first; ongoing projects lead the list.
     */
    #[Scope]
    protected function latestFirst(Builder $query): void
    {
        $query->orderByRaw('completion_year is null desc')
            ->orderByDesc('completion_year')
            ->orderByDesc('id');
    }
}
