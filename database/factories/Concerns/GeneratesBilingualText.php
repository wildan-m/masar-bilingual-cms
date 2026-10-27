<?php

namespace Database\Factories\Concerns;

use App\Support\Localization\LocalizedSlug;

trait GeneratesBilingualText
{
    /**
     * @var list<string>
     */
    private static array $arabicWords = [
        'مشروع', 'تطوير', 'هندسة', 'تصميم', 'منشأة', 'برج', 'جسر', 'شبكة', 'محطة', 'مجمع',
        'سكني', 'تجاري', 'طريق', 'مياه', 'طاقة', 'استدامة', 'إشراف', 'تنفيذ', 'دراسة', 'تحديث',
    ];

    /**
     * A short English and Arabic title pair.
     *
     * @return array{en: string, ar: string}
     */
    protected function bilingualTitle(): array
    {
        return [
            'en' => rtrim(fake()->unique()->sentence(3), '.'),
            'ar' => implode(' ', fake()->randomElements(self::$arabicWords, 3)).' '.fake()->unique()->numberBetween(1, 99999),
        ];
    }

    /**
     * @return array{en: string, ar: string}
     */
    protected function bilingualParagraph(): array
    {
        return [
            'en' => fake()->paragraph(),
            'ar' => implode(' ', fake()->randomElements(self::$arabicWords, 12)).'.',
        ];
    }

    /**
     * @param  array<string, string>  $titles
     * @return array<string, string>
     */
    protected function slugsFor(array $titles): array
    {
        return array_map(
            fn (string $locale): string => LocalizedSlug::make($titles[$locale], $locale),
            array_combine(array_keys($titles), array_keys($titles)),
        );
    }
}
