<?php

namespace App\Models;

use App\Enums\PageTemplate;
use App\Models\Concerns\HasLocalizedAttributes;
use App\Models\Concerns\HasLocalizedSlug;
use App\Models\Concerns\HasSeoFields;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Publishable;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A free-form page (about, contact, privacy, ...).
 *
 * System pages carry a `key` so the navigation can link to them whatever
 * slug editors choose; they cannot be deleted.
 */
#[Fillable(['key', 'template', 'title', 'slug', 'body', 'meta_title', 'meta_description', 'is_published'])]
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory, HasLocalizedAttributes, HasLocalizedSlug, HasSeoFields, LogsActivity, Publishable;

    public const ABOUT = 'about';

    public const CONTACT = 'contact';

    public const PRIVACY = 'privacy';

    /**
     * @var list<string>
     */
    protected array $localized = ['title', 'slug', 'body', 'meta_title', 'meta_description'];

    /**
     * @var list<string>
     */
    protected array $requiredTranslations = ['title', 'slug', 'body'];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'template' => PageTemplate::Standard->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'template' => PageTemplate::class,
            'is_published' => 'boolean',
        ];
    }

    public static function publicRouteName(): string
    {
        return 'site.pages.show';
    }

    protected function seoTitleAttribute(): string
    {
        return 'title';
    }

    protected function seoSummaryAttribute(): string
    {
        return 'body';
    }

    public function isSystemPage(): bool
    {
        return filled($this->key);
    }

    public static function findByKey(string $key): ?self
    {
        return static::query()->published()->where('key', $key)->first();
    }
}
