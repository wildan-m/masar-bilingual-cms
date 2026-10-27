<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use App\Models\Concerns\HasSeoFields;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Publishable;
use Database\Factories\DisciplineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * An engineering discipline offered by the firm (structural, MEP, ...).
 */
#[Fillable(['name', 'slug', 'summary', 'body', 'icon', 'sort_order', 'is_published', 'meta_title', 'meta_description'])]
class Discipline extends Model
{
    /** @use HasFactory<DisciplineFactory> */
    use HasFactory, HasLocalizedAttributes, HasLocalizedSlug, HasSeoFields, LogsActivity, Publishable;

    /**
     * Icons offered in the admin, keyed by Heroicon name.
     */
    public const ICONS = [
        'building-office-2' => 'Buildings',
        'bolt' => 'Power and MEP',
        'map' => 'Roads and infrastructure',
        'cube-transparent' => 'Structure',
        'globe-alt' => 'Geotechnics',
        'sun' => 'Sustainability',
        'clipboard-document-check' => 'Project management',
        'wrench-screwdriver' => 'Maintenance',
    ];

    /**
     * @var list<string>
     */
    protected array $localized = ['name', 'slug', 'summary', 'body', 'meta_title', 'meta_description'];

    /**
     * @var list<string>
     */
    protected array $requiredTranslations = ['name', 'slug', 'summary', 'body'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function publicRouteName(): string
    {
        return 'site.disciplines.show';
    }

    protected function seoTitleAttribute(): string
    {
        return 'name';
    }

    protected function seoSummaryAttribute(): string
    {
        return 'summary';
    }

    /**
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
