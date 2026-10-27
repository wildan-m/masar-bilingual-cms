<?php

namespace App\Models;

use App\Models\Concerns\HasCoverImage;
use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use App\Models\Concerns\HasSeoFields;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Publishable;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A news article.
 */
#[Fillable([
    'title', 'slug', 'excerpt', 'body', 'meta_title', 'meta_description',
    'news_category_id', 'author_id', 'cover_image', 'is_published', 'published_at',
])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasCoverImage, HasFactory, HasLocalizedAttributes, HasLocalizedSlug, HasSeoFields, LogsActivity, Publishable;

    /**
     * @var list<string>
     */
    protected array $localized = ['title', 'slug', 'excerpt', 'body', 'meta_title', 'meta_description'];

    /**
     * @var list<string>
     */
    protected array $requiredTranslations = ['title', 'slug', 'excerpt', 'body'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public static function publicRouteName(): string
    {
        return 'site.news.show';
    }

    protected function usesPublicationDate(): bool
    {
        return true;
    }

    protected function seoTitleAttribute(): string
    {
        return 'title';
    }

    protected function seoSummaryAttribute(): string
    {
        return 'excerpt';
    }

    /**
     * @return BelongsTo<NewsCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
