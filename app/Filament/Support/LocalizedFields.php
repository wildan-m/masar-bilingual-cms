<?php

namespace App\Filament\Support;

use App\Support\Localization\Locale;
use App\Support\Localization\LocalizedSlug;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * Field factories for translatable attributes, used inside LocaleTabs.
 * English is required; other languages may be left empty and fall back.
 */
final class LocalizedFields
{
    /**
     * A title that fills the slug of the same language while it is empty.
     */
    public static function title(Locale $locale, string $name = 'title', string $label = 'Title', ?string $slug = 'slug'): TextInput
    {
        $field = TextInput::make("{$name}.{$locale->value}")
            ->label($label)
            ->required($locale->isFallback())
            ->maxLength(180);

        if ($slug === null) {
            return $field;
        }

        return $field
            ->live(onBlur: true)
            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) use ($locale, $slug): void {
                $path = "{$slug}.{$locale->value}";

                if (blank($get($path))) {
                    $set($path, LocalizedSlug::make($state, $locale));
                }
            });
    }

    /**
     * A URL slug, unique per language. The prefix shows where it is used.
     */
    public static function slug(Locale $locale, string $prefix, string $name = 'slug'): TextInput
    {
        return TextInput::make("{$name}.{$locale->value}")
            ->label('URL slug')
            ->prefix("/{$locale->value}/".ltrim($prefix, '/'))
            ->required($locale->isFallback())
            ->maxLength(180)
            ->regex(LocalizedSlug::PATTERN)
            ->scopedUnique(column: "{$name}->{$locale->value}", ignoreRecord: true)
            ->validationMessages(['regex' => 'Use letters, numbers and single hyphens only.'])
            ->helperText($locale->isFallback()
                ? 'Lowercase letters, numbers and hyphens. Changing it changes the page address.'
                : 'Letters in this language, numbers and hyphens. Leave empty to reuse the English slug.');
    }

    public static function textarea(Locale $locale, string $name, string $label, int $rows = 3, int $maxLength = 500): Textarea
    {
        return Textarea::make("{$name}.{$locale->value}")
            ->label($label)
            ->rows($rows)
            ->maxLength($maxLength);
    }

    public static function richText(Locale $locale, string $name = 'body', string $label = 'Body'): RichEditor
    {
        return RichEditor::make("{$name}.{$locale->value}")
            ->label($label)
            ->toolbarButtons([
                ['bold', 'italic', 'link'],
                ['h2', 'h3'],
                ['bulletList', 'orderedList', 'blockquote'],
                ['undo', 'redo'],
            ]);
    }

    /**
     * Per-language meta title and description, in a collapsed section.
     */
    public static function seoSection(): Section
    {
        return Section::make('Search engines and social sharing')
            ->description('Optional. When empty, the title and summary of the same language are used.')
            ->collapsed()
            ->columnSpanFull()
            ->schema([
                LocaleTabs::make(fn (Locale $locale): array => [
                    TextInput::make("meta_title.{$locale->value}")
                        ->label('Meta title')
                        ->maxLength(70),
                    Textarea::make("meta_description.{$locale->value}")
                        ->label('Meta description')
                        ->rows(2)
                        ->maxLength(160),
                ], 'Metadata', flagMissing: false),
            ]);
    }
}
