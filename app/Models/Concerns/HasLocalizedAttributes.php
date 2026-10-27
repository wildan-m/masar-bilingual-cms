<?php

namespace App\Models\Concerns;

use App\Casts\AsLocalizedText;
use App\Support\Localization\Locale;
use App\Support\Localization\LocalizedText;
use App\Support\Localization\TranslationCompleteness;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

/**
 * Adds bilingual attributes to a model.
 *
 * The model lists its translatable attributes in a `$localized` property and
 * may narrow the ones that must be translated for the record to count as
 * complete with `$requiredTranslations` (SEO fields are usually optional
 * because they fall back to the title and summary).
 *
 * Every listed attribute is cast to a LocalizedText object, so
 * `$project->title` prints the current locale (with fallback) while forms and
 * APIs see the full {"en": ..., "ar": ...} structure.
 */
trait HasLocalizedAttributes
{
    public function initializeHasLocalizedAttributes(): void
    {
        $this->mergeCasts(array_fill_keys($this->localizedAttributes(), AsLocalizedText::class));
    }

    /**
     * @return list<string>
     */
    public function localizedAttributes(): array
    {
        return property_exists($this, 'localized') ? $this->localized : [];
    }

    /**
     * @return list<string>
     */
    public function requiredTranslations(): array
    {
        return property_exists($this, 'requiredTranslations')
            ? $this->requiredTranslations
            : $this->localizedAttributes();
    }

    public function localizedText(string $attribute): LocalizedText
    {
        if (! in_array($attribute, $this->localizedAttributes(), true)) {
            throw new InvalidArgumentException(sprintf('[%s] is not a localized attribute of [%s].', $attribute, static::class));
        }

        return $this->getAttribute($attribute) ?? new LocalizedText;
    }

    /**
     * The attribute in the given (or current) locale, falling back to English.
     */
    public function translate(string $attribute, Locale|string|null $locale = null): string
    {
        return $this->localizedText($attribute)->get($locale);
    }

    public function translationCompleteness(Locale|string $locale = Locale::Arabic): TranslationCompleteness
    {
        $locale = Locale::resolve($locale);
        $required = $this->requiredTranslations();

        $missing = array_values(array_filter(
            $required,
            fn (string $attribute): bool => ! $this->localizedText($attribute)->has($locale),
        ));

        return new TranslationCompleteness($locale, count($required), $missing);
    }

    /**
     * Records where at least one required attribute lacks the locale.
     */
    #[Scope]
    protected function missingTranslation(Builder $query, Locale|string $locale = Locale::Arabic): void
    {
        $code = Locale::resolve($locale)->value;

        $query->where(function (Builder $query) use ($code): void {
            foreach ($this->requiredTranslations() as $attribute) {
                $query->orWhereNull("{$attribute}->{$code}");
            }
        });
    }

    #[Scope]
    protected function fullyTranslated(Builder $query, Locale|string $locale = Locale::Arabic): void
    {
        $code = Locale::resolve($locale)->value;

        foreach ($this->requiredTranslations() as $attribute) {
            $query->whereNotNull("{$attribute}->{$code}");
        }
    }
}
