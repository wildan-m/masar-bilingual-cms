<?php

namespace App\Filament\Support;

use App\Support\Localization\Locale;
use Closure;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Renders one tab per language for a model's translatable fields.
 *
 * The caller builds the fields of a single locale, naming them
 * "{attribute}.{locale}" (such as "title.ar"); the helper then:
 *  - sets dir/lang on every input, textarea and rich editor, so Arabic is
 *    typed right-to-left;
 *  - flags fields of non-fallback locales that are still untranslated on the
 *    saved record, telling editors that visitors see English instead;
 *  - shows the number of missing required translations on the tab itself.
 */
final class LocaleTabs
{
    /**
     * @param  Closure(Locale): array<Component>  $fields
     * @param  bool  $flagMissing  Mark untranslated fields and count them on the tab.
     */
    public static function make(Closure $fields, string $label = 'Content', bool $flagMissing = true): Tabs
    {
        $tabs = array_map(
            fn (Locale $locale): Tab => self::tab($locale, $fields($locale), $flagMissing, Str::slug($label)),
            Locale::cases(),
        );

        return Tabs::make($label)
            ->tabs($tabs)
            ->columnSpanFull();
    }

    /**
     * @param  array<Component>  $components
     */
    private static function tab(Locale $locale, array $components, bool $flagMissing, string $group): Tab
    {
        foreach ($components as $component) {
            self::localize($component, $locale, $flagMissing);
        }

        $tab = Tab::make($locale->isFallback() ? $locale->englishName() : "{$locale->englishName()} · {$locale->nativeName()}")
            ->key("{$group}-{$locale->value}")
            ->schema($components);

        if ($flagMissing && ! $locale->isFallback()) {
            $tab->badge(fn (?Model $record): ?int => self::missingTranslations($record, $locale) ?: null)
                ->badgeColor('warning')
                ->badgeTooltip('Required fields without a translation');
        }

        return $tab;
    }

    private static function localize(Component $component, Locale $locale, bool $flagMissing): void
    {
        if (! $component instanceof Field) {
            return;
        }

        if (method_exists($component, 'extraInputAttributes')) {
            $component->extraInputAttributes(['dir' => $locale->direction(), 'lang' => $locale->value], merge: true);
        }

        if (! $flagMissing || $locale->isFallback()) {
            return;
        }

        $attribute = Str::before($component->getName(), '.');

        $component
            ->hint(fn (?Model $record): ?string => self::isUntranslated($record, $attribute, $locale)
                ? 'Not translated: visitors see the English text'
                : null)
            ->hintIcon(fn (?Model $record): ?Heroicon => self::isUntranslated($record, $attribute, $locale)
                ? Heroicon::OutlinedExclamationTriangle
                : null)
            ->hintColor('warning');
    }

    private static function isUntranslated(?Model $record, string $attribute, Locale $locale): bool
    {
        if ($record === null || ! $record->exists || ! method_exists($record, 'localizedText')) {
            return false;
        }

        $text = $record->localizedText($attribute);

        return ! $text->isEmpty() && ! $text->has($locale);
    }

    private static function missingTranslations(?Model $record, Locale $locale): int
    {
        if ($record === null || ! $record->exists || ! method_exists($record, 'translationCompleteness')) {
            return 0;
        }

        return count($record->translationCompleteness($locale)->missing);
    }
}
